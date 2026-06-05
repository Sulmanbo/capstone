<?php

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Models\Subject;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();

    $this->year = AcademicYear::create([
        'year_label' => '2025-2026',
        'start_date' => '2025-06-01',
        'end_date'   => '2026-03-31',
        'term_type'  => 'quarterly',
        'status'     => 'active',
    ]);

    $this->section = Section::create([
        'section_name'     => 'St. Joseph',
        'grade_level'      => 'Grade 7',
        'academic_year_id' => $this->year->id,
        'status'           => 'active',
    ]);

    $this->subject = Subject::create([
        'subject_id'   => 'SUBJ-MTH7',
        'subject_code' => 'MTH7',
        'subject_name' => 'Mathematics 7',
        'year_level'   => 'Grade 7',
    ]);

    $this->faculty = User::factory()->faculty()->create();

    $this->room = Classroom::create([
        'academic_year_id' => $this->year->id,
        'room_name'        => 'Room 101',
        'capacity'         => 40,
        'status'           => 'active',
    ]);
});

// ── TBA creation ────────────────────────────────────────────────────────────

test('admin can create a schedule without assigning faculty (TBA)', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->subject->id,
        'classroom_id'     => $this->room->id,
        // faculty_id intentionally omitted
        'schedule_days'    => ['monday', 'wednesday'],
        'start_time'       => '08:00',
        'end_time'         => '10:00',
    ]);

    $response->assertRedirect();
    $schedule = Schedule::first();
    expect($schedule)->not->toBeNull();
    expect($schedule->status)->toBe('tba');
    expect($schedule->faculty_id)->toBeNull();
});

test('TBA schedule creation logs SCHEDULE_CREATED audit entry', function () {
    $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->subject->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00',
        'end_time'         => '10:00',
    ]);

    expect(AuditLog::where('action_type', 'SCHEDULE_CREATED')->exists())->toBeTrue();
});

// ── Assigning faculty later ─────────────────────────────────────────────────

test('admin can assign faculty to a TBA schedule via the dedicated action', function () {
    $schedule = Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->subject->id,
        'classroom_id'     => $this->room->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'tba',
    ]);

    $this->actingAs($this->admin)->post(route('admin.schedules.assign-faculty', $schedule), [
        'faculty_id' => $this->faculty->id,
    ])->assertRedirect();

    $schedule->refresh();
    expect($schedule->faculty_id)->toBe($this->faculty->id);
    expect($schedule->status)->toBe('assigned');

    // section_subjects row should have been created so grading still works
    expect(SectionSubject::where('section_id', $this->section->id)
        ->where('subject_id', $this->subject->id)
        ->where('academic_year_id', $this->year->id)
        ->exists())->toBeTrue();
});

test('assigning faculty with a time conflict is rejected', function () {
    // First schedule: faculty has Mon 8-10am on a Math section
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->subject->id,
        'faculty_id'       => $this->faculty->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'assigned',
    ]);

    // Second schedule: TBA, same time slot, different section
    $section2 = Section::create([
        'section_name'     => 'St. Mary',
        'grade_level'      => 'Grade 7',
        'academic_year_id' => $this->year->id,
        'status'           => 'active',
    ]);
    $subject2 = Subject::create([
        'subject_id'   => 'SUBJ-ENG7',
        'subject_code' => 'ENG7',
        'subject_name' => 'English 7',
        'year_level'   => 'Grade 7',
    ]);
    $tba = Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $section2->id,
        'subject_id'       => $subject2->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'tba',
    ]);

    $this->actingAs($this->admin)->post(route('admin.schedules.assign-faculty', $tba), [
        'faculty_id' => $this->faculty->id,
    ])->assertSessionHasErrors('conflict');

    $tba->refresh();
    expect($tba->faculty_id)->toBeNull();
    expect($tba->status)->toBe('tba');
});

// ── Conflict rejection on create ────────────────────────────────────────────

test('store rejects when chosen room is already booked at overlapping time', function () {
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->subject->id,
        'classroom_id'     => $this->room->id,
        'faculty_id'       => $this->faculty->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'assigned',
    ]);

    $section2 = Section::create([
        'section_name'     => 'St. Mary',
        'grade_level'      => 'Grade 7',
        'academic_year_id' => $this->year->id,
        'status'           => 'active',
    ]);
    $subject2 = Subject::create([
        'subject_id'   => 'SUBJ-SCI7',
        'subject_code' => 'SCI7',
        'subject_name' => 'Science 7',
        'year_level'   => 'Grade 7',
    ]);

    $response = $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
        'academic_year_id' => $this->year->id,
        'section_id'       => $section2->id,
        'subject_id'       => $subject2->id,
        'classroom_id'     => $this->room->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '09:00',
        'end_time'         => '11:00',
    ]);

    $response->assertSessionHasErrors('conflict');
    expect(Schedule::count())->toBe(1); // the second one was rejected
});

test('store rejects when schedule is shorter than the minimum duration', function () {
    $this->actingAs($this->admin)->post(route('admin.schedules.store'), [
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->subject->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00',
        'end_time'         => '08:30', // 30 minutes
    ])->assertSessionHasErrors('conflict');

    expect(Schedule::count())->toBe(0);
});

// ── Unique-subject-per-section DB constraint ───────────────────────────────

test('cannot schedule the same subject twice in the same section in the same year', function () {
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->subject->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'tba',
    ]);

    // Same section + same subject + same year — should violate the unique constraint
    $thrown = false;
    try {
        Schedule::create([
            'academic_year_id' => $this->year->id,
            'section_id'       => $this->section->id,
            'subject_id'       => $this->subject->id,
            'schedule_days'    => ['tuesday'],
            'start_time'       => '08:00:00',
            'end_time'         => '10:00:00',
            'status'           => 'tba',
        ]);
    } catch (\Illuminate\Database\QueryException $e) {
        $thrown = true;
    }

    expect($thrown)->toBeTrue();
});

// ── Cascading dropdown AJAX endpoint ───────────────────────────────────────

test('subjects-for-section endpoint returns only year-level-matching subjects', function () {
    $g8Subject = Subject::create([
        'subject_id'   => 'SUBJ-MTH8',
        'subject_code' => 'MTH8',
        'subject_name' => 'Mathematics 8',
        'year_level'   => 'Grade 8',
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.schedules.subjects-for-section', $this->section)); // section is Grade 7

    $response->assertOk();
    $codes = collect($response->json())->pluck('subject_code')->toArray();

    expect($codes)->toContain('MTH7');
    expect($codes)->not->toContain('MTH8');
});
