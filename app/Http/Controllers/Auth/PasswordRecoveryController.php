<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\AuditLog;
use App\Models\PasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * PasswordRecoveryController
 *
 * Three-step OTP password recovery flow:
 *
 * Step 1 — /forgot-password         (GET/POST) Enter email → send OTP
 * Step 2 — /forgot-password/verify  (GET/POST) Enter 6-digit OTP
 * Step 3 — /forgot-password/reset   (GET/POST) Set new password
 *
 * Security:
 * - OTP is 6 digits, bcrypt-hashed before storage
 * - Email is never stored plain in password_otps table (SHA-256 hash only)
 * - OTP expires in 10 minutes
 * - Max 3 wrong attempts before OTP is invalidated
 * - Rate limited: 1 OTP request per email per minute
 * - All steps logged to audit trail
 */
class PasswordRecoveryController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;

    // ══════════════════════════════════════════════════════════════════════
    // STEP 1 — Enter email, receive OTP
    // ══════════════════════════════════════════════════════════════════════

    public function showEmailForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:200'],
        ]);

        $email     = strtolower(trim($request->input('email')));
        $emailHash = hash('sha256', $email);

        // ── Rate limit: 1 OTP per email per 60 seconds ─────────────────────
        $recent = PasswordOtp::where('email_hash', $emailHash)
            ->where('created_at', '>=', now()->subMinute())
            ->exists();

        if ($recent) {
            return back()->withErrors([
                'email' => 'Please wait at least 1 minute before requesting another OTP.',
            ]);
        }

        // ── Look up user by email_hash ─────────────────────────────────────
        // Always show success message even if email not found (prevent enumeration)
        $user = User::where('email_hash', $emailHash)
                    ->where('status', 'active')
                    ->first();

        if ($user) {
            // Generate and store OTP
            $plainOtp = PasswordOtp::generateOtp();
            PasswordOtp::createForEmail($emailHash, $plainOtp, self::OTP_EXPIRY_MINUTES);

            // Send email via SendGrid SMTP
            try {
                Mail::to($email)->send(new OtpMail(
                    $plainOtp,
                    $user->first_name,
                    self::OTP_EXPIRY_MINUTES
                ));
            } catch (\Exception $e) {
                \Log::error('OTP email failed: ' . $e->getMessage());
                return back()->withErrors([
                    'email' => 'Failed to send OTP email. Please try again or contact your administrator.',
                ]);
            }

            AuditLog::record(
                AuditLog::PASSWORD_RESET,
                ['step' => 'OTP_SENT', 'note' => 'Password recovery OTP emailed.'],
                $user->id,
                $user->full_name
            );
        }

        // Store email hash in session for next step (never the plain email)
        session(['recovery_email_hash' => $emailHash]);

        return redirect()->route('password.verify-otp')
            ->with('status', 'If that email is registered, a 6-digit OTP has been sent. Check your inbox.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // STEP 2 — Verify OTP
    // ══════════════════════════════════════════════════════════════════════

    public function showVerifyForm()
    {
        if (!session('recovery_email_hash')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'digits:6'],
        ]);

        $emailHash = session('recovery_email_hash');

        if (!$emailHash) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $otpRecord = PasswordOtp::where('email_hash', $emailHash)->first();

        // ── OTP not found or expired ───────────────────────────────────────
        if (!$otpRecord || !$otpRecord->isValid()) {
            PasswordOtp::where('email_hash', $emailHash)->delete();
            session()->forget('recovery_email_hash');
            return redirect()->route('password.request')
                ->withErrors(['email' => 'OTP expired or already used. Please request a new one.']);
        }

        // ── Verify OTP ─────────────────────────────────────────────────────
        if (!$otpRecord->verify($request->input('otp'))) {
            $attemptsLeft = max(0, 3 - $otpRecord->fresh()->attempts);

            if ($attemptsLeft === 0) {
                PasswordOtp::where('email_hash', $emailHash)->delete();
                session()->forget('recovery_email_hash');
                return redirect()->route('password.request')
                    ->withErrors(['email' => 'Too many incorrect attempts. Please request a new OTP.']);
            }

            return back()->withErrors([
                'otp' => "Incorrect OTP. {$attemptsLeft} attempt(s) remaining.",
            ]);
        }

        // ── OTP correct — mark session as verified ─────────────────────────
        session(['recovery_verified' => true]);
        PasswordOtp::where('email_hash', $emailHash)->delete(); // consume it

        return redirect()->route('password.reset-form');
    }

    // ══════════════════════════════════════════════════════════════════════
    // STEP 3 — Set new password
    // ══════════════════════════════════════════════════════════════════════

    public function showResetForm()
    {
        if (!session('recovery_email_hash') || !session('recovery_verified')) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please complete OTP verification first.']);
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        if (!session('recovery_email_hash') || !session('recovery_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => [
                'required', 'string', 'min:8', 'max:64', 'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@#$%^&!?_*]/',
                'not_regex:/[\s\\\\\/]/',
            ],
        ], [
            'password.regex'     => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@, #, $, %, ^, &, !, ?, _).',
            'password.not_regex' => 'Password must not contain spaces, backslashes, or forward slashes.',
        ]);

        $emailHash = session('recovery_email_hash');
        $user      = User::where('email_hash', $emailHash)->first();

        if (!$user) {
            session()->forget(['recovery_email_hash', 'recovery_verified']);
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Account not found. Please try again.']);
        }

        // ── Update password ────────────────────────────────────────────────
        $user->update([
            'password'               => $request->input('password'), // bcrypt via mutator
            'password_reset_required'=> false,
            'failed_attempts'        => 0,
            'status'                 => 'active',
            'locked_until'           => null,
        ]);

        AuditLog::record(
            AuditLog::PASSWORD_CHANGED,
            ['step' => 'OTP_RECOVERY', 'note' => 'Password reset via OTP recovery flow.'],
            $user->id,
            $user->full_name
        );

        // Clear recovery session
        session()->forget(['recovery_email_hash', 'recovery_verified']);

        return redirect()->route('login')
            ->with('status', 'Password reset successfully. You can now log in with your new password.');
    }
}
