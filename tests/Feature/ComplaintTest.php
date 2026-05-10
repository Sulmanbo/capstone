<?php

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeComplaint;
use App\Models\GradingQuarter;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

// ── Shared setup ──────────────────────────────────────────────────────────────

beforeEach(function () {
    $this->year = AcademicYear::create([
        'year_label' => '2025-2026',
        'start_date' => '2025-06-01',
        'end_date'   => '2026-03-31',
        'status'     => 'active',
    ]);

    $this->quarter = GradingQuarter::create([
        'academic_year_id' => $this->year->id,
        'quarter_number'   => 1,
        'quarter_name'     => '1st Quarter',
        'start_date'       => '2025-06-01',
        'end_date'         => '2025-08-31',
        'status'           => 'active',
    ]);

    $subject = Subject::create([
        'subject_id'   => 'SUBJ-SCI7',
        'subject_name' => 'Science',
        'subject_code' => 'SCI7',
    ]);

    $this->section = Section::create([
        'section_name'     => 'Rizal',
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

    $this->student = User::factory()->student()->create();
    $this->enrollment = Enrollment::create([
        'student_id'       => $this->student->id,
        'section_id'       => $this->section->id,
        'academic_year_id' => $this->year->id,
        'status'           => 'enrolled',
    ]);

    $this->grade = Grade::create([
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'enrollment_id'      => $this->enrollment->id,
        'final_grade'        => 72,
        'status'             => 'finalized',
    ]);
});

// ── Submission ─────────────────────────────────────────────────────────────────

test('student can submit a grade complaint', function () {
    $this->actingAs($this->student)
         ->post(route('complaints.store'), [
             'section_subject_id' => $this->ss->id,
             'grading_quarter_id' => $this->quarter->id,
             'reason'             => 'My final grade does not reflect my actual performance. I believe there was a computation error.',
         ])->assertRedirect(route('complaints.index'));

    $this->assertDatabaseHas('grade_complaints', [
        'student_id'         => $this->student->id,
        'section_subject_id' => $this->ss->id,
        'status'             => 'pending',
    ]);
});

test('faculty and registrars are notified when a complaint is submitted', function () {
    Notification::fake();

    $registrar = User::factory()->registrar()->create();

    $this->actingAs($this->student)
         ->post(route('complaints.store'), [
             'section_subject_id' => $this->ss->id,
             'grading_quarter_id' => $this->quarter->id,
             'reason'             => 'My final grade does not reflect my actual performance. There was a computation error.',
         ]);

    Notification::assertSentTo($this->faculty,  \App\Notifications\ComplaintReceivedNotification::class);
    Notification::assertSentTo($registrar, \App\Notifications\ComplaintReceivedNotification::class);
});

test('student cannot file a second open complaint for the same subject and quarter', function () {
    GradeComplaint::create([
        'student_id'         => $this->student->id,
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'grade_id'           => $this->grade->id,
        'reason'             => 'First complaint already open.',
        'status'             => 'pending',
    ]);

    $this->actingAs($this->student)
         ->post(route('complaints.store'), [
             'section_subject_id' => $this->ss->id,
             'grading_quarter_id' => $this->quarter->id,
             'reason'             => 'Trying to file again for the same issue.',
         ])->assertSessionHasErrors('section_subject_id');
});

test('student cannot submit a complaint for a subject they are not enrolled in', function () {
    $otherSection = Section::create([
        'section_name'     => 'Mabini',
        'grade_level'      => 'Grade 8',
        'academic_year_id' => $this->year->id,
        'status'           => 'active',
    ]);

    $otherSs = SectionSubject::create([
        'section_id'       => $otherSection->id,
        'subject_id'       => $this->ss->subject_id,
        'faculty_id'       => $this->faculty->id,
        'academic_year_id' => $this->year->id,
        'schedule_days'    => json_encode(['tuesday', 'thursday']),
        'start_time'       => '09:00:00',
        'end_time'         => '10:00:00',
    ]);

    $this->actingAs($this->student)
         ->post(route('complaints.store'), [
             'section_subject_id' => $otherSs->id,
             'grading_quarter_id' => $this->quarter->id,
             'reason'             => 'Trying to complain about a class I am not enrolled in.',
         ])->assertStatus(403);
});

// ── Respond ────────────────────────────────────────────────────────────────────

test('faculty can respond to a complaint and student is notified', function () {
    Notification::fake();

    $complaint = GradeComplaint::create([
        'student_id'         => $this->student->id,
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'reason'             => 'I believe my grade was computed incorrectly based on my performance.',
        'status'             => 'pending',
    ]);

    $this->actingAs($this->faculty)
         ->patch(route('complaints.respond', $complaint), [
             'status'   => 'resolved',
             'response' => 'We reviewed the computation and found the grade is accurate based on recorded scores.',
         ])->assertRedirect();

    expect($complaint->fresh()->status)->toBe('resolved');
    expect($complaint->fresh()->responded_by)->toBe($this->faculty->id);
    Notification::assertSentTo($this->student, \App\Notifications\ComplaintRespondedNotification::class);
});

test('faculty cannot respond to a complaint from a different teacher subject', function () {
    $other = User::factory()->faculty()->create();

    $complaint = GradeComplaint::create([
        'student_id'         => $this->student->id,
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'reason'             => 'Grade complaint for testing faculty isolation.',
        'status'             => 'pending',
    ]);

    $this->actingAs($other)
         ->patch(route('complaints.respond', $complaint), [
             'status'   => 'resolved',
             'response' => 'Attempting to respond to another faculty member complaint.',
         ])->assertStatus(403);
});

test('student cannot access the complaint management page', function () {
    $this->actingAs($this->student)
         ->get(route('complaints.manage'))
         ->assertStatus(403);
});
