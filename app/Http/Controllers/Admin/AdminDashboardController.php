<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\FacultySchedule;
use App\Models\ThreatEvent;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'studentCount'    => User::where('role_id', '01')->where('status', 'active')->count(),
            'facultyCount'    => User::where('role_id', '02')->where('status', 'active')->count(),
            'registrarCount'  => User::where('role_id', '03')->where('status', 'active')->count(),
            'lockedAccounts'  => User::where('status', 'locked')->count(),
            'maleUsers'       => User::where('gender', 'male')->count(),
            'femaleUsers'     => User::where('gender', 'female')->count(),
            'activeThreats'   => ThreatEvent::where('status', 'active')->count(),
            'recentAnnouncements' => Announcement::with('author')
                ->orderByDesc('created_at')
                ->take(4)
                ->get(),
            'totalAnnouncements'  => Announcement::count(),
            'activeAnnouncements' => Announcement::active()->count(),
            'recentSchedules'     => FacultySchedule::with('faculty')
                ->orderByDesc('created_at')
                ->take(4)
                ->get(),
            'totalSchedules'      => FacultySchedule::count(),
        ]);
    }
}
