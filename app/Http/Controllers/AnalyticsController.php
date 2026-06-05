<?php
namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::where('status', 'active')->first();

        // ── Enrollment stats ──────────────────────────────────────────────
        $totalStudents   = User::where('role_id', '01')->where('status', 'active')->count();
        $enrolledCount   = $activeYear
            ? Enrollment::where('academic_year_id', $activeYear->id)->where('status', 'enrolled')->count()
            : 0;
        $droppedCount    = $activeYear
            ? Enrollment::where('academic_year_id', $activeYear->id)->where('status', 'dropped')->count()
            : 0;

        $enrollmentByGrade = $activeYear
            ? Enrollment::where('academic_year_id', $activeYear->id)
                ->where('status', 'enrolled')
                ->with('section')
                ->get()
                ->groupBy(fn($e) => $e->section?->grade_level ?? 'Unknown')
                ->map->count()
                ->sortKeys()
            : collect();

        // ── Grade distribution (finalized only) ──────────────────────────
        $gradeDistribution = Grade::whereNotNull('final_grade')
            ->where('status', 'finalized')
            ->selectRaw("
                CASE
                    WHEN final_grade >= 90 THEN 'Outstanding (90-100)'
                    WHEN final_grade >= 85 THEN 'Very Satisfactory (85-89)'
                    WHEN final_grade >= 80 THEN 'Satisfactory (80-84)'
                    WHEN final_grade >= 75 THEN 'Fairly Satisfactory (75-79)'
                    ELSE 'Did Not Meet Expectations (<75)'
                END as band,
                COUNT(*) as cnt
            ")
            ->groupBy('band')
            ->orderByRaw("MIN(final_grade) DESC")
            ->pluck('cnt', 'band');

        $avgGrade = Grade::whereNotNull('final_grade')
            ->where('status', 'finalized')
            ->avg('final_grade');

        $belowPassingCount = Grade::whereNotNull('final_grade')
            ->where('status', 'finalized')
            ->where('final_grade', '<', 75)
            ->count();

        // ── At-risk students (failing 2+ subjects) ────────────────────────
        $atRisk = [];
        if ($activeYear) {
            $atRiskRows = Grade::whereNotNull('final_grade')
                ->where('status', 'finalized')
                ->where('final_grade', '<', 75)
                ->whereHas('enrollment', fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->with(['enrollment.student', 'sectionSubject.subject'])
                ->get()
                ->groupBy(fn($g) => $g->enrollment?->student_id)
                ->filter(fn($grades) => $grades->count() >= 2)
                ->map(fn($grades) => [
                    'student'  => $grades->first()->enrollment->student,
                    'count'    => $grades->count(),
                    'subjects' => $grades->pluck('sectionSubject.subject.subject_name')->filter()->implode(', '),
                ])
                ->values();
            $atRisk = $atRiskRows->take(20);
        }

        // ── Attendance overview ───────────────────────────────────────────
        $attendanceRate = null;
        if ($activeYear) {
            $total   = Attendance::whereHas('sectionSubject', fn($q) => $q->where('academic_year_id', $activeYear->id))->count();
            $present = Attendance::whereHas('sectionSubject', fn($q) => $q->where('academic_year_id', $activeYear->id))
                ->where('status', 'present')->count();
            $attendanceRate = $total > 0 ? round(($present / $total) * 100, 1) : null;
        }

        // ── Faculty stats ─────────────────────────────────────────────────
        $totalFaculty   = User::where('role_id', '02')->where('status', 'active')->count();
        $activeSections = $activeYear ? Section::where('academic_year_id', $activeYear->id)->count() : 0;

        // ── Grade submission pipeline ─────────────────────────────────────
        $gradePipeline = Grade::selectRaw("status, COUNT(*) as cnt")
            ->groupBy('status')
            ->pluck('cnt', 'status');

        return view('analytics.index', compact(
            'activeYear', 'totalStudents', 'enrolledCount', 'droppedCount',
            'enrollmentByGrade', 'gradeDistribution', 'avgGrade', 'belowPassingCount',
            'atRisk', 'attendanceRate', 'totalFaculty', 'activeSections', 'gradePipeline'
        ));
    }
}
