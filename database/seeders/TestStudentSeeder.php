<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestStudentSeeder extends Seeder
{
    public function run(): void
    {
        $activeYear = AcademicYear::where('status', 'active')->first();

        $students = [
            ['first_name' => 'Maria',   'last_name' => 'Santos',    'lrn' => '202600101', 'grade_level' => 'Grade 7'],
            ['first_name' => 'Jose',    'last_name' => 'Reyes',     'lrn' => '202600102', 'grade_level' => 'Grade 7'],
            ['first_name' => 'Ana',     'last_name' => 'Cruz',      'lrn' => '202600103', 'grade_level' => 'Grade 8'],
            ['first_name' => 'Miguel',  'last_name' => 'Garcia',    'lrn' => '202600104', 'grade_level' => 'Grade 8'],
            ['first_name' => 'Sofia',   'last_name' => 'Ramos',     'lrn' => '202600105', 'grade_level' => 'Grade 9'],
            ['first_name' => 'Carlos',  'last_name' => 'Diaz',      'lrn' => '202600106', 'grade_level' => 'Grade 9'],
            ['first_name' => 'Isabella','last_name' => 'Lopez',     'lrn' => '202600107', 'grade_level' => 'Grade 10'],
            ['first_name' => 'Marco',   'last_name' => 'Torres',    'lrn' => '202600108', 'grade_level' => 'Grade 10'],
            ['first_name' => 'Camille', 'last_name' => 'Flores',    'lrn' => '202600109', 'grade_level' => 'Grade 11'],
            ['first_name' => 'Luis',    'last_name' => 'Aquino',    'lrn' => '202600110', 'grade_level' => 'Grade 12'],
        ];

        foreach ($students as $data) {
            $username = strtolower($data['first_name'] . '.' . $data['last_name']);

            $student = User::firstOrCreate(
                ['lrn' => $data['lrn']],
                [
                    'first_name'  => $data['first_name'],
                    'last_name'   => $data['last_name'],
                    'username'    => $username,
                    'email'       => encrypt($username . '@test.sakya.edu.ph'),
                    'email_hash'  => hash('sha256', strtolower($username . '@test.sakya.edu.ph')),
                    'password'    => Hash::make('Password@123'),
                    'role_id'     => '01',
                    'grade_level' => $data['grade_level'],
                    'gender'      => 'male',
                    'status'      => 'active',
                    'password_reset_required' => false,
                ]
            );

            // Create a confirmed payment so enrollment is not blocked
            if ($activeYear) {
                Payment::firstOrCreate(
                    [
                        'student_id'       => $student->id,
                        'academic_year_id' => $activeYear->id,
                    ],
                    [
                        'grade_level'      => $data['grade_level'],
                        'amount'           => 5000.00,
                        'currency'         => 'PHP',
                        'account_label'    => 'Test Payment',
                        'account_number'   => 'TEST-001',
                        'status'           => 'paid',
                        'reference_number' => 'TEST-' . $student->id . '-' . $activeYear->id,
                        'paid_at'          => now(),
                        'confirmed_by'     => User::where('role_id', '03')->value('id')
                                          ?? User::where('role_id', '04')->value('id'),
                        'notes'            => 'Test payment — seeded for development',
                    ]
                );
            }
        }

        // Also create paid payments for any existing students who don't have one
        if ($activeYear) {
            User::where('role_id', '01')
                ->whereDoesntHave('payments', fn($q) =>
                    $q->where('academic_year_id', $activeYear->id)->where('status', 'paid')
                )
                ->get()
                ->each(function ($student) use ($activeYear) {
                    Payment::firstOrCreate(
                        [
                            'student_id'       => $student->id,
                            'academic_year_id' => $activeYear->id,
                        ],
                        [
                            'grade_level'      => $student->grade_level ?? 'Grade 7',
                            'amount'           => 5000.00,
                            'currency'         => 'PHP',
                            'account_label'    => 'Test Payment',
                            'account_number'   => 'TEST-001',
                            'status'           => 'paid',
                            'reference_number' => 'TEST-' . $student->id . '-' . $activeYear->id,
                            'paid_at'          => now(),
                            'confirmed_by'     => User::where('role_id', '03')->value('id')
                                              ?? User::where('role_id', '04')->value('id'),
                            'notes'            => 'Test payment — seeded for development',
                        ]
                    );
                });
        }

        $this->command->info('Test students created and all existing students marked as paid.');
    }
}
