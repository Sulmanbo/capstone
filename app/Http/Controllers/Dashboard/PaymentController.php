<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\EnrollmentFee;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Student PaymentController
 *
 * Shows the student their enrollment fee, lists the school's accept-payment
 * accounts (each with a QR code), and accepts proof-of-payment submissions.
 *
 * Confirmation happens on the registrar side — the registrar reviews the
 * uploaded proof and clicks Confirm (or Reject). Once a payment is 'paid',
 * the registrar can enlist this student into a section.
 */
class PaymentController extends Controller
{
    /**
     * GET /student/payments
     */
    public function index()
    {
        $user       = auth()->user();
        $activeYear = AcademicYear::where('status', 'active')->first();

        $gradeLevel = $user->grade_level ?? null;
        $fee        = ($activeYear && $gradeLevel)
            ? EnrollmentFee::resolve($activeYear->id, $gradeLevel)
            : null;

        $payments = $activeYear
            ? Payment::where('student_id', $user->id)
                ->where('academic_year_id', $activeYear->id)
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $hasPaid = $activeYear
            ? Payment::studentHasPaid($user->id, $activeYear->id)
            : false;

        $accounts = config('payments.accounts', []);
        $instructions = config('payments.instructions', '');

        return view('dashboard.student-payments', compact(
            'user', 'activeYear', 'gradeLevel', 'fee', 'payments', 'hasPaid',
            'accounts', 'instructions'
        ));
    }

    /**
     * POST /student/payments
     *
     * Student submits proof of bank/e-wallet transfer.
     */
    public function submit(Request $request)
    {
        $accounts   = collect(config('payments.accounts', []));
        $accountIds = $accounts->pluck('id')->all();

        $data = $request->validate([
            'account_id'       => ['required', Rule::in($accountIds)],
            'reference_number' => ['required', 'string', 'max:100'],
            'proof'            => ['required', 'image', 'max:4096'], // 4 MB
        ]);

        $user       = auth()->user();
        $activeYear = AcademicYear::where('status', 'active')->first();

        if (!$activeYear) {
            return back()->withErrors(['payment' => 'There is no active academic year. Contact the registrar.']);
        }

        $fee = EnrollmentFee::resolve($activeYear->id, $user->grade_level);
        if (!$fee) {
            return back()->withErrors(['payment' => 'No enrollment fee has been set for your grade level yet. Contact the registrar.']);
        }

        if (Payment::studentHasPaid($user->id, $activeYear->id)) {
            return back()->with('success', 'You have already paid for this academic year.');
        }

        // Snapshot the chosen account so historical records make sense even
        // if config/payments.php is edited later.
        $account = $accounts->firstWhere('id', $data['account_id']);

        $proofPath = $request->file('proof')->store('payment-proofs', 'public');

        $payment = Payment::create([
            'student_id'       => $user->id,
            'academic_year_id' => $activeYear->id,
            'grade_level'      => $user->grade_level,
            'amount'           => $fee->amount,
            'currency'         => $fee->currency,
            'account_id'       => $account['id'],
            'account_label'    => $account['label'],
            'account_number'   => $account['account_number'],
            'status'           => 'pending',
            'reference_number' => $data['reference_number'],
            'proof_path'       => $proofPath,
        ]);

        AuditLog::record('PAYMENT_SUBMITTED', [
            'payment_id'       => $payment->id,
            'account_id'       => $payment->account_id,
            'reference_number' => $payment->reference_number,
            'amount'           => $payment->amount,
        ]);

        return back()->with('success',
            'Proof of payment submitted. The registrar will verify it and confirm your payment shortly.');
    }
}
