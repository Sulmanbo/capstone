<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentScore;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * DevelopmentSeeder
 *
 * Produces a realistic dev dataset for the EncryptEd school system.
 * Idempotent: truncates all academic tables and non-admin users before
 * re-seeding. The existing admin account (role_id=04) is preserved.
 *
 * Run: php artisan db:seed --class=DevelopmentSeeder
 *
 * Data volume (approximate):
 *   12 sections × 8 subjects  =  96 section_subjects
 *   84 students               =  84 enrollments
 *   grades: 84 × 8 × 3 quarters = 2,016 rows
 *   attendance: 84 × 8 subjects × 30 days = 20,160 rows (batch-inserted)
 *   assessments: 96 × 6 avg  =  ~576 rows
 *   scores: ~576 × 7 avg students = ~4,032 rows
 */
class DevelopmentSeeder extends Seeder
{
    // ── Constants ──────────────────────────────────────────────────────────

    private const GRADE_LEVELS = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];

    private const SECTION_NAMES = [
        'Grade 7'  => ['St. Therese', 'St. Michael', 'St. Joseph'],
        'Grade 8'  => ['St. Mary', 'St. John', 'St. Peter'],
        'Grade 9'  => ['St. Paul', 'St. Mark', 'St. Luke'],
        'Grade 10' => ['St. Matthew', 'St. Andrew', 'St. James'],
    ];

    // Real DepEd K-12 Junior High School subjects
    private const SUBJECTS = [
        ['code' => 'ENG',   'name' => 'English',                              'credits' => 3],
        ['code' => 'FIL',   'name' => 'Filipino',                             'credits' => 3],
        ['code' => 'MATH',  'name' => 'Mathematics',                          'credits' => 4],
        ['code' => 'SCI',   'name' => 'Science',                              'credits' => 4],
        ['code' => 'AP',    'name' => 'Araling Panlipunan',                   'credits' => 3],
        ['code' => 'ESP',   'name' => 'Edukasyon sa Pagpapakatao',            'credits' => 2],
        ['code' => 'TLE',   'name' => 'Technology and Livelihood Education',  'credits' => 3],
        ['code' => 'MAPEH', 'name' => 'MAPEH',                               'credits' => 3],
    ];

    // Timetable — one slot per subject (no conflicts for a student)
    private const SUBJECT_SCHEDULE = [
        'ENG'   => ['days' => ['monday', 'wednesday', 'friday'],          'start' => '07:30', 'end' => '08:30'],
        'FIL'   => ['days' => ['monday', 'wednesday', 'friday'],          'start' => '08:30', 'end' => '09:30'],
        'MATH'  => ['days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], 'start' => '09:30', 'end' => '10:30'],
        'SCI'   => ['days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], 'start' => '10:30', 'end' => '11:30'],
        'AP'    => ['days' => ['monday', 'wednesday', 'friday'],          'start' => '11:30', 'end' => '12:30'],
        'ESP'   => ['days' => ['tuesday', 'thursday'],                    'start' => '13:00', 'end' => '14:00'],
        'TLE'   => ['days' => ['tuesday', 'thursday'],                    'start' => '14:00', 'end' => '15:00'],
        'MAPEH' => ['days' => ['monday', 'wednesday', 'friday'],          'start' => '13:00', 'end' => '14:00'],
    ];

    private const ROOMS = ['Room 101', 'Room 102', 'Room 103', 'Room 201', 'Room 202', 'Lab 301', 'AVR', 'Gym'];

    private const ATTENDANCE_WEIGHTS = [
        'present' => 80,
        'absent'  => 10,
        'late'    => 7,
        'excused' => 3,
    ];

    // ══════════════════════════════════════════════════════════════════════
    // ENTRY POINT
    // ══════════════════════════════════════════════════════════════════════

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('┌─────────────────────────────────────────┐');
        $this->command->info('│        EncryptEd — DevelopmentSeeder     │');
        $this->command->info('└─────────────────────────────────────────┘');

        $this->cleanDatabase();

        [$academicYear, $quarters] = $this->seedAcademicYear();
        $subjects   = $this->seedSubjects();
        $faculty    = $this->seedFaculty();
        $registrars = $this->seedRegistrars();
        $sections   = $this->seedSections($academicYear, $faculty);
        $students   = $this->seedStudents($sections);

        $this->seedCurriculumMappings($academicYear, $subjects);

        $enrollmentMap    = $this->seedEnrollments($students, $sections, $academicYear);
        $sectionSubjects  = $this->seedSectionSubjects($sections, $subjects, $faculty, $academicYear);
        $this->seedGrades($enrollmentMap, $sectionSubjects, $quarters, $faculty);
        $this->seedAttendance($enrollmentMap, $sectionSubjects, $faculty, $quarters);
        $this->seedAssessments($sectionSubjects, $faculty, $quarters);

        $this->command->info('');
        $this->command->info('✓ DevelopmentSeeder completed successfully.');
        $this->printCredentials($faculty, $registrars);
    }

    // ══════════════════════════════════════════════════════════════════════
    // CLEANUP
    // ══════════════════════════════════════════════════════════════════════

    private function cleanDatabase(): void
    {
        $this->command->line('  Truncating academic tables…');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'assessment_scores', 'assessments', 'attendance',
            'grades', 'section_subjects', 'enrollments',
            'sections', 'curriculum_mappings', 'grading_quarters',
            'academic_years',
        ] as $table) {
            DB::table($table)->truncate();
        }

        // Remove seeder-created faculty, registrars, students (keep admins)
        DB::table('users')->whereIn('role_id', ['01', '02', '03'])->delete();

        // Reset auto-increment on subjects (we re-seed subjects too)
        DB::table('subjects')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->line('  ✓ Clean slate.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // ACADEMIC YEAR + QUARTERS
    // ══════════════════════════════════════════════════════════════════════

    private function seedAcademicYear(): array
    {
        $this->command->line('  Seeding academic year & quarters…');

        $ay = AcademicYear::create([
            'year_label' => '2025-2026',
            'start_date' => '2025-06-02',
            'end_date'   => '2026-04-03',
            'status'     => 'active',
            'is_active'  => true,
        ]);

        // Q1: archived (grades finalized), Q2: archived, Q3: active (current), Q4: inactive (future)
        $quarterDefs = [
            ['quarter_number' => 1, 'quarter_name' => 'First Quarter',  'start' => '2025-06-02', 'end' => '2025-08-08', 'status' => 'archived'],
            ['quarter_number' => 2, 'quarter_name' => 'Second Quarter', 'start' => '2025-08-18', 'end' => '2025-10-17', 'status' => 'archived'],
            ['quarter_number' => 3, 'quarter_name' => 'Third Quarter',  'start' => '2025-11-04', 'end' => '2026-01-30', 'status' => 'active'],
            ['quarter_number' => 4, 'quarter_name' => 'Fourth Quarter', 'start' => '2026-02-09', 'end' => '2026-04-03', 'status' => 'inactive'],
        ];

        $quarters = [];
        foreach ($quarterDefs as $def) {
            $quarters[] = GradingQuarter::create([
                'academic_year_id' => $ay->id,
                'quarter_number'   => $def['quarter_number'],
                'quarter_name'     => $def['quarter_name'],
                'start_date'       => $def['start'],
                'end_date'         => $def['end'],
                'status'           => $def['status'],
                'is_active'        => $def['status'] === 'active',
            ]);
        }

        $this->command->line("  ✓ Academic year {$ay->year_label} with 4 quarters.");
        return [$ay, $quarters];
    }

    // ══════════════════════════════════════════════════════════════════════
    // SUBJECTS
    // ══════════════════════════════════════════════════════════════════════

    private function seedSubjects(): array
    {
        $this->command->line('  Seeding subjects…');
        $subjects = [];
        foreach (self::SUBJECTS as $def) {
            $subjects[$def['code']] = Subject::create([
                'subject_code' => $def['code'],
                'subject_name' => $def['name'],
                'description'  => "DepEd K-12 Junior High School — {$def['name']}",
                'credits'      => $def['credits'],
                'status'       => 'active',
            ]);
        }
        $this->command->line('  ✓ ' . count($subjects) . ' subjects.');
        return $subjects;
    }

    // ══════════════════════════════════════════════════════════════════════
    // CURRICULUM MAPPINGS
    // ══════════════════════════════════════════════════════════════════════

    private function seedCurriculumMappings(AcademicYear $ay, array $subjects): void
    {
        $this->command->line('  Seeding curriculum mappings…');
        $seq = 1;
        foreach (self::GRADE_LEVELS as $gradeLevel) {
            foreach ($subjects as $subject) {
                \App\Models\CurriculumMapping::create([
                    'academic_year_id' => $ay->id,
                    'grade_level'      => $gradeLevel,
                    'subject_id'       => $subject->id,
                    'is_required'      => true,
                    'sequence_order'   => $seq++,
                    'status'           => 'active',
                ]);
            }
            $seq = 1; // reset per grade level
        }
        $this->command->line('  ✓ Curriculum mappings for Grade 7–10.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // FACULTY
    // ══════════════════════════════════════════════════════════════════════

    private function seedFaculty(): array
    {
        $this->command->line('  Seeding faculty…');

        $facultyData = [
            ['first' => 'Carlos',    'last' => 'Santos',   'emp' => 'FAC-0001', 'user' => 'fac.santos'],
            ['first' => 'Lourdes',   'last' => 'Reyes',    'emp' => 'FAC-0002', 'user' => 'fac.reyes'],
            ['first' => 'Roberto',   'last' => 'Cruz',     'emp' => 'FAC-0003', 'user' => 'fac.cruz'],
            ['first' => 'Maricel',   'last' => 'Garcia',   'emp' => 'FAC-0004', 'user' => 'fac.garcia'],
            ['first' => 'Emmanuel',  'last' => 'Lopez',    'emp' => 'FAC-0005', 'user' => 'fac.lopez'],
        ];

        $faculty = [];
        foreach ($facultyData as $i => $d) {
            $faculty[] = User::create([
                'first_name'              => $d['first'],
                'last_name'               => $d['last'],
                'email'                   => strtolower("{$d['first']}.{$d['last']}@encryptedschool.edu.ph"),
                'username'                => $d['user'],
                'password'                => 'Faculty@1234',
                'role_id'                 => '02',
                'employee_number'         => $d['emp'],
                'gender'                  => $i % 2 === 0 ? 'male' : 'female',
                'status'                  => 'active',
                'password_reset_required' => false,
            ]);
        }

        $this->command->line('  ✓ ' . count($faculty) . ' faculty users.');
        return $faculty;
    }

    // ══════════════════════════════════════════════════════════════════════
    // REGISTRARS
    // ══════════════════════════════════════════════════════════════════════

    private function seedRegistrars(): array
    {
        $this->command->line('  Seeding registrars…');

        $data = [
            ['first' => 'Ana',   'last' => 'Flores',  'emp' => 'REG-0001', 'user' => 'reg.flores'],
            ['first' => 'Jose',  'last' => 'Mendoza',  'emp' => 'REG-0002', 'user' => 'reg.mendoza'],
        ];

        $registrars = [];
        foreach ($data as $d) {
            $registrars[] = User::create([
                'first_name'              => $d['first'],
                'last_name'               => $d['last'],
                'email'                   => strtolower("{$d['first']}.{$d['last']}@encryptedschool.edu.ph"),
                'username'                => $d['user'],
                'password'                => 'Registrar@1234',
                'role_id'                 => '03',
                'employee_number'         => $d['emp'],
                'status'                  => 'active',
                'password_reset_required' => false,
            ]);
        }

        $this->command->line('  ✓ ' . count($registrars) . ' registrar users.');
        return $registrars;
    }

    // ══════════════════════════════════════════════════════════════════════
    // SECTIONS
    // ══════════════════════════════════════════════════════════════════════

    private function seedSections(AcademicYear $ay, array $faculty): array
    {
        $this->command->line('  Seeding sections…');

        $sections  = [];
        $adviserId = 0;

        foreach (self::SECTION_NAMES as $gradeLevel => $names) {
            foreach ($names as $name) {
                $sections[] = Section::create([
                    'section_name'     => $name,
                    'grade_level'      => $gradeLevel,
                    'academic_year_id' => $ay->id,
                    'adviser_id'       => $faculty[$adviserId % count($faculty)]->id,
                    'capacity'         => 40,
                    'status'           => 'active',
                ]);
                $adviserId++;
            }
        }

        $this->command->line('  ✓ ' . count($sections) . ' sections across 4 grade levels.');
        return $sections;
    }

    // ══════════════════════════════════════════════════════════════════════
    // STUDENTS
    // ══════════════════════════════════════════════════════════════════════

    private function seedStudents(array $sections): array
    {
        $this->command->line('  Seeding ~84 students…');

        $firstNames = ['Juan', 'Maria', 'Pedro', 'Ana', 'Jose', 'Rosa', 'Antonio', 'Carmen',
                       'Ricardo', 'Elena', 'Miguel', 'Luz', 'Fernando', 'Patricia', 'Eduardo',
                       'Gloria', 'Ramon', 'Teresa', 'Alfredo', 'Dolores', 'Mario', 'Nena',
                       'Ernesto', 'Carina', 'Rodrigo'];

        $lastNames  = ['Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Lopez', 'Torres', 'Ramos',
                       'Flores', 'Bautista', 'Aquino', 'Mendoza', 'Castillo', 'Villanueva',
                       'Buenaventura', 'Pascual'];

        $parentFirstNames = ['Roberto', 'Concepcion', 'Ernesto', 'Maricel', 'Danilo', 'Lourdes',
                             'Rodrigo', 'Nimfa', 'Alberto', 'Corazon'];

        $students = [];
        $lrnBase  = 100000000001;
        $counter  = 1;

        foreach ($sections as $section) {
            for ($i = 0; $i < 7; $i++) {
                $first  = $firstNames[array_rand($firstNames)];
                $last   = $lastNames[array_rand($lastNames)];
                $lrn    = (string)($lrnBase + $counter);
                $gender = $counter % 2 === 0 ? 'female' : 'male';
                $pFirst = $parentFirstNames[array_rand($parentFirstNames)];

                $student = User::create([
                    'first_name'              => $first,
                    'last_name'               => $last,
                    'email'                   => "stu.{$lrn}@encryptedschool.edu.ph",
                    'username'                => "stu.{$lrn}",
                    'password'                => 'Student@1234',
                    'role_id'                 => '01',
                    'lrn'                     => $lrn,
                    'grade_level'             => $section->grade_level,
                    'section_id'              => $section->id,
                    'enrollment_date'         => '2025-06-02',
                    'parent_name'             => "{$pFirst} {$last}",   // encrypted by mutator
                    'parent_contact'          => '09' . rand(100000000, 999999999),
                    'lrn_status'              => 'verified',
                    'gender'                  => $gender,
                    'status'                  => 'active',
                    'password_reset_required' => false,
                ]);

                $students[] = $student;
                $counter++;
            }
        }

        $this->command->line('  ✓ ' . count($students) . ' students.');
        return $students;
    }

    // ══════════════════════════════════════════════════════════════════════
    // ENROLLMENTS
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Returns a map of student_id → Enrollment model.
     */
    private function seedEnrollments(array $students, array $sections, AcademicYear $ay): array
    {
        $this->command->line('  Seeding enrollments…');

        // Build a section lookup: section_id → section
        $sectionMap = [];
        foreach ($sections as $s) {
            $sectionMap[$s->id] = $s;
        }

        $enrollmentMap = [];
        foreach ($students as $student) {
            $enrollment = Enrollment::create([
                'student_id'       => $student->id,
                'section_id'       => $student->section_id,
                'academic_year_id' => $ay->id,
                'status'           => 'enrolled',
                'enrolled_at'      => '2025-06-02 08:00:00',
            ]);
            $enrollmentMap[$student->id] = $enrollment;
        }

        $this->command->line('  ✓ ' . count($enrollmentMap) . ' enrollments.');
        return $enrollmentMap;
    }

    // ══════════════════════════════════════════════════════════════════════
    // SECTION SUBJECTS
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Returns a flat array of SectionSubject models.
     */
    private function seedSectionSubjects(array $sections, array $subjects, array $faculty, AcademicYear $ay): array
    {
        $this->command->line('  Seeding section_subjects…');

        // Round-robin faculty assignment per subject code
        $subjectCodes    = array_keys($subjects);
        $facultyBySubject = [];
        foreach ($subjectCodes as $idx => $code) {
            $facultyBySubject[$code] = $faculty[$idx % count($faculty)];
        }

        $sectionSubjects = [];
        $roomIdx         = 0;

        foreach ($sections as $section) {
            foreach ($subjects as $code => $subject) {
                $sched = self::SUBJECT_SCHEDULE[$code];
                $ss = SectionSubject::create([
                    'section_id'       => $section->id,
                    'subject_id'       => $subject->id,
                    'faculty_id'       => $facultyBySubject[$code]->id,
                    'academic_year_id' => $ay->id,
                    'room'             => self::ROOMS[$roomIdx % count(self::ROOMS)],
                    'schedule_days'    => $sched['days'],
                    'start_time'       => $sched['start'] . ':00',
                    'end_time'         => $sched['end'] . ':00',
                ]);
                $sectionSubjects[] = $ss;
                $roomIdx++;
            }
        }

        $this->command->line('  ✓ ' . count($sectionSubjects) . ' section_subjects.');
        return $sectionSubjects;
    }

    // ══════════════════════════════════════════════════════════════════════
    // GRADES
    // ══════════════════════════════════════════════════════════════════════

    private function seedGrades(
        array $enrollmentMap,
        array $sectionSubjects,
        array $quarters,
        array $faculty
    ): void {
        $this->command->line('  Seeding grades (batch insert)…');

        $submitterId  = $faculty[0]->id;
        $finalizerId  = $faculty[1]->id;
        $weights      = config('academic.grade_weights');
        $now          = now()->toDateTimeString();

        // Quarter index → status
        // Q1 (idx 0) = finalized, Q2 (idx 1) = finalized, Q3 (idx 2) = draft, Q4 not seeded
        $quarterConfig = [
            0 => 'finalized',
            1 => 'finalized',
            2 => 'draft',
        ];

        $rows = [];

        // Build a lookup: section_id → [enrollment_ids for students in that section]
        $sectionEnrollments = [];
        foreach ($enrollmentMap as $studentId => $enrollment) {
            $sectionEnrollments[$enrollment->section_id][] = $enrollment;
        }

        foreach ($sectionSubjects as $ss) {
            $enrollments = $sectionEnrollments[$ss->section_id] ?? [];

            foreach ($enrollments as $enrollment) {
                foreach ($quarterConfig as $qIdx => $status) {
                    $quarter = $quarters[$qIdx];

                    // Generate realistic grade components (75–98 range)
                    $ww  = $this->randomGrade();
                    $pt  = $this->randomGrade();
                    $qa  = $this->randomGrade();
                    $fg  = round(($ww * $weights['written_work']) + ($pt * $weights['performance_task']) + ($qa * $weights['quarterly_assessment']), 2);

                    $rows[] = [
                        'enrollment_id'       => $enrollment->id,
                        'section_subject_id'  => $ss->id,
                        'grading_quarter_id'  => $quarter->id,
                        'written_work'        => $ww,
                        'performance_task'    => $pt,
                        'quarterly_assessment'=> $qa,
                        'final_grade'         => $fg,
                        'status'              => $status,
                        'submitted_at'        => $status !== 'draft' ? $now : null,
                        'submitted_by'        => $status !== 'draft' ? $submitterId : null,
                        'finalized_at'        => $status === 'finalized' ? $now : null,
                        'finalized_by'        => $status === 'finalized' ? $finalizerId : null,
                        'remarks'             => null,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }
            }
        }

        // Chunk inserts for performance (500 rows per query)
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('grades')->insert($chunk);
        }

        $this->command->line('  ✓ ' . count($rows) . ' grade records.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // ATTENDANCE
    // ══════════════════════════════════════════════════════════════════════

    private function seedAttendance(
        array $enrollmentMap,
        array $sectionSubjects,
        array $faculty,
        array $quarters
    ): void {
        $this->command->line('  Seeding attendance (last 30 school days — batch insert)…');

        // Generate the last 30 weekdays from today
        $schoolDays = [];
        $day = now()->subDays(1);
        while (count($schoolDays) < 30) {
            if (!in_array($day->dayOfWeek, [0, 6])) { // skip Sun/Sat
                $schoolDays[] = $day->toDateString();
            }
            $day->subDay();
        }
        $schoolDays = array_reverse($schoolDays); // chronological order

        $statuses = [];
        foreach (self::ATTENDANCE_WEIGHTS as $status => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $statuses[] = $status;
            }
        }

        $recorderId = $faculty[0]->id;
        $now        = now()->toDateTimeString();

        $sectionEnrollments = [];
        foreach ($enrollmentMap as $enrollment) {
            $sectionEnrollments[$enrollment->section_id][] = $enrollment;
        }

        $rows = [];
        foreach ($sectionSubjects as $ss) {
            $enrollments = $sectionEnrollments[$ss->section_id] ?? [];
            $days        = $ss->schedule_days ?? [];

            foreach ($schoolDays as $date) {
                $dayName = strtolower(date('l', strtotime($date)));
                if (!in_array($dayName, $days)) {
                    continue; // this subject doesn't meet on this day
                }
                foreach ($enrollments as $enrollment) {
                    $status = $statuses[array_rand($statuses)];
                    $rows[] = [
                        'enrollment_id'      => $enrollment->id,
                        'section_subject_id' => $ss->id,
                        'date'               => $date,
                        'status'             => $status,
                        'remarks'            => null,
                        'recorded_by'        => $recorderId,
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ];
                }
            }

            // Flush in chunks every 2,000 rows to avoid memory spikes
            if (count($rows) >= 2000) {
                DB::table('attendance')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('attendance')->insert($rows);
        }

        $this->command->line('  ✓ Attendance records inserted.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // ASSESSMENTS + SCORES
    // ══════════════════════════════════════════════════════════════════════

    private function seedAssessments(array $sectionSubjects, array $faculty, array $quarters): void
    {
        $this->command->line('  Seeding assessments & scores…');

        $assessmentTemplates = [
            ['title' => 'Written Quiz 1',        'type' => 'quiz',       'category' => 'written_work',         'max' => 50],
            ['title' => 'Written Quiz 2',        'type' => 'quiz',       'category' => 'written_work',         'max' => 50],
            ['title' => 'Long Test',             'type' => 'exam',       'category' => 'written_work',         'max' => 100],
            ['title' => 'Group Project',         'type' => 'project',    'category' => 'performance_task',      'max' => 100],
            ['title' => 'Laboratory Activity',   'type' => 'assignment', 'category' => 'performance_task',      'max' => 30],
            ['title' => 'Quarterly Examination', 'type' => 'exam',       'category' => 'quarterly_assessment', 'max' => 100],
        ];

        $now           = now()->toDateTimeString();
        $scoreRows     = [];
        $assessmentCount = 0;

        // Build enrollment lookup: section_id → [enrollments]
        // We need enrollment IDs, not models, for the score rows
        // Reload from DB since we inserted via DB::table
        $enrollmentsBySection = Enrollment::with('section')
            ->get()
            ->groupBy('section_id');

        foreach ($sectionSubjects as $ss) {
            $enrollments = $enrollmentsBySection[$ss->section_id] ?? collect();
            if ($enrollments->isEmpty()) {
                continue;
            }

            // Use Q1 due dates for finalized assessments (closed quarter)
            $q1 = $quarters[0];
            $q3 = $quarters[2]; // active quarter for draft/posted assessments

            foreach ($assessmentTemplates as $idx => $tmpl) {
                $isFinished = $idx < 4; // first 4 templates in closed quarters
                $dueDate    = $isFinished
                    ? date('Y-m-d H:i:s', strtotime($q1->end_date . ' 23:59:00') - ($idx * 86400))
                    : date('Y-m-d H:i:s', strtotime($q3->end_date . ' 23:59:00') - ($idx * 86400));

                $assessment = Assessment::create([
                    'section_subject_id' => $ss->id,
                    'title'              => $tmpl['title'],
                    'description'        => 'Assessment for ' . ($ss->subject->subject_name ?? 'Subject'),
                    'type'               => $tmpl['type'],
                    'category'           => $tmpl['category'],
                    'max_score'          => $tmpl['max'],
                    'due_date'           => $dueDate,
                    'posted_by'          => $ss->faculty_id,
                    'status'             => $isFinished ? 'closed' : 'posted',
                ]);
                $assessmentCount++;

                // Generate scores for each enrolled student
                foreach ($enrollments as $enrollment) {
                    $score = $isFinished
                        ? round(($tmpl['max'] * $this->randomGrade()) / 100, 2)
                        : null; // Q3 assessments may not be scored yet

                    $scoreRows[] = [
                        'assessment_id' => $assessment->id,
                        'enrollment_id' => $enrollment->id,
                        'score'         => $score,
                        'submitted_at'  => $isFinished ? $now : null,
                        'graded_at'     => $isFinished ? $now : null,
                        'feedback'      => null,
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ];
                }
            }

            if (count($scoreRows) >= 2000) {
                DB::table('assessment_scores')->insert($scoreRows);
                $scoreRows = [];
            }
        }

        if (!empty($scoreRows)) {
            DB::table('assessment_scores')->insert($scoreRows);
        }

        $this->command->line("  ✓ {$assessmentCount} assessments with scores.");
    }

    // ══════════════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════════════

    /** Generate a realistic DepEd-range grade (75–98). */
    private function randomGrade(): float
    {
        // Weighted toward the 80–92 range (normal distribution approximation)
        $base = rand(75, 98);
        $adj  = rand(-3, 3);
        return max(75, min(100, (float)($base + $adj)));
    }

    private function printCredentials(array $faculty, array $registrars): void
    {
        $this->command->info('');
        $this->command->info('  ── Seeded Credentials ──────────────────────────');
        $this->command->line('  Role       Username           Password');
        $this->command->line('  ─────────────────────────────────────────────────');

        foreach ($faculty as $f) {
            $u = str_pad($f->username, 18);
            $this->command->line("  Faculty    {$u} Faculty@1234");
        }
        foreach ($registrars as $r) {
            $u = str_pad($r->username, 18);
            $this->command->line("  Registrar  {$u} Registrar@1234");
        }

        $this->command->line('  Student    stu.100000000002    Student@1234');
        $this->command->line('  Admin      adm.admin           Admin@1234');
        $this->command->line('  ─────────────────────────────────────────────────');
        $this->command->info('');
    }
}
