<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * OtpMail
 *
 * Sends a 6-digit OTP to the user's email for password recovery.
 * Uses Laravel's Mail facade with SendGrid SMTP (configured in .env).
 *
 * To send:
 *   Mail::to($plainEmail)->send(new OtpMail($otp, $firstName));
 */
class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public string $firstName;
    public int    $expiryMinutes;

    public function __construct(string $otp, string $firstName, int $expiryMinutes = 10)
    {
        $this->otp           = $otp;
        $this->firstName     = $firstName;
        $this->expiryMinutes = $expiryMinutes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'EncryptEd — Your Password Reset OTP',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }
}
