<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Section CRUD.
 *
 * Sections are year-scoped per adviser feedback. Each section is one
 * class group (e.g. "Grade 7 — St. Therese") with an optional homeroom
 * adviser. Same section-name + grade-level cannot appear twice in the
 * same year (DB unique constraint enforces this).
 */
class SectionController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $yearId = $request->input('academic_year_id') ?? optional($academicYears->first())->id;

        $sections = Section::query()
            ->with(['academicYear', 'adviser'])
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->orderBy('grade_level')
            ->orderBy('section_name')
            ->paginate(20)
            ->withQueryString();

        $faculty = User::where('role_id', '02')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get();

        return view('admin.sections.index', compact('sections', 'academicYears', 'yearId', 'faculty'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'grade_level'      => ['required', 'string', 'max:20'],
            'section_name'     => [
                'required', 'string', 'max:100',
                Rule::unique('sections')->where(fn($q) => $q
                    ->where('academic_year_id', $request->academic_year_id)
                    ->where('grade_level', $request->grade_level)
                ),
            ],
            'adviser_id'       => ['nullable', 'exists:users,id'],
            'capacity'         => ['required', 'integer', 'min:1', 'max:200'],
            'status'           => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $section = Section::create($data);

        AuditLog::record('SECTION_CREATED', [
            'section_id'   => $section->id,
            'grade_level'  => $section->grade_level,
            'section_name' => $section->section_name,
            'year_id'      => $section->academic_year_id,
        ]);

        return back()->with('success', "Section '{$section->grade_level} — {$section->section_name}' created.");
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'grade_level'  => ['required', 'string', 'max:20'],
            'section_name' => [
                'required', 'string', 'max:100',
                Rule::unique('sections')
                    ->ignore($section->id)
                    ->where(fn($q) => $q
                        ->where('academic_year_id', $section->academic_year_id)
                        ->where('grade_level', $request->grade_level)
                    ),
            ],
            'adviser_id'   => ['nullable', 'exists:users,id'],
            'capacity'     => ['required', 'integer', 'min:1', 'max:200'],
            'status'       => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $before = $section->only(['grade_level', 'section_name', 'adviser_id', 'capacity', 'status']);
        $section->update($data);

        AuditLog::record('SECTION_UPDATED', [
            'section_id' => $section->id,
            'before'     => $before,
            'after'      => $section->only(['grade_level', 'section_name', 'adviser_id', 'capacity', 'status']),
        ]);

        return back()->with('success', 'Section updated.');
    }

    public function destroy(Section $section)
    {
        // Prevent deletion if section has enrollments or schedules
        if ($section->enrollments()->exists()) {
            return back()->withErrors([
                'section' => "Cannot delete '{$section->grade_level} — {$section->section_name}' — it has enrolled students. Deactivate it instead.",
            ]);
        }

        AuditLog::record('SECTION_DELETED', [
            'section_id'   => $section->id,
            'grade_level'  => $section->grade_level,
            'section_name' => $section->section_name,
        ]);

        $section->delete();

        return back()->with('success', 'Section removed.');
    }
}
