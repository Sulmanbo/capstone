<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\SectionSubject;
use Illuminate\Http\Request;

class AdminGradesController extends Controller
{
    public function index(Request $request)
    {
        $activeYear    = AcademicYear::where('status', 'active')->first();
        $activeQuarter = null;
        $quarters      = collect();

        if ($activeYear) {
            $quarters = GradingQuarter::where('academic_year_id', $activeYear->id)
                ->orderBy('quarter_number')
                ->get();
            $activeQuarter = $quarters->firstWhere('status', 'active');
        }

        $selectedQuarterId = $request->input('quarter_id', $activeQuarter?->id);
        $selectedQuarter   = $quarters->firstWhere('id', $selectedQuarterId);

        $stats = [
            'draft'     => 0,
            'submitted' => 0,
            'finalized' => 0,
            'locked'    => 0,
        ];

        $sectionSummaries = collect();

        if ($activeYear && $selectedQuarter) {
            $gradeCounts = Grade::where('grading_quarter_id', $selectedQuarter->id)
                ->selectRaw('status, COUNT(*) as cnt')
                ->groupBy('status')
                ->pluck('cnt', 'status');

            foreach ($stats as $s => $_) {
                $stats[$s] = $gradeCounts->get($s, 0);
            }

            $sectionSummaries = SectionSubject::where('academic_year_id', $activeYear->id)
                ->with(['section', 'subject', 'faculty'])
                ->get()
                ->map(function (SectionSubject $ss) use ($selectedQuarter) {
                    $counts = Grade::where('section_subject_id', $ss->id)
                        ->where('grading_quarter_id', $selectedQuarter->id)
                        ->selectRaw('status, COUNT(*) as cnt')
                        ->groupBy('status')
                        ->pluck('cnt', 'status');

                    $ss->grade_counts = [
                        'draft'     => $counts->get('draft', 0),
                        'submitted' => $counts->get('submitted', 0),
                        'finalized' => $counts->get('finalized', 0),
                        'locked'    => $counts->get('locked', 0),
                    ];
                    $ss->total_students = array_sum($ss->grade_counts);
                    return $ss;
                })
                ->filter(fn($ss) => $ss->total_students > 0 || true);
        }

        return view('admin.grades.index', compact(
            'activeYear',
            'activeQuarter',
            'quarters',
            'selectedQuarter',
            'stats',
            'sectionSummaries'
        ));
    }
}
