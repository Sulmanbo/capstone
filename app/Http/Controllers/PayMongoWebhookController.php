<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Payment;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * PayMongoWebhookController
 *
 * Receives asynchronous payment notifications from PayMongo. This is the
 * source of truth for online payments: when PayMongo confirms a charge it
 * POSTs here, and we flip the matching Payment to 'paid' so the registrar
 * sees it immediately.
 *
 * This route is CSRF-exempt (PayMongo can't send a CSRF token) — see the
 * exclusion in bootstrap/app.php.
 *
 * Docs: https://developers.paymongo.com/docs/webhooks
 */
class PayMongoWebhookController extends Controller
{
    public function __construct(private PayMongoService $payMongo)
    {
    }

    public function handle(Request $request)
    {
        $rawBody   = $request->getContent();
        $signature = $request->header('Paymongo-Signature');

        if (!$this->payMongo->verifyWebhookSignature($rawBody, $signature)) {
            Log::warning('PayMongo webhook rejected: bad signature.');
            return response()->json(['received' => false], 400);
        }

        $event = json_decode($rawBody, true);
        $type  = data_get($event, 'data.attributes.type');

        // We care about successful payments.
        if (in_array($type, ['checkout_session.payment.paid', 'payment.paid'])) {
            $this->handlePaid($event);
        } elseif ($type === 'payment.failed') {
            $this->handleFailed($event);
        }

        // Always 200 so PayMongo doesn't keep retrying once we've accepted it.
        return response()->json(['received' => true]);
    }

    private function handlePaid(array $event): void
    {
        // Try metadata first (we set payment_id when creating the checkout).
        $paymentId = data_get($event, 'data.attributes.data.attributes.metadata.payment_id')
            ?? data_get($event, 'data.attributes.data.attributes.payment_intent.attributes.metadata.payment_id');

        $payment = null;
        if ($paymentId) {
            $payment = Payment::find($paymentId);
        }

        // Fall back to matching by checkout session id.
        if (!$payment) {
            $checkoutId = data_get($event, 'data.attributes.data.id');
            if ($checkoutId) {
                $payment = Payment::where('provider_reference', $checkoutId)->first();
            }
        }

        if (!$payment) {
            Log::warning('PayMongo webhook: no matching Payment found.', ['event' => $event]);
            return;
        }

        if ($payment->isPaid()) {
            return; // idempotent — already handled
        }

        $payment->update(['status' => 'paid', 'paid_at' => now()]);

        AuditLog::record(AuditLog::PAYMENT_CONFIRMED, [
            'payment_id' => $payment->id,
            'method'     => $payment->method,
            'amount'     => $payment->amount,
            'source'     => 'webhook',
        ]);
    }

    private function handleFailed(array $event): void
    {
        $paymentId = data_get($event, 'data.attributes.data.attributes.metadata.payment_id');
        if (!$paymentId) {
            return;
        }
        $payment = Payment::find($paymentId);
        if ($payment && !$payment->isPaid()) {
            $payment->update(['status' => 'failed']);
            AuditLog::record(AuditLog::PAYMENT_FAILED, ['payment_id' => $payment->id]);
        }
    }
}
