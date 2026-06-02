<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Models\Subject;
use App\Models\User;
use App\Services\ScheduleConflictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Admin ScheduleController
 *
 * Per adviser feedback, this is the cascading-dropdown schedule creator:
 *
 *   1. Academic Year (top of the form — selected first)
 *   2. Section (filtered by year)
 *   3. Subject (filtered by section's grade level)
 *   4. Classroom (filtered by year)
 *   5. Faculty (OPTIONAL — TBA is allowed)
 *   6. Days + start/end time
 *
 * Conflict service runs on every save: faculty/room time overlaps and the
 * minimum-duration rule. The DB unique constraint prevents the same subject
 * from being scheduled twice in one section per year.
 *
 * On faculty assignment (either initially or via assignFaculty), a matching
 * section_subjects row is created or attached, so existing grading and class-
 * list code continues to work unchanged.
 */
class ScheduleController extends Controller
{
    public function __construct(private ScheduleConflictService $conflicts)
    {
    }

    /**
     * GET /admin/schedules
     */
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $yearId       = $request->input('academic_year_id') ?? AcademicYear::currentId();
        $sectionId    = $request->input('section_id');
        $facultyId    = $request->input('faculty_id');
        $statusFilter = $request->input('status');

        $schedules = Schedule::query()
            ->with(['section', 'subject', 'faculty', 'classroom', 'academicYear'])
            ->when($yearId,       fn($q) => $q->where('academic_year_id', $yearId))
            ->when($sectionId,    fn($q) => $q->where('section_id', $sectionId))
            ->when($facultyId,    fn($q) => $q->where('faculty_id', $facultyId))
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->orderBy('start_time')
            ->paginate(20)
            ->withQueryString();

        $sections = $yearId
            ? Section::where('academic_year_id', $yearId)->active()->orderBy('grade_level')->orderBy('section_name')->get()
            : collect();

        $faculty = User::where('role_id', '02')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->get();

        return view('admin.schedules.index', compact(
            'schedules', 'academicYears', 'sections', 'faculty',
            'yearId', 'sectionId', 'facultyId', 'statusFilter'
        ));
    }

    /**
     * GET /admin/schedules/create
     */
    public function create(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $yearId        = $request->input('academic_year_id') ?? AcademicYear::currentId();

        $sections   = collect();
        $classrooms = collect();
        $faculty    = User::where('role_id', '02')->where('status', 'active')->orderBy('last_name')->get();

        if ($yearId) {
            $sections   = Section::where('academic_year_id', $yearId)->active()->orderBy('grade_level')->orderBy('section_name')->get();
            $classrooms = Classroom::forYear($yearId)->active()->orderBy('room_name')->get();
        }

        return view('admin.schedules.create', compact('academicYears', 'sections', 'classrooms', 'faculty', 'yearId'));
    }

    /**
     * AJAX: GET /admin/schedules/subjects-for-section/{section}
     *
     * Returns active subjects matching the section's grade level. Used by the
     * cascading dropdown JS in the create/edit form.
     */
    public function subjectsForSection(Section $section)
    {
        $subjects = Subject::query()
            ->where('status', 'active')
            ->where(function ($q) use ($section) {
                $q->where('year_level', $section->grade_level)
                  ->orWhereNull('year_level');   // legacy rows without a year_level still show
            })
            ->orderBy('subject_name')
            ->get(['id', 'subject_code', 'subject_name', 'year_level']);

        return response()->json($subjects);
    }

    /**
     * AJAX: POST /admin/schedules/check-conflict
     *
     * Accepts the same fields as the schedule form and returns any conflicts
     * so the UI can show live availability warnings before submission.
     */
    public function checkConflict(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required'],
            'faculty_id'       => ['nullable'],
            'classroom_id'     => ['nullable'],
            'schedule_days'    => ['required', 'array'],
            'schedule_days.*'  => ['string'],
            'start_time'       => ['required'],
            'end_time'         => ['required'],
            'ignore_id'        => ['nullable', 'integer'],
        ]);

        $errors = $this->conflicts->check($data, ignoreId: $data['ignore_id'] ?? null);

        return response()->json(['conflicts' => $errors]);
    }

    /**
     * POST /admin/schedules
     */
    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $errors = $this->conflicts->check($data);
        if (!empty($errors)) {
            return back()->withInput()->withErrors(['conflict' => $errors]);
        }

        $schedule = DB::transaction(function () use ($data) {
            $hasFaculty = !empty($data['faculty_id']);

            $schedule = Schedule::create([
                'academic_year_id' => $data['academic_year_id'],
                'section_id'       => $data['section_id'],
                'subject_id'       => $data['subject_id'],
                'classroom_id'     => $data['classroom_id'] ?? null,
                'faculty_id'       => $hasFaculty ? $data['faculty_id'] : null,
                'schedule_days'    => $data['schedule_days'],
                'start_time'       => $data['start_time'],
                'end_time'         => $data['end_time'],
                'status'           => $hasFaculty ? 'assigned' : 'tba',
            ]);

            if ($hasFaculty) {
                $schedule->section_subject_id = $this->syncSectionSubject($schedule)->id;
                $schedule->save();
            }

            AuditLog::record('SCHEDULE_CREATED', [
                'schedule_id'      => $schedule->id,
                'section_id'       => $schedule->section_id,
                'subject_id'       => $schedule->subject_id,
                'faculty_id'       => $schedule->faculty_id,
                'classroom_id'     => $schedule->classroom_id,
                'status'           => $schedule->status,
            ]);

            return $schedule;
        });

        return redirect()->route('admin.schedules.index', ['academic_year_id' => $data['academic_year_id']])
            ->with('success', $schedule->isTba()
                ? 'Schedule created as TBA. Assign a faculty member when ready.'
                : 'Schedule created and faculty assigned.');
    }

    /**
     * GET /admin/schedules/{schedule}/edit
     */
    public function edit(Schedule $schedule)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $yearId        = $schedule->academic_year_id;
        $sections      = Section::where('academic_year_id', $yearId)->active()->orderBy('grade_level')->orderBy('section_name')->get();
        $classrooms    = Classroom::forYear($yearId)->active()->orderBy('room_name')->get();
        $faculty       = User::where('role_id', '02')->where('status', 'active')->orderBy('last_name')->get();

        $section = Section::find($schedule->section_id);
        $subjects = Subject::query()
            ->where('status', 'active')
            ->where(function ($q) use ($section) {
                $q->where('year_level', $section?->grade_level)
                  ->orWhereNull('year_level');
            })
            ->orderBy('subject_name')->get();

        return view('admin.schedules.edit', compact(
            'schedule', 'academicYears', 'sections', 'classrooms', 'faculty', 'subjects', 'yearId'
        ));
    }

    /**
     * PUT /admin/schedules/{schedule}
     */
    public function update(Request $request, Schedule $schedule)
    {
        $data = $this->validatePayload($request);

        $errors = $this->conflicts->check($data, ignoreId: $schedule->id);
        if (!empty($errors)) {
            return back()->withInput()->withErrors(['conflict' => $errors]);
        }

        DB::transaction(function () use ($schedule, $data) {
            $before = $schedule->only([
                'section_id','subject_id','faculty_id','classroom_id','status','schedule_days','start_time','end_time'
            ]);

            $hasFaculty = !empty($data['faculty_id']);

            $schedule->update([
                'academic_year_id' => $data['academic_year_id'],
                'section_id'       => $data['section_id'],
                'subject_id'       => $data['subject_id'],
                'classroom_id'     => $data['classroom_id'] ?? null,
                'faculty_id'       => $hasFaculty ? $data['faculty_id'] : null,
                'schedule_days'    => $data['schedule_days'],
                'start_time'       => $data['start_time'],
                'end_time'         => $data['end_time'],
                'status'           => $hasFaculty ? 'assigned' : 'tba',
            ]);

            if ($hasFaculty) {
                $schedule->section_subject_id = $this->syncSectionSubject($schedule)->id;
                $schedule->save();
            } else {
                $schedule->section_subject_id = null;
                $schedule->save();
            }

            AuditLog::record('SCHEDULE_UPDATED', [
                'schedule_id' => $schedule->id,
                'before'      => $before,
                'after'       => $schedule->only([
                    'section_id','subject_id','faculty_id','classroom_id','status','schedule_days','start_time','end_time'
                ]),
            ]);
        });

        return redirect()->route('admin.schedules.index', ['academic_year_id' => $data['academic_year_id']])
            ->with('success', 'Schedule updated.');
    }

    /**
     * DELETE /admin/schedules/{schedule}
     */
    public function destroy(Schedule $schedule)
    {
        $yearId = $schedule->academic_year_id;

        AuditLog::record('SCHEDULE_DELETED', [
            'schedule_id' => $schedule->id,
            'snapshot'    => $schedule->only([
                'section_id','subject_id','faculty_id','classroom_id','status','schedule_days','start_time','end_time'
            ]),
        ]);

        $schedule->delete();

        return redirect()->route('admin.schedules.index', ['academic_year_id' => $yearId])
            ->with('success', 'Schedule removed.');
    }

    /**
     * POST /admin/schedules/{schedule}/assign-faculty
     *
     * Dedicated endpoint for "promote a TBA into an assigned schedule"
     * without going through the full edit form. Runs only the faculty
     * conflict check (room/days/times already fixed at this point).
     */
    public function assignFaculty(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'faculty_id' => ['required', 'exists:users,id'],
        ]);

        $checkPayload = [
            'academic_year_id' => $schedule->academic_year_id,
            'faculty_id'       => $data['faculty_id'],
            'schedule_days'    => $schedule->schedule_days,
            'start_time'       => $schedule->start_time,
            'end_time'         => $schedule->end_time,
        ];

        $errors = $this->conflicts->check($checkPayload, ignoreId: $schedule->id);
        if (!empty($errors)) {
            return back()->withErrors(['conflict' => $errors]);
        }

        DB::transaction(function () use ($schedule, $data) {
            $schedule->update([
                'faculty_id' => $data['faculty_id'],
                'status'     => 'assigned',
            ]);

            $schedule->section_subject_id = $this->syncSectionSubject($schedule)->id;
            $schedule->save();

            AuditLog::record('SCHEDULE_FACULTY_ASSIGNED', [
                'schedule_id' => $schedule->id,
                'faculty_id'  => $schedule->faculty_id,
            ]);
        });

        return back()->with('success', 'Faculty assigned to schedule.');
    }

    // ──────────────────────────────────────────────────────────────────────

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'section_id'       => ['required', 'exists:sections,id'],
            'subject_id'       => ['required', 'exists:subjects,id'],
            'classroom_id'     => ['nullable', 'exists:classrooms,id'],
            'faculty_id'       => ['nullable', 'exists:users,id'],
            'schedule_days'    => ['required', 'array', 'min:1'],
            'schedule_days.*'  => [Rule::in(['monday','tuesday','wednesday','thursday','friday','saturday'])],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
    }

    /**
     * Ensure a matching section_subjects row exists so the rest of the system
     * (grading, class lists, attendance) sees the assignment.
     *
     * Same section + subject + year is unique on section_subjects, so we use
     * updateOrCreate to handle the "schedule already had a faculty, but the
     * registrar changed it" case cleanly.
     */
    private function syncSectionSubject(Schedule $schedule): SectionSubject
    {
        return SectionSubject::updateOrCreate(
            [
                'section_id'       => $schedule->section_id,
                'subject_id'       => $schedule->subject_id,
                'academic_year_id' => $schedule->academic_year_id,
            ],
            [
                'faculty_id'    => $schedule->faculty_id,
                'room'          => $schedule->classroom?->room_name,
                'schedule_days' => $schedule->schedule_days,
                'start_time'    => $schedule->start_time,
                'end_time'      => $schedule->end_time,
            ]
        );
    }
}
