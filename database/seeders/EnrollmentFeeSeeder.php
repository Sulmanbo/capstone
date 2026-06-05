<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\EnrollmentFee;
use Illuminate\Database\Seeder;

/**
 * EnrollmentFeeSeeder
 *
 * Seeds placeholder enrollment fees for each grade level under every existing
 * academic year. Safe to run repeatedly — uses updateOrCreate.
 *
 * Edit the amounts here, or change them in the registrar UI at /admin/payments/fees.
 *
 * Run with: php artisan db:seed --class=EnrollmentFeeSeeder
 */
class EnrollmentFeeSeeder extends Seeder
{
    public function run(): void
    {
        $placeholderFees = [
            'Grade 7'  => 25000.00,
            'Grade 8'  => 26000.00,
            'Grade 9'  => 27000.00,
            'Grade 10' => 28000.00,
        ];

        $years = AcademicYear::all();
        if ($years->isEmpty()) {
            $this->command->warn('No academic years exist yet — create one first, then re-run this seeder.');
            return;
        }

        $count = 0;
        foreach ($years as $year) {
            foreach ($placeholderFees as $gradeLevel => $amount) {
                EnrollmentFee::updateOrCreate(
                    ['academic_year_id' => $year->id, 'grade_level' => $gradeLevel],
                    ['amount' => $amount, 'currency' => 'PHP'],
                );
                $count++;
            }
        }

        $this->command->info("Seeded {$count} enrollment-fee rows across {$years->count()} academic year(s).");
    }
}
