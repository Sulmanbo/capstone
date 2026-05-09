<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PasswordOtp extends Model
{
    const UPDATED_AT = null;

    protected $table = 'password_otps';

    protected $fillable = [
        'email_hash',
        'otp_hash',
        'attempts',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'attempts'   => 'integer',
    ];

    // ── Helpers ────────────────────────────────────────────────────────────

    /** Generate a cryptographically secure 6-digit OTP */
    public static function generateOtp(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /** Hash the email for storage (no plain email in this table) */
    public static function hashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    /** Create or replace an OTP for the given email hash */
    public static function createForEmail(string $emailHash, string $plainOtp, int $expiryMinutes = 10): self
    {
        // Delete any existing OTP for this email first
        static::where('email_hash', $emailHash)->delete();

        return static::create([
            'email_hash' => $emailHash,
            'otp_hash'   => Hash::make($plainOtp),
            'attempts'   => 0,
            'expires_at' => now()->addMinutes($expiryMinutes),
        ]);
    }

    /** Check if the OTP is still valid (not expired, not maxed out) */
    public function isValid(): bool
    {
        return $this->attempts < 3 && now()->isBefore($this->expires_at);
    }

    /** Verify a plain OTP against the stored hash */
    public function verify(string $plainOtp): bool
    {
        if (!$this->isValid()) return false;

        $this->increment('attempts');

        if (Hash::check($plainOtp, $this->otp_hash)) {
            return true;
        }

        return false;
    }
}
