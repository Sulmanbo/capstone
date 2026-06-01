<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Classroom CRUD.
 *
 * Classrooms are year-scoped per adviser feedback. Same room name twice in
 * the same year is rejected by the DB unique constraint.
 */
class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $yearId = $request->input('academic_year_id') ?? optional($academicYears->first())->id;

        $classrooms = Classroom::query()
            ->with('academicYear')
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->orderBy('room_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.classrooms.index', compact('classrooms', 'academicYears', 'yearId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'room_name'        => [
                'required', 'string', 'max:50',
                Rule::unique('classrooms')->where(fn($q) => $q->where('academic_year_id', $request->academic_year_id)),
            ],
            'building'         => ['nullable', 'string', 'max:50'],
            'capacity'         => ['required', 'integer', 'min:1', 'max:200'],
            'status'           => ['required', Rule::in(['active','inactive'])],
        ]);

        $classroom = Classroom::create($data);

        AuditLog::record('CLASSROOM_CREATED', [
            'classroom_id' => $classroom->id,
            'room_name'    => $classroom->room_name,
            'year_id'      => $classroom->academic_year_id,
        ]);

        return back()->with('success', 'Classroom created.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'room_name' => [
                'required', 'string', 'max:50',
                Rule::unique('classrooms')
                    ->ignore($classroom->id)
                    ->where(fn($q) => $q->where('academic_year_id', $classroom->academic_year_id)),
            ],
            'building'  => ['nullable', 'string', 'max:50'],
            'capacity'  => ['required', 'integer', 'min:1', 'max:200'],
            'status'    => ['required', Rule::in(['active','inactive'])],
        ]);

        $before = $classroom->only(['room_name','building','capacity','status']);
        $classroom->update($data);

        AuditLog::record('CLASSROOM_UPDATED', [
            'classroom_id' => $classroom->id,
            'before'       => $before,
            'after'        => $classroom->only(['room_name','building','capacity','status']),
        ]);

        return back()->with('success', 'Classroom updated.');
    }

    public function destroy(Classroom $classroom)
    {
        // Prevent deletion if schedules reference this room
        if ($classroom->sectionSubjects()->exists()) {
            return back()->withErrors([
                'classroom' => 'This classroom cannot be deleted because it is used in one or more schedules. Deactivate it instead.',
            ]);
        }

        AuditLog::record('CLASSROOM_DELETED', [
            'classroom_id' => $classroom->id,
            'room_name'    => $classroom->room_name,
            'year_id'      => $classroom->academic_year_id,
        ]);

        $classroom->delete();

        return back()->with('success', 'Classroom removed.');
    }
}
