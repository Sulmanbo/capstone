<?php
namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\Section;
use App\Models\SectionSubject;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SFFormController extends Controller
{
    // ── SF1: Class List ───────────────────────────────────────────────────
    public function sf1(Request $request)
    {
        $sections    = Section::with('academicYear', 'adviser')->orderBy('grade_level')->get();
        $activeYear  = AcademicYear::where('status', 'active')->first();
        $sectionId   = $request->input('section_id');
        $section     = null;
        $students    = collect();

        if ($sectionId) {
            $section  = Section::with(['adviser', 'academicYear'])->findOrFail($sectionId);
            $students = Enrollment::where('section_id', $sectionId)
                ->where('status', 'enrolled')
                ->with('student')
                ->orderBy('created_at')
                ->get()
                ->map->student
                ->filter()
                ->sortBy('last_name')
                ->values();
        }

        if ($request->input('download') === '1' && $section) {
            $pdf = Pdf::loadView('pdf.sf1', compact('section', 'students'))
                ->setPaper('letter', 'portrait');
            return $pdf->download("SF1-{$section->section_name}.pdf");
        }

        return view('sf-forms.sf1', compact('sections', 'activeYear', 'sectionId', 'section', 'students'));
    }

    // ── SF2: Attendance Register ──────────────────────────────────────────
    public function sf2(Request $request)
    {
        $sections   = Section::with('academicYear')->orderBy('grade_level')->get();
        $sectionId  = $request->input('section_id');
        $month      = (int) $request->input('month', now()->month);
        $year       = (int) $request->input('year', now()->year);
        $section    = null;
        $students   = collect();
        $attendance = collect();
        $daysInMonth = 0;

        if ($sectionId) {
            $section  = Section::with('adviser')->findOrFail($sectionId);
            $students = Enrollment::where('section_id', $sectionId)
                ->where('status', 'enrolled')
                ->with('student')
                ->get()
                ->map->student
                ->filter()
                ->sortBy('last_name')
                ->values();

            $daysInMonth = \Carbon\Carbon::create($year, $month, 1)->daysInMonth;

            $attendance = Attendance::whereHas('sectionSubject', fn($q) => $q->where('section_id', $sectionId))
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->groupBy('student_id');
        }

        if ($request->input('download') === '1' && $section) {
            $pdf = Pdf::loadView('pdf.sf2', compact('section', 'students', 'attendance', 'month', 'year', 'daysInMonth'))
                ->setPaper('legal', 'landscape');
            return $pdf->download("SF2-{$section->section_name}-" . \Carbon\Carbon::create($year, $month)->format('M-Y') . ".pdf");
        }

        return view('sf-forms.sf2', compact('sections', 'sectionId', 'section', 'students', 'attendance', 'month', 'year', 'daysInMonth'));
    }

    // ── SF9: Report Card / Learner Progress Report ───────────────────────
    public function sf9(Request $request)
    {
        $sections   = Section::with('academicYear')->orderBy('grade_level')->get();
        $sectionId  = $request->input('section_id');
        $studentId  = $request->input('student_id');
        $section    = null;
        $student    = null;
        $gradeData  = [];
        $quarters   = collect();
        $students   = collect();

        if ($sectionId) {
            $section  = Section::findOrFail($sectionId);
            $students = Enrollment::where('section_id', $sectionId)
                ->where('status', 'enrolled')
                ->with('student')
                ->get()
                ->map->student
                ->filter()
                ->sortBy('last_name')
                ->values();
        }

        if ($studentId && $sectionId) {
            $student    = User::findOrFail($studentId);
            $enrollment = Enrollment::where('student_id', $studentId)
                ->where('section_id', $sectionId)
                ->first();

            if ($enrollment) {
                $academicYearId = $section->academic_year_id;
                $quarters       = GradingQuarter::where('academic_year_id', $academicYearId)
                    ->orderBy('quarter_number')->get();

                $grades = Grade::whereHas('sectionSubject', fn($q) =>
                        $q->where('academic_year_id', $academicYearId)
                    )
                    ->where('enrollment_id', $enrollment->id)
                    ->whereIn('status', ['finalized', 'locked'])
                    ->with(['sectionSubject.subject', 'gradingQuarter'])
                    ->get();

                foreach ($grades as $grade) {
                    $subj = $grade->sectionSubject?->subject?->subject_name ?? 'Unknown';
                    $qNum = $grade->gradingQuarter?->quarter_number ?? 0;
                    $gradeData[$subj][$qNum] = $grade->final_grade;
                }
            }
        }

        if ($request->input('download') === '1' && $student && $section) {
            $pdf = Pdf::loadView('pdf.sf9', compact('student', 'section', 'gradeData', 'quarters'))
                ->setPaper('letter', 'portrait');
            return $pdf->download("SF9-{$student->last_name}-{$student->first_name}.pdf");
        }

        return view('sf-forms.sf9', compact('sections', 'sectionId', 'studentId', 'section', 'students', 'student', 'gradeData', 'quarters'));
    }

    // ── SF10: Permanent Record ────────────────────────────────────────────
    public function sf10(Request $request)
    {
        $search    = $request->input('search', '');
        $studentId = $request->input('student_id');
        $student   = null;
        $history   = collect();

        $students = User::where('role_id', '01')
            ->when($search, fn($q) => $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('lrn', 'like', "%{$search}%"))
            ->orderBy('last_name')
            ->limit(50)
            ->get();

        if ($studentId) {
            $student = User::findOrFail($studentId);
            $history = Enrollment::where('student_id', $studentId)
                ->with(['section.academicYear', 'section'])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($enrollment) {
                    $grades = Grade::where('enrollment_id', $enrollment->id)
                        ->whereIn('status', ['finalized', 'locked'])
                        ->with(['sectionSubject.subject', 'gradingQuarter'])
                        ->get();
                    return ['enrollment' => $enrollment, 'grades' => $grades];
                });
        }

        if ($request->input('download') === '1' && $student) {
            $pdf = Pdf::loadView('pdf.sf10', compact('student', 'history'))
                ->setPaper('legal', 'portrait');
            return $pdf->download("SF10-{$student->last_name}-{$student->first_name}.pdf");
        }

        return view('sf-forms.sf10', compact('students', 'search', 'studentId', 'student', 'history'));
    }
}
