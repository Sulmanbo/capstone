<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeComplaint;
use App\Models\GradingQuarter;
use App\Models\SectionSubject;
use App\Models\User;
use App\Notifications\ComplaintReceivedNotification;
use App\Notifications\ComplaintRespondedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeComplaintController extends Controller
{
    // ── Student: complaint submission form ──────────────────────────────────

    public function create(Request $request): View
    {
        $user = auth()->user();

        $enrollment = Enrollment::where('student_id', $user->id)
            ->whereHas('academicYear', fn($q) => $q->where('status', 'active'))
            ->with(['section', 'academicYear'])
            ->first();

        $sectionSubjects = collect();
        $quarters        = collect();

        if ($enrollment) {
            $sectionSubjects = SectionSubject::forSection($enrollment->section_id)
                ->forActiveAcademicYear()
                ->with(['subject', 'faculty'])
                ->orderBy('start_time')
                ->get();

            $quarters = GradingQuarter::whereHas(
                'academicYear', fn($q) => $q->where('status', 'active')
            )->orderBy('quarter_number')->get();
        }

        // Preselect from query params (from "File Complaint" links on grade views)
        $preSection = $request->query('section_subject_id');
        $preQuarter = $request->query('quarter');

        return view('complaints.create', compact(
            'enrollment', 'sectionSubjects', 'quarters', 'preSection', 'preQuarter'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        abort_unless($user->role_id === '01', 403);

        $validated = $request->validate([
            'section_subject_id' => ['required', 'integer', 'exists:section_subjects,id'],
            'grading_quarter_id' => ['nullable', 'integer', 'exists:grading_quarters,id'],
            'reason'             => ['required', 'string', 'min:20', 'max:2000'],
        ]);

        // Verify enrollment
        $enrollment = Enrollment::where('student_id', $user->id)
            ->whereHas('academicYear', fn($q) => $q->where('status', 'active'))
            ->first();
        abort_unless($enrollment, 403, 'No active enrollment found.');

        // Verify the section_subject belongs to the student's section
        $ss = SectionSubject::where('id', $validated['section_subject_id'])
            ->where('section_id', $enrollment->section_id)
            ->first();
        abort_unless($ss, 403, 'You are not enrolled in that subject.');

        // Prevent duplicate open complaints for same subject + quarter
        $exists = GradeComplaint::where('student_id', $user->id)
            ->where('section_subject_id', $ss->id)
            ->when(
                !empty($validated['grading_quarter_id']),
                fn($q) => $q->where('grading_quarter_id', $validated['grading_quarter_id'])
            )
            ->whereIn('status', ['pending', 'under_review'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'section_subject_id' => 'You already have an open complaint for this subject and quarter.',
            ])->withInput();
        }

        // Find the associated grade record if it exists
        $grade = null;
        if (!empty($validated['grading_quarter_id'])) {
            $grade = Grade::where('enrollment_id', $enrollment->id)
                ->where('section_subject_id', $ss->id)
                ->where('grading_quarter_id', $validated['grading_quarter_id'])
                ->first();
        }

        $complaint = GradeComplaint::create([
            'student_id'         => $user->id,
            'section_subject_id' => $ss->id,
            'grading_quarter_id' => $validated['grading_quarter_id'] ?? null,
            'grade_id'           => $grade?->id,
            'reason'             => $validated['reason'],
            'status'             => 'pending',
        ]);

        AuditLog::record(AuditLog::GRADE_COMPLAINT_SUBMITTED, [
            'complaint_id'       => $complaint->id,
            'section_subject_id' => $ss->id,
            'subject'            => $ss->subject?->subject_name,
        ]);

        $ss->loadMissing('faculty');
        $complaintNotif = new ComplaintReceivedNotification(
            $user->full_name,
            $ss->subject?->subject_name ?? 'Unknown Subject',
            mb_substr($validated['reason'], 0, 80) . (mb_strlen($validated['reason']) > 80 ? '…' : ''),
        );
        if ($ss->faculty) {
            $ss->faculty->notify($complaintNotif);
        }
        User::where('role_id', '03')->each(fn($r) => $r->notify($complaintNotif));

        return redirect()->route('complaints.index')
            ->with('success', 'Your complaint has been submitted and will be reviewed.');
    }

    // ── Student: list own complaints ────────────────────────────────────────

    public function index(): View
    {
        $user = auth()->user();
        abort_unless($user->role_id === '01', 403);

        $complaints = GradeComplaint::forStudent($user->id)
            ->with(['sectionSubject.subject', 'gradingQuarter', 'respondedBy'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('complaints.index', compact('complaints'));
    }

    // ── Faculty / Registrar / Admin: management list ────────────────────────

    public function manage(): View
    {
        $user = auth()->user();
        abort_unless(in_array($user->role_id, ['02', '03', '04']), 403);

        $query = GradeComplaint::with([
            'student',
            'sectionSubject.subject',
            'sectionSubject.section',
            'gradingQuarter',
            'grade',
            'respondedBy',
        ])->orderBy('status')->orderByDesc('created_at');

        // Faculty only see complaints for their subjects
        if ($user->role_id === '02') {
            $query->whereHas('sectionSubject', fn($q) => $q->where('faculty_id', $user->id));
        }

        $complaints = $query->paginate(20);

        return view('complaints.manage', compact('complaints'));
    }

    // ── Faculty / Registrar / Admin: respond ───────────────────────────────

    public function respond(Request $request, GradeComplaint $complaint): RedirectResponse
    {
        $user = auth()->user();
        abort_unless(in_array($user->role_id, ['02', '03', '04']), 403);

        // Faculty restricted to their own subjects
        if ($user->role_id === '02') {
            abort_unless(
                $complaint->sectionSubject->faculty_id === $user->id,
                403, 'You are not the faculty for this subject.'
            );
        }

        $validated = $request->validate([
            'status'   => ['required', 'in:under_review,resolved,dismissed'],
            'response' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $complaint->update([
            'status'       => $validated['status'],
            'response'     => $validated['response'],
            'responded_by' => $user->id,
            'responded_at' => now(),
        ]);

        $actionType = $validated['status'] === 'dismissed'
            ? AuditLog::GRADE_COMPLAINT_DISMISSED
            : AuditLog::GRADE_COMPLAINT_RESPONDED;

        AuditLog::record($actionType, [
            'complaint_id' => $complaint->id,
            'new_status'   => $validated['status'],
            'student_id'   => $complaint->student_id,
        ]);

        $complaint->load(['student', 'sectionSubject.subject']);
        $complaint->student?->notify(new ComplaintRespondedNotification(
            $complaint->sectionSubject?->subject?->subject_name ?? 'Unknown Subject',
            $validated['status'],
            mb_substr($validated['response'], 0, 100) . (mb_strlen($validated['response']) > 100 ? '…' : ''),
        ));

        return back()->with('success', 'Response saved.');
    }
}
