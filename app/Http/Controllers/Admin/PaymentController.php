<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\EnrollmentFee;
use App\Models\Payment;
use Illuminate\Http\Request;

/**
 * Admin/Registrar PaymentController
 *
 * Registrar reviews proofs of payment and either confirms or rejects them.
 * A confirmed payment makes the student enlistable. Also manages the
 * per-grade-level enrollment fees.
 */
class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $yearId = $request->input('academic_year_id') ?? AcademicYear::currentId();
        $status = $request->input('status');

        $payments = Payment::query()
            ->with(['student', 'academicYear', 'confirmedBy'])
            ->when($yearId, fn($q) => $q->where('academic_year_id', $yearId))
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'paid'    => Payment::where('academic_year_id', $yearId)->where('status', 'paid')->count(),
            'pending' => Payment::where('academic_year_id', $yearId)->where('status', 'pending')->count(),
            'failed'  => Payment::where('academic_year_id', $yearId)->where('status', 'failed')->count(),
        ];

        return view('admin.payments.index', compact(
            'payments', 'academicYears', 'yearId', 'status', 'stats'
        ));
    }

    /**
     * POST /admin/payments/{payment}/confirm
     */
    public function confirm(Payment $payment)
    {
        if ($payment->isPaid()) {
            return back()->with('success', 'This payment is already confirmed.');
        }

        $payment->update([
            'status'       => 'paid',
            'paid_at'      => now(),
            'confirmed_by' => auth()->id(),
        ]);

        AuditLog::record('PAYMENT_CONFIRMED', [
            'payment_id'   => $payment->id,
            'student_id'   => $payment->student_id,
            'amount'       => $payment->amount,
            'confirmed_by' => auth()->id(),
        ]);

        return back()->with('success',
            "Payment confirmed for {$payment->student?->first_name} {$payment->student?->last_name}. You can now enlist this student into a section.");
    }

    /**
     * POST /admin/payments/{payment}/reject
     */
    public function reject(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $payment->update([
            'status' => 'failed',
            'notes'  => $data['notes'] ?? 'Rejected by registrar.',
        ]);

        AuditLog::record('PAYMENT_REJECTED', [
            'payment_id' => $payment->id,
            'notes'      => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Payment marked as rejected.');
    }

    // ── Enrollment fee management ──────────────────────────────────────────

    public function fees(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $yearId = $request->input('academic_year_id') ?? AcademicYear::currentId();

        $fees = $yearId
            ? EnrollmentFee::where('academic_year_id', $yearId)->orderBy('grade_level')->get()
            : collect();

        return view('admin.payments.fees', compact('academicYears', 'yearId', 'fees'));
    }

    public function storeFee(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'grade_level'      => ['required', 'string', 'max:20'],
            'amount'           => ['required', 'numeric', 'min:1'],
        ]);

        $fee = EnrollmentFee::updateOrCreate(
            ['academic_year_id' => $data['academic_year_id'], 'grade_level' => $data['grade_level']],
            ['amount' => $data['amount'], 'currency' => 'PHP'],
        );

        AuditLog::record('ENROLLMENT_FEE_UPDATED', [
            'fee_id'      => $fee->id,
            'grade_level' => $fee->grade_level,
            'amount'      => $fee->amount,
        ]);

        return back()->with('success',
            "Fee for {$fee->grade_level} set to ₱" . number_format($fee->amount, 2) . ".");
    }
}
