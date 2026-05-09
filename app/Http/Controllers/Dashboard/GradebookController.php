<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\GradeUnlockRequest;
use App\Models\SectionSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradebookController extends Controller
{
    private function activeQuarter(): ?GradingQuarter
    {
        $activeYear = AcademicYear::where('status', 'active')->first();
        if (!$activeYear) return null;

        return GradingQuarter::where('academic_year_id', $activeYear->id)
            ->where('status', 'active')
            ->first();
    }

    private function assertFacultyOwns(SectionSubject $ss): void
    {
        abort_unless(
            (int) $ss->faculty_id === auth()->id() &&
            SectionSubject::forActiveAcademicYear()->where('id', $ss->id)->exists(),
            403
        );
    }

    public function show(SectionSubject $sectionSubject): View
    {
        $ss = $sectionSubject->load(['section', 'subject']);
        $this->assertFacultyOwns($ss);

        $quarter = $this->activeQuarter();

        $enrollments = Enrollment::where('section_id', $ss->section_id)
            ->where('status', 'enrolled')
            ->with('student')
            ->orderBy('student_id')
            ->get();

        $grades = collect();
        if ($quarter && $enrollments->isNotEmpty()) {
            $grades = Grade::where('section_subject_id', $ss->id)
                ->where('grading_quarter_id', $quarter->id)
                ->whereIn('enrollment_id', $enrollments->pluck('id'))
                ->get()
                ->keyBy('enrollment_id');
        }

        $allDraft     = $grades->isNotEmpty() && $grades->every(fn($g) => $g->status === 'draft');
        $allSubmitted = $grades->isNotEmpty() && $grades->every(fn($g) => $g->status === 'submitted');
        $anyLocked    = $grades->some(fn($g) => $g->status === 'locked');
        $anyFinalized = $grades->some(fn($g) => in_array($g->status, ['finalized', 'locked']));

        return view('dashboard.faculty-gradebook-entry', compact(
            'ss', 'quarter', 'enrollments', 'grades',
            'allDraft', 'allSubmitted', 'anyLocked', 'anyFinalized'
        ));
    }

    public function saveDraft(Request $request, SectionSubject $sectionSubject): RedirectResponse
    {
        $ss = $sectionSubject->load(['section', 'subject']);
        $this->assertFacultyOwns($ss);

        $quarter = $this->activeQuarter();
        abort_unless($quarter, 422, 'No active grading quarter.');

        $request->validate([
            'grades'                        => 'nullable|array',
            'grades.*.written_work'         => 'nullable|numeric|min:0|max:100',
            'grades.*.performance_task'     => 'nullable|numeric|min:0|max:100',
            'grades.*.quarterly_assessment' => 'nullable|numeric|min:0|max:100',
        ]);

        $validEnrollmentIds = Enrollment::where('section_id', $ss->section_id)
            ->where('status', 'enrolled')
            ->pluck('id')
            ->flip();

        $saved = 0;
        foreach ((array) $request->input('grades', []) as $enrollmentId => $scores) {
            if (!$validEnrollmentIds->has((int) $enrollmentId)) continue;

            $existing = Grade::where('section_subject_id', $ss->id)
                ->where('grading_quarter_id', $quarter->id)
                ->where('enrollment_id', $enrollmentId)
                ->first();

            if ($existing && in_array($existing->status, ['finalized', 'locked'])) continue;

            $grade = $existing ?? new Grade([
                'section_subject_id' => $ss->id,
                'grading_quarter_id' => $quarter->id,
                'enrollment_id'      => (int) $enrollmentId,
                'status'             => 'draft',
            ]);

            $toFloat = fn($v) => isset($v) && $v !== '' ? (float) $v : null;
            $grade->fill([
                'written_work'         => $toFloat($scores['written_work'] ?? null),
                'performance_task'     => $toFloat($scores['performance_task'] ?? null),
                'quarterly_assessment' => $toFloat($scores['quarterly_assessment'] ?? null),
                'status'               => 'draft',
            ]);
            $grade->final_grade = $grade->computeFinalGrade();
            $grade->save();
            $saved++;
        }

        AuditLog::record(AuditLog::GRADE_DRAFT_SAVED, [
            'section_subject_id' => $ss->id,
            'quarter_id'         => $quarter->id,
            'rows_saved'         => $saved,
        ]);

        return redirect()->route('faculty.gradebook.show', $sectionSubject)
            ->with('success', "Draft saved for {$saved} student(s).");
    }

    public function submit(SectionSubject $sectionSubject): RedirectResponse
    {
        $ss = $sectionSubject->load(['section', 'subject']);
        $this->assertFacultyOwns($ss);

        $quarter = $this->activeQuarter();
        abort_unless($quarter, 422, 'No active grading quarter.');

        $draftGrades = Grade::where('section_subject_id', $ss->id)
            ->where('grading_quarter_id', $quarter->id)
            ->where('status', 'draft')
            ->whereNotNull('final_grade')
            ->get();

        abort_if($draftGrades->isEmpty(), 422, 'No complete draft grades to submit.');

        $now = now();
        Grade::whereIn('id', $draftGrades->pluck('id'))->update([
            'status'       => 'submitted',
            'submitted_at' => $now,
            'submitted_by' => auth()->id(),
        ]);

        AuditLog::record(AuditLog::GRADE_SUBMITTED, [
            'section_subject_id' => $ss->id,
            'quarter_id'         => $quarter->id,
            'grades_submitted'   => $draftGrades->count(),
        ]);

        return redirect()->route('faculty.gradebook.show', $sectionSubject)
            ->with('success', "{$draftGrades->count()} grade(s) submitted for registrar review.");
    }

    public function finalize(SectionSubject $sectionSubject): RedirectResponse
    {
        $ss = $sectionSubject->load(['section', 'subject']);
        $quarter = $this->activeQuarter();
        abort_unless($quarter, 422, 'No active grading quarter.');

        $submittedGrades = Grade::where('section_subject_id', $ss->id)
            ->where('grading_quarter_id', $quarter->id)
            ->where('status', 'submitted')
            ->get();

        abort_if($submittedGrades->isEmpty(), 422, 'No submitted grades to finalize.');

        $now = now();
        Grade::whereIn('id', $submittedGrades->pluck('id'))->update([
            'status'       => 'finalized',
            'finalized_at' => $now,
            'finalized_by' => auth()->id(),
        ]);

        AuditLog::record(AuditLog::GRADE_FINALIZED, [
            'section_subject_id' => $ss->id,
            'quarter_id'         => $quarter->id,
            'grades_finalized'   => $submittedGrades->count(),
        ]);

        return redirect()->back()
            ->with('success', "{$submittedGrades->count()} grade(s) finalized.");
    }

    public function requestUnlock(Request $request, SectionSubject $sectionSubject): RedirectResponse
    {
        $ss = $sectionSubject->load(['section', 'subject']);
        $this->assertFacultyOwns($ss);

        $quarter = $this->activeQuarter();
        abort_unless($quarter, 422, 'No active grading quarter.');

        $request->validate([
            'reason' => 'required|string|min:10|max:1000',
        ]);

        // Prevent duplicate pending requests for the same section+quarter
        $alreadyPending = GradeUnlockRequest::where('section_subject_id', $ss->id)
            ->where('grading_quarter_id', $quarter->id)
            ->pending()
            ->exists();

        if ($alreadyPending) {
            return redirect()->route('faculty.gradebook.show', $sectionSubject)
                ->with('error', 'You already have a pending unlock request for this class.');
        }

        GradeUnlockRequest::create([
            'section_subject_id' => $ss->id,
            'grading_quarter_id' => $quarter->id,
            'requested_by'       => auth()->id(),
            'reason'             => $request->input('reason'),
            'status'             => 'pending',
        ]);

        AuditLog::record(AuditLog::GRADE_UNLOCK_REQUESTED, [
            'section_subject_id' => $ss->id,
            'quarter_id'         => $quarter->id,
        ]);

        return redirect()->route('faculty.gradebook.show', $sectionSubject)
            ->with('success', 'Unlock request submitted. The registrar will review it shortly.');
    }
}
