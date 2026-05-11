<?php

use App\Models\AcademicYear;
use App\Models\CurriculumMapping;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;

it('blocks enrollment when prerequisites are unmet', function () {
    $registrar = User::factory()->registrar()->create();
    $student   = User::factory()->student()->create();
    $year      = AcademicYear::create([
        'year_label' => '2025-2026',
        'status'     => 'active',
        'start_date' => '2025-06-01',
        'end_date'   => '2026-03-31',
    ]);
    $section = Section::create([
        'section_name'     => 'St. Therese',
        'grade_level'      => 'Grade 8',
        'academic_year_id' => $year->id,
        'status'           => 'active',
    ]);
    $math7 = Subject::create(['subject_name' => 'Math 7', 'subject_code' => 'MATH7']);
    $math8 = Subject::create(['subject_name' => 'Math 8', 'subject_code' => 'MATH8']);

    // Math 8 requires Math 7 with min grade 75
    CurriculumMapping::create([
        'academic_year_id'        => $year->id,
        'grade_level'             => 'Grade 8',
        'subject_id'              => $math8->id,
        'prerequisite_subject_id' => $math7->id,
        'prerequisite_min_grade'  => 75,
        'status'                  => 'active',
    ]);

    $response = $this->actingAs($registrar)->post(route('registrar.enroll'), [
        'student_id'       => $student->id,
        'grade_level'      => 'Grade 8',
        'section_id'       => $section->id,
        'academic_year_id' => $year->id,
    ]);

    expect($response->getSession()->get('errors')->first('enrollment'))
        ->toContain('Unmet prerequisites');
    expect(Enrollment::where('student_id', $student->id)->exists())->toBeFalse();
});

it('allows enrollment when prerequisites are met', function () {
    $registrar = User::factory()->registrar()->create();
    $student   = User::factory()->student()->create();
    $year      = AcademicYear::create([
        'year_label' => '2025-2026-met',
        'status'     => 'active',
        'start_date' => '2025-06-01',
        'end_date'   => '2026-03-31',
    ]);
    $section = Section::create([
        'section_name'     => 'St. Joseph',
        'grade_level'      => 'Grade 7',
        'academic_year_id' => $year->id,
        'status'           => 'active',
    ]);

    // No prerequisites for Grade 7 → enrollment should succeed
    $response = $this->actingAs($registrar)->post(route('registrar.enroll'), [
        'student_id'       => $student->id,
        'grade_level'      => 'Grade 7',
        'section_id'       => $section->id,
        'academic_year_id' => $year->id,
    ]);

    expect(Enrollment::where('student_id', $student->id)->exists())->toBeTrue();
});
