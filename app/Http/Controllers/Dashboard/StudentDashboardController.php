<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Assessment;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SectionSubject;
use App\Services\PrerequisiteService;
use Illuminate\Http\Request;

class StudentDashboardController extends Controller
{
    // ── Private helpers ────────────────────────────────────────────────────

    private function academicContext(): array
    {
        $activeYear    = AcademicYear::where('status', 'active')->first();
        $activeQuarter = $activeYear
            ? $activeYear->quarters()->where('status', 'active')->first()
            : null;

        return [$activeYear, $activeQuarter];
    }

    private function activeEnrollment(int $studentId): ?Enrollment
    {
        return Enrollment::where('student_id', $studentId)
            ->where('status', 'enrolled')
            ->with(['section'])
            ->first();
    }

    private function studentInfo($user, ?Enrollment $enrollment): array
    {
        return [
            'full_name'   => $user->full_name,
            'lrn'         => $user->lrn,
            'grade_level' => $enrollment?->section?->grade_level ?? $user->grade_level ?? 'N/A',
            'section'     => $enrollment?->section?->section_name ?? 'N/A',
        ];
    }

    private function sectionSubjectsFor(?Enrollment $enrollment)
    {
        if (!$enrollment) {
            return collect();
        }

        return SectionSubject::forSection($enrollment->section_id)
            ->forActiveAcademicYear()
            ->with(['subject', 'faculty'])
            ->orderBy('start_time')
            ->get();
    }

    private function buildTodaySchedule($sectionSubjects): array
    {
        $today = strtolower(now()->format('l'));

        return $sectionSubjects
            ->filter(fn($ss) => $ss->meetsOn($today))
            ->sortBy('start_time')
            ->map(fn($ss) => [
                'time'    => $ss->time_range,
                'subject' => $ss->subject?->subject_name ?? '—',
                'teacher' => $ss->faculty?->full_name ?? '—',
                'room'    => $ss->room ?? '—',
            ])
            ->values()
            ->toArray();
    }

    // ── Actions ────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = auth()->user();
        [$activeAcademicYear, $activeQuarter] = $this->academicContext();
        $enrollment      = $this->activeEnrollment($user->id);
        $sectionSubjects = $this->sectionSubjectsFor($enrollment);
        $ssIds           = $sectionSubjects->pluck('id')->all();

        // ── Grades for the active quarter ───────────────────────────────────
        $grades = collect();
        if ($enrollment && $activeQuarter && !empty($ssIds)) {
            $grades = Grade::where('enrollment_id', $enrollment->id)
                ->where('grading_quarter_id', $activeQuarter->id)
                ->whereIn('section_subject_id', $ssIds)
                ->get()
                ->keyBy('section_subject_id');
        }

        // ── Current subjects with quarter grade ─────────────────────────────
        $currentSubjects = $sectionSubjects->map(function ($ss) use ($grades) {
            $grade = $grades->get($ss->id);
            return [
                'code'   => $ss->subject?->subject_code ?? '—',
                'name'   => $ss->subject?->subject_name ?? '—',
                'grade'  => $grade?->final_grade !== null ? number_format($grade->final_grade, 0) : '—',
                'status' => $grade?->descriptor ?? '—',
            ];
        })->toArray();

        // ── Overall GPA from all finalized/locked grades ────────────────────
        $allFinalizedGrades = $enrollment
            ? Grade::where('enrollment_id', $enrollment->id)
                ->whereIn('status', ['finalized', 'locked'])
                ->whereNotNull('final_grade')
                ->get()
            : collect();

        $gpa = $allFinalizedGrades->isNotEmpty()
            ? number_format($allFinalizedGrades->avg('final_grade'), 2)
            : '—';

        $standing = '—';
        if ($allFinalizedGrades->isNotEmpty()) {
            $avg      = $allFinalizedGrades->avg('final_grade');
            $standing = $avg >= 90 ? 'Outstanding'
                : ($avg >= 85 ? 'Very Satisfactory'
                : ($avg >= 80 ? 'Satisfactory'
                : ($avg >= 75 ? 'Fairly Satisfactory'
                : 'Did Not Meet Expectations')));
        }

        // ── Attendance rate ─────────────────────────────────────────────────
        $totalAtt   = ($enrollment && !empty($ssIds))
            ? Attendance::where('enrollment_id', $enrollment->id)
                ->whereIn('section_subject_id', $ssIds)->count()
            : 0;
        $presentAtt = ($totalAtt > 0)
            ? Attendance::where('enrollment_id', $enrollment->id)
                ->whereIn('section_subject_id', $ssIds)
                ->whereIn('status', ['present', 'late'])->count()
            : 0;
        $attendanceRate = $totalAtt > 0
            ? round(($presentAtt / $totalAtt) * 100) . '%'
            : '—';

        $stats = [
            'gpa'                  => $gpa,
            'attendance_rate'      => $attendanceRate,
            'total_subjects'       => $sectionSubjects->count(),
            'standing'             => $standing,
            'active_quarter'       => $activeQuarter,
            'active_academic_year' => $activeAcademicYear,
        ];

        // ── Today's schedule ────────────────────────────────────────────────
        $todaySchedule = $this->buildTodaySchedule($sectionSubjects);

        // ── Upcoming assignments (next 7 days) ──────────────────────────────
        $upcomingAssignments = [];
        if (!empty($ssIds)) {
            $upcomingAssignments = Assessment::whereIn('section_subject_id', $ssIds)
                ->where('status', 'posted')
                ->whereNotNull('due_date')
                ->where('due_date', '>=', now())
                ->orderBy('due_date')
                ->take(5)
                ->with('sectionSubject.subject')
                ->get()
                ->map(fn($a) => [
                    'title'    => $a->title,
                    'subject'  => $a->sectionSubject?->subject?->subject_name ?? '—',
                    'due_date' => $a->due_date->format('M d, Y'),
                    'priority' => $a->due_date->diffInDays(now()) <= 1 ? 'high'
                               : ($a->due_date->diffInDays(now()) <= 3 ? 'medium' : 'low'),
                ])->toArray();
        }

        // ── Assessment component breakdown (averages across subjects) ───────
        $gradedRecords = ($enrollment && !empty($ssIds))
            ? Grade::where('enrollment_id', $enrollment->id)
                ->whereIn('section_subject_id', $ssIds)
                ->whereNotNull('written_work')
                ->get()
            : collect();

        $assessmentBreakdown = $gradedRecords->isNotEmpty() ? [
            ['label' => 'Written Work (30%)',         'description' => 'Quizzes, long tests, written outputs',  'score' => round($gradedRecords->avg('written_work'), 1)],
            ['label' => 'Performance Tasks (50%)',    'description' => 'Projects, presentations, experiments',   'score' => round($gradedRecords->avg('performance_task'), 1)],
            ['label' => 'Quarterly Assessment (20%)', 'description' => 'Periodic/quarterly examinations',        'score' => round($gradedRecords->avg('quarterly_assessment'), 1)],
        ] : [];

        // ── Report cards (one per finalized quarter) ────────────────────────
        $gradesByQuarter = $enrollment
            ? Grade::where('enrollment_id', $enrollment->id)
                ->whereIn('status', ['finalized', 'locked'])
                ->whereNotNull('final_grade')
                ->with('gradingQuarter')
                ->get()
                ->groupBy('grading_quarter_id')
            : collect();

        $reportCards = $gradesByQuarter->map(function ($gradeGroup) use ($activeAcademicYear) {
            $q = $gradeGroup->first()->gradingQuarter;
            return [
                'term'    => $q?->quarter_name ?? '—',
                'year'    => $activeAcademicYear?->year_label ?? '—',
                'status'  => 'Finalized',
                'gpa'     => number_format($gradeGroup->avg('final_grade') ?? 0, 2),
                'remarks' => $gradeGroup->every(fn($g) => $g->isPassing())
                             ? 'All subjects passed'
                             : 'Some subjects need improvement',
                '_sort'   => $q?->quarter_number ?? 0,
            ];
        })->sortBy('_sort')->values()
          ->map(fn($rc) => collect($rc)->except('_sort')->toArray())
          ->toArray();

        // ── Announcements ────────────────────────────────────────────────────
        $announcements = Announcement::active()
            ->forRole('student')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($a) => [
                'title'    => $a->title,
                'message'  => $a->message,
                'date'     => $a->created_at->format('M d, Y'),
                'priority' => $a->priority,
            ])->toArray();

        // ── Audit log ───────────────────────────────────────────────────────
        $recentActivities = AuditLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $recentLogins = AuditLog::where('user_id', $user->id)
            ->whereIn('action_type', [AuditLog::LOGIN_SUCCESS, AuditLog::LOGIN_FAILED])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $studentInfo = $this->studentInfo($user, $enrollment);

        $quickActions = [
            ['title' => 'View Grades',      'description' => 'Check your grades for all subjects',      'icon' => 'bar-chart-2',    'color' => 'blue',   'action' => 'view_grades'],
            ['title' => 'Class Schedule',   'description' => 'See your complete class schedule',        'icon' => 'calendar',       'color' => 'green',  'action' => 'class_schedule'],
            ['title' => 'Assignments',      'description' => 'View upcoming assignments and deadlines', 'icon' => 'clipboard-list', 'color' => 'orange', 'action' => 'assignments'],
            ['title' => 'Announcements',    'description' => 'Read important school announcements',     'icon' => 'bell',           'color' => 'purple', 'action' => 'announcements'],
            ['title' => 'Attendance',       'description' => 'Check your attendance record',            'icon' => 'check-circle',   'color' => 'teal',   'action' => 'attendance'],
            ['title' => 'Academic Progress','description' => 'Track your academic standing',            'icon' => 'trending-up',    'color' => 'red',    'action' => 'academic_progress'],
        ];

        return view('dashboard.student', compact(
            'user',
            'stats',
            'studentInfo',
            'recentActivities',
            'quickActions',
            'currentSubjects',
            'todaySchedule',
            'upcomingAssignments',
            'announcements',
            'assessmentBreakdown',
            'reportCards',
            'recentLogins'
        ));
    }

    public function reportCard()
    {
        $user = auth()->user();
        [$activeAcademicYear, $activeQuarter] = $this->academicContext();
        $enrollment  = $this->activeEnrollment($user->id);
        $studentInfo = $this->studentInfo($user, $enrollment);

        $reportCard = [
            'term'     => $activeQuarter?->quarter_name ?? '—',
            'year'     => $activeAcademicYear?->year_label ?? '—',
            'status'   => 'Pending',
            'gpa'      => '—',
            'remarks'  => 'No finalized grades yet.',
            'subjects' => [],
        ];

        if ($enrollment) {
            $ssIds = SectionSubject::forSection($enrollment->section_id)
                ->forActiveAcademicYear()
                ->pluck('id')
                ->all();

            $latestGrade = empty($ssIds) ? null
                : Grade::where('enrollment_id', $enrollment->id)
                    ->whereIn('status', ['finalized', 'locked'])
                    ->whereIn('section_subject_id', $ssIds)
                    ->with('gradingQuarter')
                    ->latest('finalized_at')
                    ->first();

            if ($latestGrade?->gradingQuarter) {
                $q       = $latestGrade->gradingQuarter;
                $qGrades = Grade::where('enrollment_id', $enrollment->id)
                    ->where('grading_quarter_id', $q->id)
                    ->whereIn('status', ['finalized', 'locked'])
                    ->whereNotNull('final_grade')
                    ->with('sectionSubject.subject')
                    ->get();

                $reportCard = [
                    'term'     => $q->quarter_name,
                    'year'     => $activeAcademicYear?->year_label ?? '—',
                    'status'   => 'Finalized',
                    'gpa'      => number_format($qGrades->avg('final_grade') ?? 0, 2),
                    'remarks'  => $qGrades->every(fn($g) => $g->isPassing())
                                  ? 'All subjects passed'
                                  : 'Some subjects need improvement',
                    'subjects' => $qGrades->map(fn($g) => [
                        'name'     => $g->sectionSubject?->subject?->subject_name ?? '—',
                        'category' => $g->descriptor ?? '—',
                        'grade'    => number_format($g->final_grade, 0),
                    ])->toArray(),
                ];
            }
        }

        return view('dashboard.student-report-card', compact('user', 'studentInfo', 'reportCard'));
    }

    public function schedule()
    {
        $user        = auth()->user();
        $enrollment  = $this->activeEnrollment($user->id);
        $studentInfo = $this->studentInfo($user, $enrollment);

        $todaySchedule = $this->buildTodaySchedule($this->sectionSubjectsFor($enrollment));

        return view('dashboard.student-schedule', compact('user', 'studentInfo', 'todaySchedule'));
    }

    public function courseOfferings()
    {
        $user        = auth()->user();
        $enrollment  = $this->activeEnrollment($user->id);
        $studentInfo = $this->studentInfo($user, $enrollment);

        $courses = $enrollment
            ? SectionSubject::forSection($enrollment->section_id)
                ->forActiveAcademicYear()
                ->with(['subject', 'faculty'])
                ->orderBy('start_time')
                ->get()
                ->map(fn($ss) => [
                    'code'     => $ss->subject?->subject_code ?? '—',
                    'name'     => $ss->subject?->subject_name ?? '—',
                    'teacher'  => $ss->faculty?->full_name ?? '—',
                    'schedule' => $ss->time_range . '  ' . $ss->schedule_days_label,
                    'status'   => 'Enrolled',
                ])->toArray()
            : [];

        return view('dashboard.student-course-offerings', compact('user', 'studentInfo', 'courses'));
    }

    public function programCurriculum()
    {
        $user        = auth()->user();
        $enrollment  = $this->activeEnrollment($user->id);
        $studentInfo = $this->studentInfo($user, $enrollment);
        $curriculum  = [];

        return view('dashboard.student-program-curriculum', compact('user', 'studentInfo', 'curriculum'));
    }

    public function academicHolds()
    {
        $user        = auth()->user();
        $enrollment  = $this->activeEnrollment($user->id);
        $studentInfo = $this->studentInfo($user, $enrollment);
        $holds       = [];

        // Build prerequisite holds for the student's current enrolled grade level
        if ($enrollment) {
            $gradeLevel  = $enrollment->section?->grade_level;
            $yearId      = $enrollment->academic_year_id;

            if ($gradeLevel && $yearId) {
                $unmet = app(PrerequisiteService::class)->getUnmet($user, $gradeLevel, $yearId);

                foreach ($unmet as $item) {
                    $holds[] = [
                        'type'        => 'Prerequisite',
                        'description' => "'{$item['subject']}' requires passing '{$item['requires']}'"
                            . ($item['student_grade'] !== null
                                ? " (your grade: {$item['student_grade']} / required: {$item['min_grade']})"
                                : " (no grade on record for '{$item['requires']}')"),
                        'date'        => $enrollment->enrolled_at?->format('M d, Y') ?? 'N/A',
                    ];
                }
            }
        }

        return view('dashboard.student-academic-holds', compact('user', 'studentInfo', 'holds'));
    }

    public function accountBalance()
    {
        $user        = auth()->user();
        $enrollment  = $this->activeEnrollment($user->id);
        $studentInfo = $this->studentInfo($user, $enrollment);
        $balance     = ['total_fees' => 0, 'total_paid' => 0, 'balance_due' => 0, 'transactions' => []];

        return view('dashboard.student-account-balance', compact('user', 'studentInfo', 'balance'));
    }

    public function admissionDocuments()
    {
        $user        = auth()->user();
        $enrollment  = $this->activeEnrollment($user->id);
        $studentInfo = $this->studentInfo($user, $enrollment);
        $documents   = [];

        return view('dashboard.student-admission-documents', compact('user', 'studentInfo', 'documents'));
    }
}
