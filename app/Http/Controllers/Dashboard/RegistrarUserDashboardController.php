<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Enrollment;
use App\Models\GradingQuarter;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\PrerequisiteService;
use Illuminate\Http\Request;

/**
 * RegistrarUserDashboardController
 *
 * Dashboard for Registrar staff (role 03) who work in the registrar's office.
 * Shows academic calendar, upcoming deadlines, and their personal tasks.
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
        
        // ── System Statistics for Context ──────────────────────────────────
        $stats = [
            'active_academic_year' => $activeAcademicYear,
            'active_quarter' => $activeQuarter,
            'pending_requests' => 34,
            'completed_requests' => 182,
            'enrollment_verifications' => 18,
            'documents_in_review' => 9,
        ];
        
        // ── Pending Registrar Workload ─────────────────────────────────────
        $pendingRequests = [
            ['type' => 'Transcript Request', 'student' => 'Juan Dela Cruz', 'status' => 'Waiting Approval', 'submitted' => 'Apr 28, 2026', 'due' => 'May 5, 2026'],
            ['type' => 'Enrollment Certification', 'student' => 'Maria Santos', 'status' => 'Under Review', 'submitted' => 'Apr 29, 2026', 'due' => 'May 6, 2026'],
            ['type' => 'Grade Verification', 'student' => 'Pedro Reyes', 'status' => 'Pending Documents', 'submitted' => 'Apr 30, 2026', 'due' => 'May 8, 2026'],
            ['type' => 'Clearance Form', 'student' => 'Anna Lopez', 'status' => 'Ready for Print', 'submitted' => 'May 1, 2026', 'due' => 'May 4, 2026'],
        ];

        // ── Registrar Deadlines and Office Notices ──────────────────────────
        $deadlines = [
            ['title' => 'Senior Certificate Submission', 'date' => 'May 15, 2026', 'note' => 'Verify all forms before submission.'],
            ['title' => 'Summer Enrollment Freeze', 'date' => 'May 20, 2026', 'note' => 'Finalize transcript batch before freeze.'],
            ['title' => 'Document Audit Review', 'date' => 'May 25, 2026', 'note' => 'Complete pending audit entries for this term.'],
        ];

        $notices = [
            ['message' => 'Registrar office systems will undergo maintenance on May 10.', 'priority' => 'medium'],
            ['message' => 'New verification workflow launched for enrollment certificates.', 'priority' => 'high'],
            ['message' => 'Submit end-of-term processing reports to the dean’s office.', 'priority' => 'low'],
        ];

        // ── Announcements for Registrar ────────────────────────────────────
        $announcements = Announcement::active()
            ->forRole('registrar')
            ->orderByDesc('created_at')
            ->get();

        // ── Quick Links and Resources ──────────────────────────────────────
        $quickLinks = [
            [
                'title' => 'Review Requests',
                'description' => 'Process pending document and record requests',
                'route' => 'admin.students.index',
            ],
            [
                'title' => 'Academic Calendar',
                'description' => 'Manage academic year and grading quarter dates',
                'route' => 'admin.academic-years.index',
            ],
            [
                'title' => 'Enrollment Verifications',
                'description' => 'Track enrollment certification progress',
                'route' => 'admin.curriculum-mappings.index',
            ],
            [
                'title' => 'Registrar Reports',
                'description' => 'View office performance and audit summaries',
                'route' => 'admin.audit.index',
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

        // Prerequisite check tool: look up a student by LRN
        $checkStudent   = null;
        $checkGrade     = null;
        $unmetPrereqs   = null;

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
