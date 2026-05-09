<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradingQuarter;
use App\Models\ReportCardToken;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    // ── Shared: build grade data array for a student + year ────────────────

    private function buildGradeData(User $student, AcademicYear $year): array
    {
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('academic_year_id', $year->id)
            ->with(['section'])
            ->first();

        $quarters = GradingQuarter::where('academic_year_id', $year->id)
            ->orderBy('quarter_number')
            ->get();

        // Finalized/locked grades for this student in this year
        $grades = Grade::whereHas('sectionSubject', fn($q) =>
                $q->where('academic_year_id', $year->id)
            )
            ->where('enrollment_id', $enrollment?->id ?? 0)
            ->whereIn('status', ['finalized', 'locked'])
            ->with(['sectionSubject.subject', 'gradingQuarter'])
            ->get();

        // Group by subject, then by quarter number
        $subjectGrades = [];
        foreach ($grades as $grade) {
            $subjectName = $grade->sectionSubject?->subject?->subject_name ?? 'Unknown';
            $qNum        = $grade->gradingQuarter?->quarter_number ?? 0;

            $subjectGrades[$subjectName][$qNum] = [
                'final_grade'  => $grade->final_grade,
                'descriptor'   => $grade->descriptor,
                'status'       => $grade->status,
            ];
        }

        // Compute general average per subject across all quarters
        $rows = [];
        foreach ($subjectGrades as $subject => $quarterData) {
            $avg = collect($quarterData)->avg('final_grade');
            $rows[$subject] = [
                'quarters' => $quarterData,
                'average'  => $avg ? round($avg, 2) : null,
            ];
        }

        $overallAvg = collect($rows)->avg('average');

        return [
            'student'    => $student,
            'enrollment' => $enrollment,
            'year'       => $year,
            'quarters'   => $quarters,
            'rows'       => $rows,
            'overall'    => $overallAvg ? round($overallAvg, 2) : null,
        ];
    }

    // ── Serializable fingerprint for tamper detection ───────────────────────

    private function fingerprintData(array $data): array
    {
        $flat = [];
        foreach ($data['rows'] as $subject => $info) {
            foreach ($info['quarters'] as $qNum => $q) {
                $flat["{$subject}_Q{$qNum}"] = $q['final_grade'];
            }
        }
        return $flat;
    }

    // ── Generate and download PDF ───────────────────────────────────────────

    public function download(Request $request, User $student): Response
    {
        // Students can only download their own; admin/registrar can download any
        $user = auth()->user();
        if ($user->role_id === '01' && $user->id !== $student->id) {
            abort(403);
        }

        $year = AcademicYear::where('status', 'active')->first()
            ?? AcademicYear::latest()->first();

        abort_unless($year, 404, 'No academic year found.');

        $data = $this->buildGradeData($student, $year);

        // Issue / reuse a verification token for this student+year
        $token = ReportCardToken::where('student_id', $student->id)
            ->where('academic_year_id', $year->id)
            ->whereNull('quarter_number')
            ->latest('generated_at')
            ->first();

        $fingerprint = $this->fingerprintData($data);
        $dataHash    = ReportCardToken::hashGradeData($fingerprint);

        if (!$token || $token->data_hash !== $dataHash) {
            $token = ReportCardToken::create([
                'student_id'       => $student->id,
                'academic_year_id' => $year->id,
                'quarter_number'   => null,
                'token'            => ReportCardToken::generateToken(),
                'data_hash'        => $dataHash,
                'generated_by'     => $user->id,
            ]);
        }

        // Generate QR code pointing to the public verify URL
        $verifyUrl = route('report-card.verify', $token->token);
        $qrCode    = new QrCode($verifyUrl);
        $writer    = new PngWriter();
        $result    = $writer->write($qrCode);
        $qrDataUri = 'data:image/png;base64,' . base64_encode($result->getString());

        AuditLog::record(AuditLog::EXPORT_REPORT, [
            'report'     => 'report_card_pdf',
            'student_id' => $student->id,
            'token'      => $token->token,
        ]);

        $pdf = Pdf::loadView('pdf.report-card', array_merge($data, [
            'token'      => $token,
            'verifyUrl'  => $verifyUrl,
            'qrDataUri'  => $qrDataUri,
            'generatedAt'=> now(),
        ]));

        $pdf->setPaper('A4', 'portrait');

        $filename = "report_card_{$student->lrn}_{$year->year_label}.pdf";
        $filename = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $filename);

        return $pdf->download($filename);
    }

    // ── Public verification endpoint (no auth required) ─────────────────────

    public function verify(string $token): View
    {
        $record = ReportCardToken::where('token', $token)
            ->with(['student', 'academicYear', 'generatedBy'])
            ->firstOrFail();

        $data        = $this->buildGradeData($record->student, $record->academicYear);
        $fingerprint = $this->fingerprintData($data);
        $currentHash = ReportCardToken::hashGradeData($fingerprint);

        $intact = hash_equals($record->data_hash, $currentHash);

        return view('report-card.verify', compact('record', 'data', 'intact'));
    }
}
