<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class StudentAttendanceController extends Controller
{
    /**
     * Show attendance records for the current student.
     * Groups attendance by subject.
     */
    public function index()
    {
        $student = Auth::user();

        // Get the student's active enrollment(s) for the current academic year
        $enrollment = Enrollment::with(['section.sectionSubjects.subject'])
            ->where('student_id', $student->id)
            ->where('status', 'enrolled')
            ->latest()
            ->first();

        if (!$enrollment) {
            return view('dashboard.student-attendance', [
                'enrollment'  => null,
                'bySubject'   => collect(),
                'stats'       => null,
            ]);
        }

        // Get attendance records for all subjects in this enrollment
        $attendance = Attendance::with('sectionSubject.subject')
            ->where('enrollment_id', $enrollment->id)
            ->orderByDesc('date')
            ->get();

        // Group by subject
        $bySubject = $attendance->groupBy('sectionSubject.subject.subject_name')->map(function ($records) {
            $total = $records->count();
            $present = $records->where('status', 'present')->count();
            $absent = $records->where('status', 'absent')->count();
            $late = $records->where('status', 'late')->count();
            $excused = $records->where('status', 'excused')->count();

            $percentage = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            return [
                'records'    => $records,
                'total'      => $total,
                'present'    => $present,
                'absent'     => $absent,
                'late'       => $late,
                'excused'    => $excused,
                'percentage' => $percentage,
            ];
        });

        // Overall stats
        $totalRecords = $attendance->count();
        $totalPresent = $attendance->where('status', 'present')->count();
        $totalAbsent = $attendance->where('status', 'absent')->count();
        $stats = [
            'total'       => $totalRecords,
            'present'     => $totalPresent,
            'absent'      => $totalAbsent,
            'percentage'  => $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 1) : 0,
        ];

        return view('dashboard.student-attendance', compact('enrollment', 'bySubject', 'stats'));
    }
}
