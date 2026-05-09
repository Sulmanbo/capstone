<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FacultySchedule;
use App\Models\User;
use Illuminate\Http\Request;

class FacultyScheduleController extends Controller
{
    public function index()
    {
        $schedules    = FacultySchedule::with(['faculty', 'academicYear'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $faculty      = User::where('role_id', '02')->where('status', 'active')->orderBy('last_name')->get();
        $academicYears = AcademicYear::orderByDesc('id')->get();

        return view('admin.schedules.index', compact('schedules', 'faculty', 'academicYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'faculty_id'       => 'required|exists:users,id',
            'subject_name'     => 'required|string|max:255',
            'section'          => 'nullable|string|max:100',
            'room'             => 'nullable|string|max:100',
            'days'             => 'required|array|min:1',
            'days.*'           => 'in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $data['created_by'] = auth()->id();

        FacultySchedule::create($data);

        return back()->with('success', 'Schedule assigned successfully.');
    }

    public function destroy(FacultySchedule $schedule)
    {
        $schedule->delete();

        return back()->with('success', 'Schedule removed.');
    }

    public function update(Request $request, FacultySchedule $schedule)
    {
        $data = $request->validate([
            'faculty_id'       => 'required|exists:users,id',
            'subject_name'     => 'required|string|max:255',
            'section'          => 'nullable|string|max:100',
            'room'             => 'nullable|string|max:100',
            'days'             => 'required|array|min:1',
            'days.*'           => 'in:monday,tuesday,wednesday,thursday,friday,saturday',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'academic_year_id' => 'nullable|exists:academic_years,id',
        ]);

        $schedule->update($data);

        return back()->with('success', 'Schedule updated.');
    }
}
