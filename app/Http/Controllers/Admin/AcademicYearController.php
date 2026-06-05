<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\GradingQuarter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * AcademicYearController
 *
 * Manages academic years (e.g., 2025-2026).
 *
 * Per adviser feedback:
 *   - Term type is selectable per year: quarterly OR semestral.
 *   - Multiple years may be active simultaneously so the registrar can
 *     prepare next year's schedules while the current year is still running.
 *   - Status is toggleable independently from creation/edit.
 *   - A single academic year cannot span more than 12 months.
 *   - Saving a year auto-creates the matching grading periods:
 *       quarterly  → 4 quarters (1st, 2nd, 3rd, 4th Quarter)
 *       semestral  → 2 semesters (1st Semester, 2nd Semester)
 *     Dates default to the year's own start/end as placeholders; the
 *     registrar fine-tunes them later under /admin/quarters. Existing
 *     quarters are NEVER removed during reconciliation — only missing
 *     ones are added.
 *
 * All state changes are audited via AuditLog.
 */
class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = AcademicYear::query();

        if ($search) {
            $query->where('year_label', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $academicYears = $query->orderByDesc('start_date')->paginate(50);

        return view('admin.registrars.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.registrars.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $year = DB::transaction(function () use ($validated) {
            $year = AcademicYear::create($validated);
            $this->reconcileGradingPeriods($year);
            return $year;
        });

        AuditLog::record('ACADEMIC_YEAR_CREATED', [
            'year_id'    => $year->id,
            'year_label' => $year->year_label,
            'term_type'  => $year->term_type,
            'status'     => $year->status,
        ]);

        $periodsLabel = $year->term_type === 'semestral' ? '2 semesters' : '4 quarters';

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$year->year_label}' created. {$periodsLabel} were auto-created — set their dates under Grading Quarters.");
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.registrars.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $this->validatePayload($request, $academicYear->id);

        $before = $academicYear->only(['year_label','start_date','end_date','term_type','status']);

        DB::transaction(function () use ($academicYear, $validated) {
            $academicYear->update($validated);
            $this->reconcileGradingPeriods($academicYear);
        });

        AuditLog::record('ACADEMIC_YEAR_UPDATED', [
            'year_id' => $academicYear->id,
            'before'  => $before,
            'after'   => $academicYear->only(['year_label','start_date','end_date','term_type','status']),
        ]);

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$academicYear->year_label}' updated successfully.");
    }

    /**
     * PATCH /admin/academic-years/{academicYear}/toggle
     *
     * Dedicated endpoint for toggling active <-> inactive without going
     * through the full edit form. Multiple years may be active at once.
     */
    public function toggle(AcademicYear $academicYear)
    {
        $newStatus = $academicYear->status === 'active' ? 'inactive' : 'active';

        if ($academicYear->status === 'active' && !$academicYear->canBeDeactivated()) {
            return back()->withErrors([
                'status' => "Cannot deactivate '{$academicYear->year_label}' — it has active grading quarters. Close the quarters first.",
            ]);
        }

        $before = $academicYear->status;
        $academicYear->update(['status' => $newStatus]);

        AuditLog::record('ACADEMIC_YEAR_STATUS_TOGGLED', [
            'year_id'   => $academicYear->id,
            'year_label'=> $academicYear->year_label,
            'from'      => $before,
            'to'        => $newStatus,
        ]);

        return back()->with('success', "Academic Year '{$academicYear->year_label}' is now {$newStatus}.");
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->status === 'active') {
            return back()->withErrors(['status' => 'Cannot delete an active academic year. Deactivate it first.']);
        }

        if ($academicYear->quarters()->count() > 0) {
            return back()->withErrors(['grade_levels' => 'Cannot delete academic year with associated grading quarters.']);
        }

        $label = $academicYear->year_label;

        AuditLog::record('ACADEMIC_YEAR_DELETED', [
            'year_id'    => $academicYear->id,
            'year_label' => $label,
        ]);

        $academicYear->delete();

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "Academic Year '{$label}' deleted successfully.");
    }

    // ──────────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Validate create/update payload, including the 1-year-max cap.
     */
    private function validatePayload(Request $request, ?int $existingId = null): array
    {
        $uniqueRule = $existingId
            ? "unique:academic_years,year_label,{$existingId}"
            : 'unique:academic_years,year_label';

        $validated = $request->validate([
            'year_label' => ['required', 'string', 'max:50', $uniqueRule],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'term_type'  => ['required', Rule::in(['quarterly', 'semestral'])],
            'status'     => ['required', 'in:active,inactive,archived'],
        ]);

        // 1-year cap (per adviser feedback)
        $start = Carbon::parse($validated['start_date']);
        $end   = Carbon::parse($validated['end_date']);
        $maxEnd = $start->copy()->addYear();
        if ($end->greaterThan($maxEnd)) {
            throw ValidationException::withMessages([
                'end_date' => 'An academic year cannot last more than 1 year from its start date.',
            ]);
        }

        return $validated;
    }

    /**
     * Ensure the year has the right grading periods for its term_type.
     *
     * Non-destructive: only ADDS missing rows. Existing grading_quarters rows
     * are never deleted, because they may already have student grades attached
     * and removing them would corrupt academic records.
     *
     * If you switch a year from quarterly→semestral after creation, the old
     * 3rd/4th Quarter rows will still exist; the registrar can hide or
     * archive them manually under /admin/quarters.
     */
    private function reconcileGradingPeriods(AcademicYear $year): void
    {
        $template = $year->term_type === 'semestral'
            ? [
                ['number' => 1, 'name' => '1st Semester'],
                ['number' => 2, 'name' => '2nd Semester'],
            ]
            : [
                ['number' => 1, 'name' => '1st Quarter'],
                ['number' => 2, 'name' => '2nd Quarter'],
                ['number' => 3, 'name' => '3rd Quarter'],
                ['number' => 4, 'name' => '4th Quarter'],
            ];

        // Placeholder dates: use the year's own bounds. Registrar refines them
        // in the Grading Quarters admin page.
        $placeholderStart = $year->start_date;
        $placeholderEnd   = $year->end_date;

        foreach ($template as $tpl) {
            GradingQuarter::firstOrCreate(
                [
                    'academic_year_id' => $year->id,
                    'quarter_number'   => $tpl['number'],
                ],
                [
                    'quarter_name' => $tpl['name'],
                    'start_date'   => $placeholderStart,
                    'end_date'     => $placeholderEnd,
                    'status'       => 'inactive',
                ]
            );
        }
    }
}
