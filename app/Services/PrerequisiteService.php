<?php

namespace App\Services;

use App\Models\CurriculumMapping;
use App\Models\Grade;
use App\Models\User;

class PrerequisiteService
{
    /**
     * Return an array of unmet prerequisites for a student enrolling at a grade level.
     *
     * Each entry:
     *   subject          — the subject the student wants to take
     *   requires         — the prerequisite subject name
     *   min_grade        — minimum grade required
     *   student_grade    — the student's best recorded grade for the prereq (null = never taken)
     *   passed           — always false (only unmet are returned)
     */
    public function getUnmet(User $student, string $targetGradeLevel, int $targetAcademicYearId): array
    {
        $mappings = CurriculumMapping::where('grade_level', $targetGradeLevel)
            ->where('academic_year_id', $targetAcademicYearId)
            ->where('status', 'active')
            ->whereNotNull('prerequisite_subject_id')
            ->with(['subject', 'prerequisiteSubject'])
            ->get();

        $unmet = [];

        foreach ($mappings as $mapping) {
            $minGrade = $mapping->prerequisite_min_grade ?? 75.0;

            // Find the student's best finalized/locked grade for the prerequisite subject
            // in any academic year (prior enrollment)
            $bestGrade = Grade::whereHas(
                    'enrollment',
                    fn($q) => $q->where('student_id', $student->id)
                )
                ->whereHas(
                    'sectionSubject',
                    fn($q) => $q->where('subject_id', $mapping->prerequisite_subject_id)
                )
                ->whereIn('status', ['finalized', 'locked'])
                ->whereNotNull('final_grade')
                ->max('final_grade');

            if ($bestGrade === null || $bestGrade < $minGrade) {
                $unmet[] = [
                    'subject'       => $mapping->subject?->subject_name ?? 'Unknown',
                    'requires'      => $mapping->prerequisiteSubject?->subject_name ?? 'Unknown',
                    'min_grade'     => $minGrade,
                    'student_grade' => $bestGrade,
                    'passed'        => false,
                ];
            }
        }

        return $unmet;
    }

    /**
     * Convenience wrapper: returns true if the student has all prerequisites met.
     */
    public function allMet(User $student, string $targetGradeLevel, int $targetAcademicYearId): bool
    {
        return empty($this->getUnmet($student, $targetGradeLevel, $targetAcademicYearId));
    }
}
