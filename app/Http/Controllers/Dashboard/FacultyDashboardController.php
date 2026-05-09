<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\AuditLog;
use App\Models\SectionSubject;
use Illuminate\Http\Request;

class FacultyDashboardController extends Controller
{
    private function loadSchedules(int $facultyId)
    {
        return SectionSubject::forFaculty($facultyId)
            ->forActiveAcademicYear()
            ->with(['section', 'subject'])
            ->orderBy('start_time')
            ->get();
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        $activeQuarter      = $activeAcademicYear
            ? $activeAcademicYear->quarters()->where('status', 'active')->first()
            : null;

        $allSchedules = $this->loadSchedules($user->id);
        $todayClasses = $allSchedules->filter(fn($s) => $s->meetsToday())->values();

        $announcements = Announcement::active()
            ->forRole('faculty')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentActivities = AuditLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $recentLogins = AuditLog::where('user_id', $user->id)
            ->whereIn('action_type', [AuditLog::LOGIN_SUCCESS, AuditLog::LOGIN_FAILED])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('dashboard.faculty', compact(
            'user',
            'activeAcademicYear',
            'activeQuarter',
            'allSchedules',
            'todayClasses',
            'announcements',
            'recentActivities',
            'recentLogins'
        ));
    }

    public function myClasses(Request $request)
    {
        $user               = auth()->user();
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        $allSchedules       = $this->loadSchedules($user->id);

        return view('dashboard.faculty-classes', compact('user', 'allSchedules', 'activeAcademicYear'));
    }

    public function gradebook(Request $request)
    {
        $user         = auth()->user();
        $allSchedules = $this->loadSchedules($user->id);

        return view('dashboard.faculty-gradebook', compact('user', 'allSchedules'));
    }

    public function attendance(Request $request)
    {
        $user         = auth()->user();
        $allSchedules = $this->loadSchedules($user->id);

        return view('dashboard.faculty-attendance', compact('user', 'allSchedules'));
    }

    public function mySchedule(Request $request)
    {
        $user               = auth()->user();
        $activeAcademicYear = AcademicYear::where('status', 'active')->first();
        $allSchedules       = $this->loadSchedules($user->id);
        $todayName          = strtolower(now()->format('l'));

        return view('dashboard.faculty-my-schedule', compact(
            'user', 'allSchedules', 'activeAcademicYear', 'todayName'
        ));
    }

    public function announcements(Request $request)
    {
        $user          = auth()->user();
        $announcements = Announcement::active()
            ->forRole('faculty')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.faculty-announcements', compact('user', 'announcements'));
    }
}
