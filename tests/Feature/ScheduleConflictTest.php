<?php

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Services\ScheduleConflictService;

beforeEach(function () {
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

    $this->mathSubject = Subject::create([
        'subject_id'   => 'SUBJ-MTH7',
        'subject_code' => 'MTH7',
        'subject_name' => 'Mathematics 7',
        'year_level'   => 'Grade 7',
    ]);

    $this->engSubject = Subject::create([
        'subject_id'   => 'SUBJ-ENG7',
        'subject_code' => 'ENG7',
        'subject_name' => 'English 7',
        'year_level'   => 'Grade 7',
    ]);

    $this->faculty1 = User::factory()->faculty()->create();
    $this->faculty2 = User::factory()->faculty()->create();

    $this->room101 = Classroom::create([
        'academic_year_id' => $this->year->id,
        'room_name'        => 'Room 101',
        'capacity'         => 40,
        'status'           => 'active',
    ]);

    $this->service = app(ScheduleConflictService::class);
});

// ── Duration rule ───────────────────────────────────────────────────────────

test('rejects schedule shorter than the configured minimum', function () {
    // default min is 2.0 hours
    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '09:00:00', // only 1 hour
    ]);

    expect($errors)->not->toBeEmpty();
    expect(implode(' ', $errors))->toContain('at least 2.0 hour');
});

test('accepts schedule exactly at the minimum duration', function () {
    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00', // exactly 2 hours
    ]);
    expect($errors)->toBeEmpty();
});

test('accepts very long schedule because there is no maximum', function () {
    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '16:00:00', // 8 hours, no upper limit
    ]);
    expect($errors)->toBeEmpty();
});

test('rejects schedule where end is before start', function () {
    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '10:00:00',
        'end_time'         => '08:00:00',
    ]);
    expect($errors)->not->toBeEmpty();
});

// ── Faculty conflict ────────────────────────────────────────────────────────

test('detects faculty double-booking on overlapping times same day', function () {
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->mathSubject->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['monday', 'wednesday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'assigned',
    ]);

    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '09:00:00',
        'end_time'         => '11:00:00', // overlaps 9-10am
    ]);

    expect($errors)->not->toBeEmpty();
    expect(implode(' ', $errors))->toContain('Faculty conflict');
});

test('allows the same faculty on adjacent non-overlapping times', function () {
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->mathSubject->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'assigned',
    ]);

    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '10:00:00', // starts when previous ends
        'end_time'         => '12:00:00',
    ]);

    expect($errors)->toBeEmpty();
});

test('allows different faculty in the same time slot', function () {
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->mathSubject->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'assigned',
    ]);

    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'faculty_id'       => $this->faculty2->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
    ]);

    expect($errors)->toBeEmpty();
});

test('allows same faculty same time on different days', function () {
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->mathSubject->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'assigned',
    ]);

    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['tuesday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
    ]);

    expect($errors)->toBeEmpty();
});

// ── Room conflict ───────────────────────────────────────────────────────────

test('detects room double-booking on overlapping times same day', function () {
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->mathSubject->id,
        'classroom_id'     => $this->room101->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['friday'],
        'start_time'       => '13:00:00',
        'end_time'         => '15:00:00',
        'status'           => 'assigned',
    ]);

    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'classroom_id'     => $this->room101->id,
        'schedule_days'    => ['friday'],
        'start_time'       => '14:00:00',
        'end_time'         => '16:00:00',
    ]);

    expect($errors)->not->toBeEmpty();
    expect(implode(' ', $errors))->toContain('Room conflict');
});

// ── ignoreId behaviour (edit scenario) ─────────────────────────────────────

test('does not flag a row against itself when editing', function () {
    $existing = Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->mathSubject->id,
        'classroom_id'     => $this->room101->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['friday'],
        'start_time'       => '13:00:00',
        'end_time'         => '15:00:00',
        'status'           => 'assigned',
    ]);

    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'classroom_id'     => $this->room101->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['friday'],
        'start_time'       => '13:00:00',
        'end_time'         => '15:00:00',
    ], ignoreId: $existing->id);

    expect($errors)->toBeEmpty();
});

// ── Cancelled schedules don't block ────────────────────────────────────────

test('cancelled schedules do not produce conflicts', function () {
    Schedule::create([
        'academic_year_id' => $this->year->id,
        'section_id'       => $this->section->id,
        'subject_id'       => $this->mathSubject->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
        'status'           => 'cancelled',
    ]);

    $errors = $this->service->check([
        'academic_year_id' => $this->year->id,
        'faculty_id'       => $this->faculty1->id,
        'schedule_days'    => ['monday'],
        'start_time'       => '08:00:00',
        'end_time'         => '10:00:00',
    ]);

    expect($errors)->toBeEmpty();
});
