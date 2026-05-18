<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Section;
use Illuminate\Http\Request;

class AggregateReportController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('id')->get();
        $selectedYear  = null;
        $sections      = collect();
        $selectedSection = null;
        $honors        = collect();
        $intervention  = collect();
        $satisfactory  = collect();

        $yearId    = $request->input('academic_year_id');
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
                [$honors, $satisfactory, $intervention] = $this->computeAggregates($selectedSection, (int) $yearId);
            }
        }

        return view('dashboard.registrar-aggregate-reports', compact(
            'academicYears', 'sections',
            'selectedYear', 'selectedSection',
            'honors', 'satisfactory', 'intervention'
        ));
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function computeAggregates(Section $section, int $yearId): array
    {
        $passingGrade = config('academic.passing_grade', 75);
        $honorCutoff  = 90;

        $enrollments = Enrollment::where('section_id', $section->id)
            ->where('academic_year_id', $yearId)
            ->where('status', 'enrolled')
            ->with([
                'student',
                'grades' => fn($q) => $q->whereNull('dropped_at')->where('status', 'locked'),
            ])
            ->get();

        $honors       = collect();
        $satisfactory = collect();
        $intervention = collect();

        foreach ($enrollments as $enrollment) {
            $grades = $enrollment->grades;
            if ($grades->isEmpty()) continue;

            $avg = round($grades->avg('final_grade'), 2);

            $row = (object) [
                'student'     => $enrollment->student,
                'average'     => $avg,
                'grade_count' => $grades->count(),
            ];

            if ($avg >= $honorCutoff) {
                $honors->push($row);
            } elseif ($avg >= $passingGrade) {
                $satisfactory->push($row);
            } else {
                $intervention->push($row);
            }
        }

        // Honor roll sorted by average descending, others by name
        $honors       = $honors->sortByDesc('average')->values();
        $satisfactory = $satisfactory->sortBy(fn($r) => optional($r->student)->last_name)->values();
        $intervention = $intervention->sortByDesc('average')->values();

        return [$honors, $satisfactory, $intervention];
    }
}
