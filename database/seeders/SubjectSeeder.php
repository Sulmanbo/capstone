<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

/**
 * Seeds a realistic set of subjects per grade level (Grade 7 – Grade 12).
 *
 * The schedule form filters subjects by `year_level` matching the section's
 * grade level, so each subject is tagged with its grade level and given a
 * grade-suffixed unique code (e.g. ENG7, MATH11).
 *
 * Idempotent: uses firstOrCreate keyed on subject_code, so it is safe to
 * run multiple times. Grade weights are left NULL (the system falls back to
 * the global config weights).
 *
 * Run: php artisan db:seed --class=SubjectSeeder
 */
class SubjectSeeder extends Seeder
{
    /**
     * Junior High School (Grade 7–10) core subjects — DepEd K-12.
     * [code prefix, name, credits]
     */
    private const JHS_SUBJECTS = [
        ['ENG',   'English',                             3],
        ['FIL',   'Filipino',                            3],
        ['MATH',  'Mathematics',                         4],
        ['SCI',   'Science',                             4],
        ['AP',    'Araling Panlipunan',                  3],
        ['ESP',   'Edukasyon sa Pagpapakatao',           2],
        ['TLE',   'Technology and Livelihood Education', 3],
        ['MAPEH', 'MAPEH',                               3],
    ];

    /**
     * Senior High School (Grade 11–12) core subjects — DepEd K-12.
     */
    private const SHS_SUBJECTS = [
        ['ORALCOM', 'Oral Communication',                          3],
        ['KOMFIL',  'Komunikasyon sa Akademikong Filipino',        3],
        ['GENMATH', 'General Mathematics',                          4],
        ['EARTHSCI','Earth and Life Science',                       4],
        ['PHILO',   'Introduction to the Philosophy of the Human', 3],
        ['PE',      'Physical Education and Health',                2],
        ['PRACRES', 'Practical Research',                           3],
        ['CONTEMP', 'Contemporary Philippine Arts',                 2],
    ];

    public function run(): void
    {
        $created = 0;
        $skipped = 0;

        // Grade 7–10 → JHS subjects
        foreach (['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'] as $grade) {
            $gradeNum = preg_replace('/\D/', '', $grade);
            foreach (self::JHS_SUBJECTS as [$prefix, $name, $credits]) {
                $this->make("{$prefix}{$gradeNum}", $name, $grade, $credits, $created, $skipped);
            }
        }

        // Grade 11–12 → SHS subjects
        foreach (['Grade 11', 'Grade 12'] as $grade) {
            $gradeNum = preg_replace('/\D/', '', $grade);
            foreach (self::SHS_SUBJECTS as [$prefix, $name, $credits]) {
                $this->make("{$prefix}{$gradeNum}", $name, $grade, $credits, $created, $skipped);
            }
        }

        $this->command->info("SubjectSeeder done: {$created} created, {$skipped} already existed.");
    }

    private function make(string $code, string $name, string $grade, int $credits, int &$created, int &$skipped): void
    {
        $subject = Subject::firstOrCreate(
            ['subject_code' => $code],
            [
                'subject_name' => $name,
                'year_level'   => $grade,
                'description'  => "DepEd K-12 — {$name} ({$grade})",
                'credits'      => $credits,
                'status'       => 'active',
            ]
        );

        $subject->wasRecentlyCreated ? $created++ : $skipped++;
    }
}
