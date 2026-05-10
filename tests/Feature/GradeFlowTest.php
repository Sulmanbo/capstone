<?php

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\GradeUnlockRequest;
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
        'subject_id'   => 'SUBJ-MATH7',
        'subject_name' => 'Mathematics',
        'subject_code' => 'MATH7',
    ]);

    $this->section = Section::create([
        'section_name'     => 'St. Therese',
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

    $this->registrar = User::factory()->registrar()->create();
});

// ── Draft ──────────────────────────────────────────────────────────────────────

test('faculty can save draft grades', function () {
    $this->actingAs($this->faculty)
         ->post(route('faculty.gradebook.save-draft', $this->ss), [
             'grades' => [
                 $this->enrollment->id => [
                     'written_work'         => 85,
                     'performance_task'     => 90,
                     'quarterly_assessment' => 88,
                 ],
             ],
         ])->assertRedirect(route('faculty.gradebook.show', $this->ss));

    $this->assertDatabaseHas('grades', [
        'section_subject_id' => $this->ss->id,
        'enrollment_id'      => $this->enrollment->id,
        'status'             => 'draft',
    ]);
});

test('another faculty cannot save draft for a class they do not own', function () {
    $other = User::factory()->faculty()->create();

    $this->actingAs($other)
         ->post(route('faculty.gradebook.save-draft', $this->ss), [
             'grades' => [$this->enrollment->id => ['written_work' => 80]],
         ])->assertStatus(403);
});

// ── Submit ─────────────────────────────────────────────────────────────────────

test('faculty can submit draft grades', function () {
    Grade::create([
        'section_subject_id'   => $this->ss->id,
        'grading_quarter_id'   => $this->quarter->id,
        'enrollment_id'        => $this->enrollment->id,
        'written_work'         => 85,
        'performance_task'     => 90,
        'quarterly_assessment' => 88,
        'final_grade'          => 87,
        'status'               => 'draft',
    ]);

    $this->actingAs($this->faculty)
         ->post(route('faculty.gradebook.submit', $this->ss))
         ->assertRedirect(route('faculty.gradebook.show', $this->ss));

    $this->assertDatabaseHas('grades', [
        'section_subject_id' => $this->ss->id,
        'enrollment_id'      => $this->enrollment->id,
        'status'             => 'submitted',
    ]);
});

test('faculty cannot submit when no complete draft grades exist', function () {
    $this->actingAs($this->faculty)
         ->post(route('faculty.gradebook.submit', $this->ss))
         ->assertStatus(422);
});

// ── Finalize ───────────────────────────────────────────────────────────────────

test('registrar can finalize submitted grades and students are notified', function () {
    Notification::fake();

    Grade::create([
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'enrollment_id'      => $this->enrollment->id,
        'final_grade'        => 87,
        'status'             => 'submitted',
        'submitted_at'       => now(),
        'submitted_by'       => $this->faculty->id,
    ]);

    $this->actingAs($this->registrar)
         ->post(route('registrar.gradebook.finalize', $this->ss))
         ->assertRedirect();

    $this->assertDatabaseHas('grades', [
        'section_subject_id' => $this->ss->id,
        'status'             => 'finalized',
    ]);

    Notification::assertSentTo($this->student, \App\Notifications\GradeFinalizedNotification::class);
});

// ── Lock ───────────────────────────────────────────────────────────────────────

test('registrar can lock finalized grades and students are notified', function () {
    Notification::fake();

    Grade::create([
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'enrollment_id'      => $this->enrollment->id,
        'final_grade'        => 87,
        'status'             => 'finalized',
    ]);

    $this->actingAs($this->registrar)
         ->post(route('registrar.grade-lock.lock-section', $this->ss))
         ->assertRedirect(route('registrar.grade-lock.index'));

    $this->assertDatabaseHas('grades', [
        'section_subject_id' => $this->ss->id,
        'status'             => 'locked',
    ]);

    Notification::assertSentTo($this->student, \App\Notifications\GradeLockedNotification::class);
});

// ── Unlock request / approve / deny ───────────────────────────────────────────

test('faculty can request unlock for locked grades', function () {
    Notification::fake();

    $this->actingAs($this->faculty)
         ->post(route('faculty.gradebook.request-unlock', $this->ss), [
             'reason' => 'Student grade entry error — needs correction.',
         ])->assertRedirect(route('faculty.gradebook.show', $this->ss));

    $this->assertDatabaseHas('grade_unlock_requests', [
        'section_subject_id' => $this->ss->id,
        'status'             => 'pending',
        'requested_by'       => $this->faculty->id,
    ]);

    Notification::assertSentTo($this->registrar, \App\Notifications\UnlockRequestedNotification::class);
});

test('registrar can approve an unlock request and faculty is notified', function () {
    Notification::fake();

    Grade::create([
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'enrollment_id'      => $this->enrollment->id,
        'final_grade'        => 87,
        'status'             => 'locked',
    ]);

    $req = GradeUnlockRequest::create([
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'requested_by'       => $this->faculty->id,
        'reason'             => 'Grade entry error.',
        'status'             => 'pending',
    ]);

    $this->actingAs($this->registrar)
         ->post(route('registrar.grade-lock.approve', $req))
         ->assertRedirect(route('registrar.grade-lock.index'));

    expect($req->fresh()->status)->toBe('approved');
    $this->assertDatabaseHas('grades', ['status' => 'finalized']);
    Notification::assertSentTo($this->faculty, \App\Notifications\UnlockDecidedNotification::class);
});

test('registrar can deny an unlock request and faculty is notified', function () {
    Notification::fake();

    $req = GradeUnlockRequest::create([
        'section_subject_id' => $this->ss->id,
        'grading_quarter_id' => $this->quarter->id,
        'requested_by'       => $this->faculty->id,
        'reason'             => 'Grade entry error.',
        'status'             => 'pending',
    ]);

    $this->actingAs($this->registrar)
         ->post(route('registrar.grade-lock.deny', $req), [
             'review_notes' => 'Grades have already been reported to DepEd.',
         ])->assertRedirect(route('registrar.grade-lock.index'));

    expect($req->fresh()->status)->toBe('denied');
    Notification::assertSentTo($this->faculty, \App\Notifications\UnlockDecidedNotification::class);
});
