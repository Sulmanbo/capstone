<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

/**
 * User Model — EncryptEd
 *
 * ENCRYPTION STRATEGY
 * ───────────────────
 * Only email is AES-256 encrypted (RA 10173 sensitive PII).
 * first_name and last_name are stored as plain text so admins
 * can identify users directly in the DB and LIKE searches work.
 * username is plain text — it's a non-sensitive login identifier.
 *
 * email_hash stores SHA-256 of the lowercase email so unique
 * lookups work without decrypting every row.
 *
 * HASHING STRATEGY
 * ────────────────
 * Passwords use bcrypt (Hash::make()) at cost factor 12.
 * SHA-256 is used only for the email_hash lookup column.
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    // ── Mass-assignable fields ─────────────────────────────────────────────
    protected $fillable = [
        'first_name',           // plain text — searchable, admin-readable
        'last_name',            // plain text — searchable, admin-readable
        'email',                // AES-256 encrypted (sensitive PII)
        'email_hash',           // SHA-256 of lowercase email (for unique lookups)
        'username',             // plain text — login identifier, not sensitive
        'password',             // bcrypt hash
        'role_id',
        'lrn',
        'employee_number',
        'gender',               // male or female
        'address',
        'phone',
        'preferences',          // JSON: notification prefs, consultation hours, etc.
        'password_reset_required',
        'failed_attempts',
        'locked_until',
        'status',
        'last_login_at',
        'last_login_ip',
        // ── Student-specific fields ────────────────────────────────────────
        'grade_level',
        'section_id',
        'enrollment_date',
        'parent_name',          // AES-256 encrypted (sensitive PII)
        'parent_contact',       // AES-256 encrypted (sensitive PII)
        'lrn_status',
    ];

    // ── Hidden from serialization ──────────────────────────────────────────
    protected $hidden = [
        'password',
        'remember_token',
        'email_hash',
    ];

    protected $casts = [
        'password_reset_required' => 'boolean',
        'failed_attempts'         => 'integer',
        'locked_until'            => 'datetime',
        'last_login_at'           => 'datetime',
        'email_verified_at'       => 'datetime',
        'preferences'             => 'array',
        'enrollment_date'         => 'date',
    ];

    // ══════════════════════════════════════════════════════════════════════
    // AES-256 ENCRYPTED ACCESSOR & MUTATOR — email only
    // first_name, last_name, username are plain text (searchable)
    // ══════════════════════════════════════════════════════════════════════

    // ── email ──────────────────────────────────────────────────────────────
    public function getEmailAttribute(string $value): string
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setEmailAttribute(string $value): void
    {
        $normalized = strtolower(trim($value));
        $this->attributes['email']      = Crypt::encryptString($normalized);
        $this->attributes['email_hash'] = hash('sha256', $normalized);
    }

    // ── parent_name (AES-256) ──────────────────────────────────────────────
    public function getParentNameAttribute(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return $value;
        }
    }

    public function setParentNameAttribute(?string $value): void
    {
        $this->attributes['parent_name'] = $value ? Crypt::encryptString($value) : null;
    }

    // ── parent_contact (AES-256) ───────────────────────────────────────────
    public function getParentContactAttribute(?string $value): ?string
    {
        if (is_null($value)) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return $value;
        }
    }

    public function setParentContactAttribute(?string $value): void
    {
        $this->attributes['parent_contact'] = $value ? Crypt::encryptString($value) : null;
    }

    // ── password (bcrypt) ──────────────────────────────────────────────────
    public function setPasswordAttribute(string $value): void
    {
        // Only hash if not already hashed
        $this->attributes['password'] = Hash::needsRehash($value)
            ? Hash::make($value, ['rounds' => 12])
            : $value;
    }

    // ══════════════════════════════════════════════════════════════════════
    // CONVENIENCE ATTRIBUTES
    // ══════════════════════════════════════════════════════════════════════

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role_id) {
            '01' => 'Student',
            '02' => 'Faculty',
            '03' => 'Registrar',
            '04' => 'Admin',
            default => 'Unknown',
        };
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->role_id === '04';
    }

    public function getIsFacultyAttribute(): bool
    {
        return $this->role_id === '02';
    }

    public function getIsStudentAttribute(): bool
    {
        return $this->role_id === '01';
    }

    // ══════════════════════════════════════════════════════════════════════
    // SECURITY HELPERS
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Increment failed login counter.
     * If it reaches 5, lock the account for 10 minutes.
     */
    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_attempts');
        $this->refresh();

        if ($this->failed_attempts >= 5) {
            $this->update([
                'status'       => 'locked',
                'locked_until' => now()->addMinutes(10),
            ]);
        }
    }

    /**
     * Reset failed counter and unlock after successful login.
     */
    public function clearFailedAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'status'          => 'active',
            'locked_until'    => null,
        ]);
    }

    /**
     * Check if the account lockout period has expired and auto-unlock.
     */
    public function checkAndUnlock(): void
    {
        if ($this->status === 'locked'
            && $this->locked_until
            && now()->isAfter($this->locked_until))
        {
            $this->update([
                'status'          => 'active',
                'failed_attempts' => 0,
                'locked_until'    => null,
            ]);
        }
    }

    public function pref(string $key, mixed $default = null): mixed
    {
        return ($this->preferences ?? [])[$key] ?? $default;
    }

    public function mergePreferences(array $new): void
    {
        $this->update(['preferences' => array_merge($this->preferences ?? [], $new)]);
    }

    // ══════════════════════════════════════════════════════════════════════
    // RELATIONSHIPS
    // ══════════════════════════════════════════════════════════════════════

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function activeEnrollment()
    {
        return $this->hasOne(Enrollment::class, 'student_id')
                    ->where('status', 'enrolled');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function threatEvents()
    {
        return $this->hasMany(ThreatEvent::class, 'user_id');
    }
}
