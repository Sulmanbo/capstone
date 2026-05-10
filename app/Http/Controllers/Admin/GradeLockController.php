<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\GradeUnlockRequest;
use App\Models\SectionSubject;
use App\Models\User;
use App\Notifications\GradeLockedNotification;
use App\Notifications\UnlockDecidedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeLockController extends Controller
{
    private function activeQuarter(): ?GradingQuarter
    {
        $activeYear = AcademicYear::where('status', 'active')->first();
        if (!$activeYear) return null;

        return GradingQuarter::where('academic_year_id', $activeYear->id)
            ->where('status', 'active')
            ->first();
    }

    public function index(): View
    {
        $activeYear    = AcademicYear::where('status', 'active')->first();
        $activeQuarter = $this->activeQuarter();

        // All section_subjects in active year, with their grade status summary
        $sectionSubjects = collect();
        if ($activeYear) {
            $sectionSubjects = SectionSubject::where('academic_year_id', $activeYear->id)
                ->with(['section', 'subject', 'faculty'])
                ->get()
                ->map(function (SectionSubject $ss) use ($activeQuarter) {
                    $gradeCounts = ['draft' => 0, 'submitted' => 0, 'finalized' => 0, 'locked' => 0];

                    if ($activeQuarter) {
                        $counts = Grade::where('section_subject_id', $ss->id)
                            ->where('grading_quarter_id', $activeQuarter->id)
                            ->selectRaw('status, COUNT(*) as cnt')
                            ->groupBy('status')
                            ->pluck('cnt', 'status');

                        foreach ($gradeCounts as $s => $_) {
                            $gradeCounts[$s] = $counts->get($s, 0);
                        }
                    }

                    $pendingUnlock = GradeUnlockRequest::where('section_subject_id', $ss->id)
                        ->when($activeQuarter, fn($q) => $q->where('grading_quarter_id', $activeQuarter->id))
                        ->pending()
                        ->exists();

                    $ss->grade_counts    = $gradeCounts;
                    $ss->pending_unlock  = $pendingUnlock;
                    return $ss;
                });
        }

        $pendingRequests = GradeUnlockRequest::pending()
            ->with(['sectionSubject.section', 'sectionSubject.subject', 'requestedBy', 'gradingQuarter'])
            ->latest()
            ->get();

        return view('admin.grade-lock.index', compact(
            'sectionSubjects', 'pendingRequests', 'activeYear', 'activeQuarter'
        ));
    }

    public function lockSection(Request $request, SectionSubject $sectionSubject): RedirectResponse
    {
        $quarter = $this->activeQuarter();
        abort_unless($quarter, 422, 'No active grading quarter.');

        $affected = Grade::where('section_subject_id', $sectionSubject->id)
            ->where('grading_quarter_id', $quarter->id)
            ->where('status', 'finalized')
            ->update(['status' => 'locked']);

        AuditLog::record(AuditLog::GRADE_LOCKED, [
            'section_subject_id' => $sectionSubject->id,
            'quarter_id'         => $quarter->id,
            'grades_locked'      => $affected,
        ]);

        if ($affected > 0) {
            $sectionSubject->loadMissing(['section', 'subject']);
            $lockNotif = new GradeLockedNotification(
                $sectionSubject->subject?->subject_name ?? 'Unknown Subject',
                $sectionSubject->section?->section_name ?? 'Unknown Section',
                'Quarter ' . $quarter->quarter_number,
            );
            User::whereHas('enrollments', fn($q) =>
                $q->where('section_id', $sectionSubject->section_id)->where('status', 'enrolled')
            )->each(fn($s) => $s->notify($lockNotif));
        }

        return redirect()->route('registrar.grade-lock.index')
            ->with('success', "{$affected} grade(s) locked for {$sectionSubject->subject?->subject_name}.");
    }

    public function lockAll(): RedirectResponse
    {
        $quarter = $this->activeQuarter();
        abort_unless($quarter, 422, 'No active grading quarter.');

        $affected = Grade::where('grading_quarter_id', $quarter->id)
            ->where('status', 'finalized')
            ->update(['status' => 'locked']);

        AuditLog::record(AuditLog::GRADE_LOCKED, [
            'quarter_id'    => $quarter->id,
            'grades_locked' => $affected,
            'scope'         => 'global',
        ]);

        return redirect()->route('registrar.grade-lock.index')
            ->with('success', "Global lock applied — {$affected} grade(s) locked.");
    }

    public function approveUnlock(GradeUnlockRequest $unlockRequest): RedirectResponse
    {
        abort_unless($unlockRequest->isPending(), 422, 'Request already reviewed.');

        $quarter = $unlockRequest->gradingQuarter;

        // Revert locked grades back to finalized so faculty can edit them again
        Grade::where('section_subject_id', $unlockRequest->section_subject_id)
            ->where('grading_quarter_id', $unlockRequest->grading_quarter_id)
            ->where('status', 'locked')
            ->update(['status' => 'finalized']);

        $unlockRequest->update([
            'status'       => 'approved',
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
        ]);

        AuditLog::record(AuditLog::GRADE_UNLOCK_APPROVED, [
            'unlock_request_id'  => $unlockRequest->id,
            'section_subject_id' => $unlockRequest->section_subject_id,
            'quarter_id'         => $unlockRequest->grading_quarter_id,
        ]);

        $unlockRequest->loadMissing(['sectionSubject.subject', 'sectionSubject.section', 'requestedBy']);
        $unlockRequest->requestedBy?->notify(new UnlockDecidedNotification(
            $unlockRequest->sectionSubject?->subject?->subject_name ?? 'Unknown Subject',
            $unlockRequest->sectionSubject?->section?->section_name ?? 'Unknown Section',
            'approved',
            null,
        ));

        return redirect()->route('registrar.grade-lock.index')
            ->with('success', 'Unlock approved — faculty can now edit grades.');
    }

    public function denyUnlock(Request $request, GradeUnlockRequest $unlockRequest): RedirectResponse
    {
        abort_unless($unlockRequest->isPending(), 422, 'Request already reviewed.');

        $request->validate([
            'review_notes' => 'nullable|string|max:500',
        ]);

        $unlockRequest->update([
            'status'       => 'denied',
            'reviewed_by'  => auth()->id(),
            'reviewed_at'  => now(),
            'review_notes' => $request->input('review_notes'),
        ]);

        AuditLog::record(AuditLog::GRADE_UNLOCK_DENIED, [
            'unlock_request_id'  => $unlockRequest->id,
            'section_subject_id' => $unlockRequest->section_subject_id,
        ]);

        $unlockRequest->loadMissing(['sectionSubject.subject', 'sectionSubject.section', 'requestedBy']);
        $unlockRequest->requestedBy?->notify(new UnlockDecidedNotification(
            $unlockRequest->sectionSubject?->subject?->subject_name ?? 'Unknown Subject',
            $unlockRequest->sectionSubject?->section?->section_name ?? 'Unknown Section',
            'denied',
            $request->input('review_notes'),
        ));

        return redirect()->route('registrar.grade-lock.index')
            ->with('success', 'Unlock request denied.');
    }
}
