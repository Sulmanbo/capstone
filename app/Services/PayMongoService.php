<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PayMongoService
 *
 * Wraps PayMongo's Checkout Sessions API. We use HOSTED checkout — the parent
 * types their card / e-wallet details on PayMongo's own secure page, never on
 * our server. This is what keeps card data off our infrastructure (PCI-DSS).
 *
 * Flow:
 *   1. createCheckout() asks PayMongo for a checkout session and returns a URL.
 *   2. We redirect the parent to that URL; they pay on PayMongo's page.
 *   3. PayMongo charges the card and settles funds to the school's PayMongo
 *      balance (which the school later withdraws to their bank account — set up
 *      once during business onboarding, never in our code).
 *   4. PayMongo calls our webhook; we mark the Payment 'paid'.
 *
 * In TEST mode this all works with PayMongo's test cards (e.g. 4343434343434345)
 * and moves no real money. Going live = swap the keys in .env. No code change.
 *
 * Docs: https://developers.paymongo.com/docs/checkout-api
 */
class PayMongoService
{
    private string $baseUrl = 'https://api.paymongo.com/v1';

    private function secretKey(): string
    {
        return (string) config('services.paymongo.secret');
    }

    public function isConfigured(): bool
    {
        return $this->secretKey() !== '';
    }

    /**
     * Create a PayMongo Checkout Session for a Payment and return the hosted URL.
     *
     * @param  Payment $payment   The pending Payment row (amount, method, etc.)
     * @param  string  $successUrl Where PayMongo sends the user after success
     * @param  string  $cancelUrl  Where PayMongo sends the user on cancel
     * @return array{checkout_url:string, reference:string}
     *
     * @throws \RuntimeException on API failure
     */
    public function createCheckout(Payment $payment, string $successUrl, string $cancelUrl): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('PayMongo is not configured. Set PAYMONGO_SECRET_KEY in your .env.');
        }

        // PayMongo wants amounts in centavos (integer).
        $amountCentavos = (int) round(((float) $payment->amount) * 100);

        // Map our method to PayMongo payment_method_types.
        $methodMap = [
            'card'     => ['card'],
            'gcash'    => ['gcash'],
            'grab_pay' => ['grab_pay'],
        ];
        $paymentMethodTypes = $methodMap[$payment->method] ?? ['card'];

        $payload = [
            'data' => [
                'attributes' => [
                    'payment_method_types' => $paymentMethodTypes,
                    'line_items' => [[
                        'name'     => "Enrollment Fee — {$payment->grade_level}",
                        'quantity' => 1,
                        'amount'   => $amountCentavos,
                        'currency' => $payment->currency ?: 'PHP',
                    ]],
                    'description'  => "Enrollment fee for student #{$payment->student_id} ({$payment->grade_level})",
                    'success_url'  => $successUrl,
                    'cancel_url'   => $cancelUrl,
                    // We pass our payment id so the webhook can find it again.
                    'metadata' => [
                        'payment_id' => (string) $payment->id,
                        'student_id' => (string) $payment->student_id,
                    ],
                ],
            ],
        ];

        $response = Http::withBasicAuth($this->secretKey(), '')
            ->acceptJson()
            ->post("{$this->baseUrl}/checkout_sessions", $payload);

        if (!$response->successful()) {
            Log::error('PayMongo checkout creation failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new \RuntimeException('Could not create the PayMongo checkout session. ' .
                'Check your API keys and that the amount is at least ₱20.00.');
        }

        $data = $response->json('data');
        $checkoutUrl = $data['attributes']['checkout_url'] ?? null;
        $reference   = $data['id'] ?? null;

        if (!$checkoutUrl || !$reference) {
            throw new \RuntimeException('PayMongo returned an unexpected response.');
        }

        return ['checkout_url' => $checkoutUrl, 'reference' => $reference];
    }

    /**
     * Retrieve a checkout session (used to confirm payment status on
     * success-redirect, as a backup to the webhook).
     */
    public function getCheckout(string $reference): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $response = Http::withBasicAuth($this->secretKey(), '')
            ->acceptJson()
            ->get("{$this->baseUrl}/checkout_sessions/{$reference}");

        return $response->successful() ? $response->json('data') : null;
    }

    /**
     * Given a checkout-session payload, has it been paid?
     *
     * A checkout session is paid when its payments[] array contains a payment
     * with status 'paid'.
     */
    public function checkoutIsPaid(array $checkout): bool
    {
        $payments = data_get($checkout, 'attributes.payments', []);
        foreach ($payments as $p) {
            if (data_get($p, 'attributes.status') === 'paid') {
                return true;
            }
        }
        return false;
    }

    /**
     * Verify a webhook signature from PayMongo.
     *
     * PayMongo sends a `Paymongo-Signature` header of the form:
     *   t=timestamp,te=test_signature,li=live_signature
     * The signature is HMAC-SHA256 of "{timestamp}.{rawBody}" using the
     * webhook secret. We compare against te (test) or li (live).
     *
     * Docs: https://developers.paymongo.com/docs/webhooks
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader): bool
    {
        $secret = (string) config('services.paymongo.webhook_secret');

        // If no webhook secret configured (common in early dev), skip verification
        // but log it so it's obvious in the defense environment.
        if ($secret === '') {
            Log::warning('PayMongo webhook secret not set — skipping signature verification.');
            return true;
        }

        if (!$signatureHeader) {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $piece) {
            [$k, $v] = array_pad(explode('=', $piece, 2), 2, '');
            $parts[trim($k)] = trim($v);
        }

        $timestamp = $parts['t'] ?? '';
        $provided  = $parts['te'] ?? $parts['li'] ?? '';
        if ($timestamp === '' || $provided === '') {
            return false;
        }

        $expected = hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);

        return hash_equals($expected, $provided);
    }
}
