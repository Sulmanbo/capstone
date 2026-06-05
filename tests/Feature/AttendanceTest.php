<?php

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Models\Subject;
use App\Models\User;

// ── Shared setup ──────────────────────────────────────────────────────────────

beforeEach(function () {
    $this->year = AcademicYear::create([
        'year_label' => '2025-2026',
        'start_date' => '2025-06-01',
        'end_date'   => '2026-03-31',
        'status'     => 'active',
    ]);

    $subject = Subject::create([
        'subject_id'   => 'SUBJ-ENG7',
        'subject_name' => 'English',
        'subject_code' => 'ENG7',
    ]);

    $this->section = Section::create([
        'section_name'     => 'St. Joseph',
        'grade_level'      => 'Grade 7',
        'academic_year_id' => $this->year->id,
        'status'           => 'active',
    ]);

    $this->faculty = User::factory()->faculty()->create();

    $this->ss = SectionSubject::create([
        'section_id'       => $this->section->id,
        'subject_id'       => $subject->id,
        'faculty_id'       => $this->faculty->id,
        'academic_year_id' => $this->year->id,
        'schedule_days'    => json_encode(['monday', 'wednesday', 'friday']),
        'start_time'       => '07:00:00',
        'end_time'         => '08:00:00',
    ]);

    $this->student    = User::factory()->student()->create();
    $this->enrollment = Enrollment::create([
        'student_id'       => $this->student->id,
        'section_id'       => $this->section->id,
        'academic_year_id' => $this->year->id,
        'status'           => 'enrolled',
    ]);
});

// ── Happy path ──────────────────────────────────────────────────────────────

test('faculty can record attendance for their assigned class', function () {
    $date = now()->toDateString();

    $this->actingAs($this->faculty)
         ->post(route('faculty.attendance.store'), [
             'section_subject_id' => $this->ss->id,
             'date'               => $date,
             'attendance'         => [
                 [
                     'enrollment_id' => $this->enrollment->id,
                     'status'        => 'present',
                     'remarks'       => null,
                 ],
             ],
         ])
         ->assertRedirect();

    $this->assertDatabaseHas('attendance', [
        'enrollment_id'      => $this->enrollment->id,
        'section_subject_id' => $this->ss->id,
        'date'               => $date,
        'status'             => 'present',
        'recorded_by'        => $this->faculty->id,
    ]);
});

test('attendance creation writes an ATTENDANCE_RECORDED audit entry', function () {
    $date = now()->toDateString();

    $this->actingAs($this->faculty)
         ->post(route('faculty.attendance.store'), [
             'section_subject_id' => $this->ss->id,
             'date'               => $date,
             'attendance'         => [
                 ['enrollment_id' => $this->enrollment->id, 'status' => 'late'],
             ],
         ]);

    expect(AuditLog::where('action_type', AuditLog::ATTENDANCE_RECORDED)->exists())->toBeTrue();
});

test('re-saving attendance for the same date updates the existing row and audits as updated', function () {
    $date = now()->toDateString();

    // First save: present
    $this->actingAs($this->faculty)
         ->post(route('faculty.attendance.store'), [
             'section_subject_id' => $this->ss->id,
             'date'               => $date,
             'attendance' => [['enrollment_id' => $this->enrollment->id, 'status' => 'present']],
         ]);

    // Second save: changed to absent
    $this->actingAs($this->faculty)
         ->post(route('faculty.attendance.store'), [
             'section_subject_id' => $this->ss->id,
             'date'               => $date,
             'attendance' => [['enrollment_id' => $this->enrollment->id, 'status' => 'absent', 'remarks' => 'sick']],
         ]);

    expect(Attendance::where('enrollment_id', $this->enrollment->id)->count())->toBe(1);
    expect(Attendance::where('enrollment_id', $this->enrollment->id)->first()->status)->toBe('absent');
    expect(Attendance::where('enrollment_id', $this->enrollment->id)->first()->remarks)->toBe('sick');
    expect(AuditLog::where('action_type', AuditLog::ATTENDANCE_UPDATED)->exists())->toBeTrue();
});

// ── Authorization failure ───────────────────────────────────────────────────

test('another faculty cannot record attendance for a class they do not own', function () {
    $other = User::factory()->faculty()->create();

    $this->actingAs($other)
         ->post(route('faculty.attendance.store'), [
             'section_subject_id' => $this->ss->id,
             'date'               => now()->toDateString(),
             'attendance' => [['enrollment_id' => $this->enrollment->id, 'status' => 'present']],
         ])
         ->assertStatus(403);

    $this->assertDatabaseMissing('attendance', [
        'enrollment_id'      => $this->enrollment->id,
        'section_subject_id' => $this->ss->id,
    ]);
});

test('students cannot reach the attendance recording endpoint', function () {
    $this->actingAs($this->student)
         ->post(route('faculty.attendance.store'), [
             'section_subject_id' => $this->ss->id,
             'date'               => now()->toDateString(),
             'attendance' => [['enrollment_id' => $this->enrollment->id, 'status' => 'present']],
         ])
         ->assertStatus(403);
});

// ── Validation ──────────────────────────────────────────────────────────────

test('attendance for a future date is rejected', function () {
    $this->actingAs($this->faculty)
         ->post(route('faculty.attendance.store'), [
             'section_subject_id' => $this->ss->id,
             'date'               => now()->addDay()->toDateString(),
             'attendance' => [['enrollment_id' => $this->enrollment->id, 'status' => 'present']],
         ])
         ->assertSessionHasErrors('date');
});

test('invalid status value is rejected', function () {
    $this->actingAs($this->faculty)
         ->post(route('faculty.attendance.store'), [
             'section_subject_id' => $this->ss->id,
             'date'               => now()->toDateString(),
             'attendance' => [['enrollment_id' => $this->enrollment->id, 'status' => 'maybe']],
         ])
         ->assertSessionHasErrors('attendance.0.status');
});
