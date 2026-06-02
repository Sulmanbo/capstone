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
    /**
     * Unique section names per grade level. Each grade level gets its own
     * themed set of 5 names so no two grade levels share section names.
     */
    private const SECTIONS_BY_GRADE = [
        'Grade 7'  => ['St. Joseph', 'St. Mary', 'St. Michael', 'St. Peter', 'St. Paul'],
        'Grade 8'  => ['Sampaguita', 'Gumamela', 'Rosal', 'Camia', 'Ilang-Ilang'],
        'Grade 9'  => ['Emerald', 'Sapphire', 'Ruby', 'Diamond', 'Topaz'],
        'Grade 10' => ['Newton', 'Einstein', 'Galileo', 'Darwin', 'Tesla'],
        'Grade 11' => ['Mabini', 'Rizal', 'Bonifacio', 'Luna', 'Aguinaldo'],
        'Grade 12' => ['Aristotle', 'Plato', 'Socrates', 'Descartes', 'Kant'],
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

        foreach (self::SECTIONS_BY_GRADE as $gradeLevel => $sectionNames) {
            foreach ($sectionNames as $sectionName) {
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

        $total = array_sum(array_map('count', self::SECTIONS_BY_GRADE));
        $this->command->info("Done: {$created} created, {$skipped} already existed (target: {$total}).");
    }
}
