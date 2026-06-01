<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    // DepEd junior high school grade progression
    private const GRADE_PROGRESSION = [
        'Grade 7'  => 'Grade 8',
        'Grade 8'  => 'Grade 9',
        'Grade 9'  => 'Grade 10',
        'Grade 10' => null, // final level → graduated
    ];

    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('id')->get();
        $sections      = collect();
        $students      = collect();
        $selectedYear  = null;
        $selectedSection = null;

        $yearId    = $request->input('academic_year_id') ?? AcademicYear::currentId();
        $sectionId = $request->input('section_id');

        if ($yearId) {
            $selectedYear = AcademicYear::find($yearId);
            $sections = Section::where('academic_year_id', $yearId)
                ->orderBy('grade_level')
                ->orderBy('section_name')
                ->get();
        }

        if ($yearId && $sectionId) {
            $selectedSection = Section::find($sectionId);
            if ($selectedSection) {
                $students = $this->buildStudentList($selectedSection, (int) $yearId);
            }
        }

        $activeYear = AcademicYear::where('status', 'active')->first();

        return view('dashboard.registrar-promotion', compact(
            'academicYears',
            'sections',
            'students',
            'selectedYear',
            'selectedSection',
            'activeYear'
        ));
    }

    public function promote(Request $request)
    {
        $request->validate([
            'source_year_id'  => ['required', 'exists:academic_years,id'],
            'source_section_id' => ['required', 'exists:sections,id'],
            'student_ids'     => ['required', 'array', 'min:1'],
            'student_ids.*'   => ['exists:users,id'],
        ]);

        $sourceSection = Section::with('academicYear')->findOrFail($request->source_section_id);
        $activeYear    = AcademicYear::where('status', 'active')->first();

        if (!$activeYear) {
            return back()->withErrors([
                'promotion' => 'No active academic year found. Please activate the next academic year before promoting students.',
            ]);
        }

        $nextGrade    = self::GRADE_PROGRESSION[$sourceSection->grade_level] ?? null;
        $promoted     = 0;
        $graduated    = 0;

        DB::transaction(function () use ($request, $sourceSection, $activeYear, $nextGrade, &$promoted, &$graduated) {
            foreach ($request->student_ids as $studentId) {
                $student = User::findOrFail($studentId);

                if ($nextGrade === null) {
                    // Completing the final supported grade level → graduated
                    $student->update([
                        'lrn_status' => 'graduated',
                        'status'     => 'inactive',
                    ]);
                    $graduated++;
                } else {
                    $student->update(['grade_level' => $nextGrade]);

                    // Create fresh enrollment in the new academic year if none exists yet
                    if (!Enrollment::existsForStudentAndYear($student->id, $activeYear->id)) {
                        Enrollment::create([
                            'student_id'       => $student->id,
                            'section_id'       => $sourceSection->id,
                            'academic_year_id' => $activeYear->id,
                            'status'           => 'enrolled',
                            'enrolled_at'      => now(),
                        ]);
                    }
                    $promoted++;
                }

                AuditLog::record(AuditLog::STUDENT_PROMOTED, [
                    'student_id'   => $student->id,
                    'student_name' => $student->full_name ?? ($student->first_name . ' ' . $student->last_name),
                    'from_grade'   => $sourceSection->grade_level,
                    'to_grade'     => $nextGrade ?? 'Graduated',
                    'from_year'    => $sourceSection->academicYear->year_label ?? '',
                    'to_year'      => $activeYear->year_label,
                ]);
            }
        });

        $parts = [];
        if ($promoted  > 0) $parts[] = "{$promoted} student(s) promoted to {$nextGrade}.";
        if ($graduated > 0) $parts[] = "{$graduated} student(s) marked as graduated.";

        return back()->with('success', implode(' ', $parts));
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function buildStudentList(Section $section, int $yearId): \Illuminate\Support\Collection
    {
        $passingGrade = config('academic.passing_grade', 75);

        $enrollments = Enrollment::where('section_id', $section->id)
            ->where('academic_year_id', $yearId)
            ->where('status', 'enrolled')
            ->with([
                'student',
                'grades' => fn($q) => $q->where('status', 'locked'),
            ])
            ->get();

        return $enrollments->map(function (Enrollment $enrollment) use ($passingGrade) {
            $grades  = $enrollment->grades;
            $avg     = $grades->isNotEmpty()
                ? round($grades->avg('final_grade'), 2)
                : null;

            return (object) [
                'student'           => $enrollment->student,
                'enrollment'        => $enrollment,
                'average'           => $avg,
                'grades_count'      => $grades->count(),
                'is_promotable'     => $avg !== null && $avg >= $passingGrade,
                'has_locked_grades' => $grades->isNotEmpty(),
            ];
        })->sortBy(fn($row) => optional($row->student)->last_name);
    }
}
