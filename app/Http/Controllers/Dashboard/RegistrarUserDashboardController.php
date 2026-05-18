<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Applicant;
use App\Models\Enrollment;
use App\Models\GradeComplaint;
use App\Models\GradeUnlockRequest;
use App\Models\GradingQuarter;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\PrerequisiteService;
use Illuminate\Http\Request;

/**
 * RegistrarUserDashboardController
 *
 * Dashboard for Registrar staff (role 03) who work in the registrar's office.
 * Shows academic calendar, live workload metrics, and pending actions.
 */
class RegistrarUserDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // ── Current Academic Information ───────────────────────────────────
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        $activeQuarter = null;
        if ($activeAcademicYear) {
            $activeQuarter = $activeAcademicYear->quarters()
                ->where('status', 'active')
                ->first();
        }

        // ── Recent Activities (Audit Log) ──────────────────────────────────
        $recentActivities = AuditLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // ── Live System Statistics ─────────────────────────────────────────
        $pendingUnlockCount   = GradeUnlockRequest::where('status', 'pending')->count();
        $pendingComplaintCount = GradeComplaint::where('status', 'pending')->count();
        $pendingTotal         = $pendingUnlockCount + $pendingComplaintCount;

        $completedTotal = GradeUnlockRequest::whereIn('status', ['approved', 'denied'])->count()
                        + GradeComplaint::whereIn('status', ['resolved', 'dismissed'])->count();

        $enrollmentCount = $activeAcademicYear
            ? Enrollment::where('academic_year_id', $activeAcademicYear->id)
                ->where('status', 'enrolled')
                ->count()
            : 0;

        $applicantsInReview = Applicant::whereIn('status', ['under_review', 'for_test', 'tested'])->count();

        $stats = [
            'active_academic_year'     => $activeAcademicYear,
            'active_quarter'           => $activeQuarter,
            'pending_requests'         => $pendingTotal,
            'completed_requests'       => $completedTotal,
            'enrollment_verifications' => $enrollmentCount,
            'documents_in_review'      => $applicantsInReview,
        ];

        // ── Pending Unlock Requests (real data) ────────────────────────────
        $unlockRequests = GradeUnlockRequest::with(['requestedBy', 'sectionSubject.section', 'sectionSubject.subject'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $pendingRequests = $unlockRequests->map(fn($req) => [
            'type'      => 'Grade Unlock Request',
            'student'   => optional($req->requestedBy)->full_name
                            ?? (optional($req->requestedBy)->first_name . ' ' . optional($req->requestedBy)->last_name),
            'status'    => 'Waiting Approval',
            'submitted' => $req->created_at->format('M d, Y'),
            'due'       => $req->created_at->addDay()->format('M d, Y'),
        ])->toArray();

        // Append pending grade complaints if there's still room
        if (count($pendingRequests) < 5) {
            $complaints = GradeComplaint::with(['student', 'sectionSubject.subject'])
                ->where('status', 'pending')
                ->orderByDesc('created_at')
                ->take(5 - count($pendingRequests))
                ->get();

            foreach ($complaints as $c) {
                $subjectName = optional(optional($c->sectionSubject)->subject)->title ?? 'Subject';
                $pendingRequests[] = [
                    'type'      => "Grade Complaint — {$subjectName}",
                    'student'   => optional($c->student)->first_name . ' ' . optional($c->student)->last_name,
                    'status'    => 'Under Review',
                    'submitted' => $c->created_at->format('M d, Y'),
                    'due'       => $c->created_at->addDays(3)->format('M d, Y'),
                ];
            }
        }

        // ── Deadlines: driven from high-priority announcements ─────────────
        $deadlineAnnouncements = Announcement::active()
            ->forRole('registrar')
            ->whereIn('priority', ['high', 'urgent'])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $deadlines = $deadlineAnnouncements->map(fn($ann) => [
            'title' => $ann->title,
            'date'  => $ann->created_at->format('M d, Y'),
            'note'  => $ann->message,
        ])->toArray();

        // ── Office Notices: medium/low announcements ───────────────────────
        $noticeAnnouncements = Announcement::active()
            ->forRole('registrar')
            ->whereNotIn('priority', ['high', 'urgent'])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $notices = $noticeAnnouncements->map(fn($ann) => [
            'message'  => $ann->message,
            'priority' => $ann->priority ?? 'low',
        ])->toArray();

        // ── All announcements for the top banner ───────────────────────────
        $announcements = Announcement::active()
            ->forRole('registrar')
            ->orderByDesc('created_at')
            ->get();

        // ── Quick Links ────────────────────────────────────────────────────
        $quickLinks = [
            [
                'title'       => 'Review Requests',
                'description' => 'Process pending unlock and complaint requests',
                'route'       => 'registrar.grade-lock.index',
            ],
            [
                'title'       => 'Academic Calendar',
                'description' => 'Manage academic year and grading quarter dates',
                'route'       => 'admin.academic-years.index',
            ],
            [
                'title'       => 'Enrollment Verifications',
                'description' => 'Track enrollment and prerequisite status',
                'route'       => 'registrar.enrollment',
            ],
            [
                'title'       => 'Registrar Reports',
                'description' => 'View office performance and audit summaries',
                'route'       => 'admin.audit.index',
            ],
        ];

        return view('dashboard.registrar', compact(
            'user',
            'stats',
            'recentActivities',
            'pendingRequests',
            'deadlines',
            'notices',
            'quickLinks',
            'announcements'
        ));
    }

    public function students(Request $request)
    {
        $search   = $request->input('search', '');
        $students = User::where('role_id', '01')
            ->when($search, fn($q) => $q->where(function ($q2) use ($search) {
                $q2->where('first_name', 'like', "%{$search}%")
                   ->orWhere('last_name',  'like', "%{$search}%");
            }))
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.registrar-students', compact('students', 'search'));
    }

    public function enrollment(Request $request)
    {
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();

        $checkStudent = null;
        $checkGrade   = null;
        $unmetPrereqs = null;

        if ($request->filled('check_lrn') && $activeAcademicYear) {
            $checkStudent = User::where('lrn', $request->input('check_lrn'))
                ->where('role_id', '01')
                ->first();
            $checkGrade = $request->input('check_grade_level');

            if ($checkStudent && $checkGrade) {
                $unmetPrereqs = app(PrerequisiteService::class)
                    ->getUnmet($checkStudent, $checkGrade, $activeAcademicYear->id);
            }
        }

        $standardGradeLevels = [
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
            'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12',
        ];

        return view('dashboard.registrar-enrollment', compact(
            'activeAcademicYear',
            'checkStudent',
            'checkGrade',
            'unmetPrereqs',
            'standardGradeLevels'
        ));
    }

    public function requests(Request $request)
    {
        return view('dashboard.registrar-requests');
    }

    public function reportCards(Request $request)
    {
        return view('dashboard.registrar-report-cards');
    }

    public function grades(Request $request)
    {
        return view('dashboard.registrar-grades');
    }

    public function calendar(Request $request)
    {
        $academicYears = AcademicYear::with('quarters')->orderByDesc('id')->get();
        $activeYear    = $academicYears->firstWhere('status', 'active');
        return view('dashboard.registrar-calendar', compact('academicYears', 'activeYear'));
    }

    public function announcements(Request $request)
    {
        $announcements = Announcement::active()
            ->forRole('registrar')
            ->orderByDesc('created_at')
            ->get();
        return view('dashboard.registrar-announcements', compact('announcements'));
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'student_id'       => ['required', 'exists:users,id'],
            'grade_level'      => ['required', 'string'],
            'section_id'       => ['required', 'exists:sections,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $student = User::findOrFail($request->student_id);

        $unmet = app(PrerequisiteService::class)
            ->getUnmet($student, $request->grade_level, $request->academic_year_id);

        if (!empty($unmet)) {
            $msgList = collect($unmet)
                ->map(fn($u) => "{$u['subject']} requires {$u['requires']} (min {$u['min_grade']})")
                ->implode('; ');

            AuditLog::record(AuditLog::ENROLLMENT_BLOCKED_PREREQUISITE, [
                'student_id'  => $student->id,
                'grade_level' => $request->grade_level,
                'unmet'       => $msgList,
            ]);

            return back()->withErrors([
                'enrollment' => "Enrollment blocked. Unmet prerequisites: {$msgList}",
            ])->withInput();
        }

        Enrollment::create([
            'student_id'       => $student->id,
            'section_id'       => $request->section_id,
            'academic_year_id' => $request->academic_year_id,
            'status'           => 'enrolled',
            'enrolled_at'      => now(),
        ]);

        AuditLog::record(AuditLog::ENROLLMENT_CREATED, [
            'student_id'  => $student->id,
            'grade_level' => $request->grade_level,
        ]);

        return back()->with('success', 'Student enrolled successfully.');
    }
}
