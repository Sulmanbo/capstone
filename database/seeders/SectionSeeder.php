<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Creates 5 sections per grade level (Grade 7 – Grade 12) for the active
 * (or most-recent) academic year. Idempotent: uses firstOrCreate so running
 * it multiple times is safe.
 *
 * Run: php artisan db:seed --class=SectionSeeder
 */
class SectionSeeder extends Seeder
{
    private const SECTIONS_PER_GRADE = [
        'St. Joseph',
        'St. Mary',
        'St. Michael',
        'St. Peter',
        'St. Paul',
    ];

    private const GRADE_LEVELS = [
        'Grade 7',
        'Grade 8',
        'Grade 9',
        'Grade 10',
        'Grade 11',
        'Grade 12',
    ];

    public function run(): void
    {
        $ay = AcademicYear::where('status', 'active')->first()
            ?? AcademicYear::orderByDesc('start_date')->first();

        if (! $ay) {
            $this->command->warn('No academic year found. Creating a default 2025-2026 year.');
            $ay = AcademicYear::create([
                'year_label' => '2025-2026',
                'start_date' => '2025-06-02',
                'end_date'   => '2026-04-03',
                'status'     => 'active',
                'is_active'  => true,
            ]);
        }

        $this->command->info("Seeding sections for academic year: {$ay->year_label}");

        // Pick a default adviser (first active faculty, if any)
        $defaultAdviser = User::where('role_id', '02')->where('status', 'active')->first();

        $created = 0;
        $skipped = 0;

        foreach (self::GRADE_LEVELS as $gradeLevel) {
            foreach (self::SECTIONS_PER_GRADE as $sectionName) {
                [$section, $wasCreated] = [
                    Section::firstOrCreate(
                        [
                            'academic_year_id' => $ay->id,
                            'grade_level'      => $gradeLevel,
                            'section_name'     => $sectionName,
                        ],
                        [
                            'adviser_id' => $defaultAdviser?->id,
                            'capacity'   => 40,
                            'status'     => 'active',
                        ]
                    ),
                    false,
                ];

                // firstOrCreate doesn't return wasCreated, check via wasRecentlyCreated
                if ($section->wasRecentlyCreated) {
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }

        $total = count(self::GRADE_LEVELS) * count(self::SECTIONS_PER_GRADE);
        $this->command->info("Done: {$created} created, {$skipped} already existed (target: {$total}).");
    }
}
