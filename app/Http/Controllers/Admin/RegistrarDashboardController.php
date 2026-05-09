<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\GradingQuarter;
use App\Models\GradeUnlockRequest;
use App\Models\Subject;
use App\Models\CurriculumMapping;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * RegistrarDashboardController
 *
 * Manages the Registrar Module landing page with:
 * - Welcome banner with registrar name, timestamp, and academic year
 * - Quick action toolbar buttons
 * - Statistical cards (grade submission rate, pending unlocks, active students)
 * - Academic feed showing section updates
 */
class RegistrarDashboardController extends Controller
{
    /**
     * Show the Registrar Dashboard
     */
    public function index(Request $request)
    {
        // ── Get current registrar (logged-in user) ─────────────────────────
        $registrar = auth()->user();
        
        // ── Get active academic year ───────────────────────────────────────
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        
        // ── Get active grading quarter ─────────────────────────────────────
        $activeQuarter = null;
        if ($activeAcademicYear) {
            $activeQuarter = $activeAcademicYear->quarters()
                ->where('status', 'active')
                ->first();
        }
        
        // ── Statistics ─────────────────────────────────────────────────────
        $stats = $this->calculateStatistics($activeAcademicYear, $activeQuarter);
        
        // ── Quick action items ─────────────────────────────────────────────
        $quickActions = [
            [
                'title' => 'Curriculum Registry',
                'icon' => 'book-open',
                'description' => 'Manage subjects and curriculum mappings',
                'color' => 'blue',
                'route' => 'admin.curriculum-registry.index',
            ],
            [
                'title' => 'Grade Verification',
                'icon' => 'check-circle',
                'description' => 'Verify submitted grades from faculty',
                'color' => 'green',
                'route' => 'admin.grade-verification.index',
            ],
            [
                'title' => 'Global Lock',
                'icon' => 'lock',
                'description' => 'Lock/unlock grade submissions system-wide',
                'color' => 'red',
                'route' => 'admin.global-lock.index',
            ],
            [
                'title' => 'Promotion',
                'icon' => 'arrow-up-circle',
                'description' => 'Manage student grade level promotions',
                'color' => 'purple',
                'route' => 'admin.promotion.index',
            ],
        ];
        
        // ── Academic feed (recent section updates) ─────────────────────────
        $academicFeed = $this->getAcademicFeed($activeAcademicYear);
        
        // ── System information ─────────────────────────────────────────────
        $systemInfo = [
            'current_timestamp' => now(),
            'academic_year' => $activeAcademicYear,
            'quarter' => $activeQuarter,
            'total_academic_years' => AcademicYear::count(),
            'total_subjects' => Subject::where('status', 'active')->count(),
            'total_active_students' => User::where('role_id', '04')->where('status', 'active')->count(),
        ];
        
        return view('admin.registrars.dashboard', compact(
            'registrar',
            'stats',
            'quickActions',
            'academicFeed',
            'systemInfo'
        ));
    }

    /**
     * Calculate dashboard statistics
     */
    private function calculateStatistics($activeAcademicYear, $activeQuarter)
    {
        return [
            // Grade Submission Rate
            'grade_submission_rate' => $this->calculateGradeSubmissionRate($activeQuarter),
            
            // Pending Unlock Requests
            'pending_unlocks' => GradeUnlockRequest::pending()->count(),
            
            // Total Active Students
            'total_active_students' => User::where('role_id', '04')  // 04 = Student
                ->where('status', 'active')
                ->count(),
            
            // Academic Year Info
            'active_academic_year' => $activeAcademicYear,
            'active_quarter' => $activeQuarter,
            
            // Curriculum Stats
            'total_subjects' => Subject::where('status', 'active')->count(),
            'total_curriculum_entries' => CurriculumMapping::where('status', 'active')
                ->when($activeAcademicYear, function($q) use ($activeAcademicYear) {
                    return $q->where('academic_year_id', $activeAcademicYear->id);
                })
                ->count(),
            
            // Grade Levels
            'grade_levels' => CurriculumMapping::where('status', 'active')
                ->distinct()
                ->pluck('grade_level')
                ->sort()
                ->values(),
        ];
    }

    /**
     * Calculate grade submission rate
     * Placeholder implementation - would need actual grade submission tracking
     */
    private function calculateGradeSubmissionRate($activeQuarter)
    {
        // This would need a GradeSubmission or similar model
        // For now, returning a placeholder percentage
        return 85; // 85% as example
    }

    /**
     * Get the academic feed (recent updates)
     */
    private function getAcademicFeed($activeAcademicYear)
    {
        $feed = [];
        
        if ($activeAcademicYear) {
            // Add recent curriculum changes
            $recentChanges = CurriculumMapping::where('academic_year_id', $activeAcademicYear->id)
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->with('subject')
                ->get();
            
            foreach ($recentChanges as $change) {
                $feed[] = [
                    'type' => 'curriculum_update',
                    'title' => 'Curriculum Updated',
                    'message' => "{$change->grade_level} - {$change->subject->subject_name}",
                    'timestamp' => $change->updated_at,
                    'status' => $change->status,
                ];
            }
        }
        
        // Add recent academic year changes
        $recentYears = AcademicYear::orderBy('updated_at', 'desc')
            ->take(3)
            ->get();
        
        foreach ($recentYears as $year) {
            if ($year->updated_at->diffInDays(now()) <= 7) {  // Only show recent changes
                $feed[] = [
                    'type' => 'academic_year_update',
                    'title' => 'Academic Year Status',
                    'message' => "{$year->year_label} - Status: {$year->status}",
                    'timestamp' => $year->updated_at,
                    'status' => $year->status,
                ];
            }
        }
        
        // Sort by timestamp descending
        usort($feed, function($a, $b) {
            return $b['timestamp']->timestamp <=> $a['timestamp']->timestamp;
        });
        
        return collect($feed)->take(10);
    }
}
