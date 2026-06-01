<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Applicant extends Model
{
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'suffix',
        'date_of_birth', 'sex', 'lrn', 'nationality',
        'address', 'barangay', 'municipality', 'province', 'zip_code',
        'previous_school', 'previous_grade_level', 'school_year_completed',
        'applying_for_grade', 'applying_for_year',
        'parent_guardian_name', 'relationship', 'parent_contact', 'parent_email',
        'reference_number', 'status', 'remarks', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        // date_of_birth is handled by encrypted accessor — no cast here
        'reviewed_at' => 'datetime',
    ];

    // ── Boot ────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Applicant $model) {
            if (!$model->reference_number) {
                $model->reference_number = static::generateReference();
            }
        });
    }

    // ══════════════════════════════════════════════════════════════════════
    // AES-256 ENCRYPTED ACCESSORS & MUTATORS  (RA 10173 sensitive PII)
    // Searchable fields (first_name, last_name, lrn, reference_number)
    // are left as plain text so LIKE queries continue to work.
    // ══════════════════════════════════════════════════════════════════════

    private function decrypt(?string $value): ?string
    {
        if (is_null($value)) return null;
        try { return Crypt::decryptString($value); } catch (\Exception) { return $value; }
    }

    private function encrypt(?string $value): ?string
    {
        return $value ? Crypt::encryptString($value) : null;
    }

    // ── date_of_birth ──────────────────────────────────────────────────────
    public function getDateOfBirthAttribute(?string $value): ?Carbon
    {
        $plain = $this->decrypt($value);
        return $plain ? Carbon::parse($plain) : null;
    }

    public function setDateOfBirthAttribute(mixed $value): void
    {
        if ($value instanceof Carbon) {
            $this->attributes['date_of_birth'] = $this->encrypt($value->format('Y-m-d'));
        } elseif ($value) {
            $this->attributes['date_of_birth'] = $this->encrypt((string) $value);
        } else {
            $this->attributes['date_of_birth'] = null;
        }
    }

    // ── address ────────────────────────────────────────────────────────────
    public function getAddressAttribute(?string $value): ?string { return $this->decrypt($value); }
    public function setAddressAttribute(?string $value): void    { $this->attributes['address'] = $this->encrypt($value); }

    // ── barangay ───────────────────────────────────────────────────────────
    public function getBarangayAttribute(?string $value): ?string { return $this->decrypt($value); }
    public function setBarangayAttribute(?string $value): void    { $this->attributes['barangay'] = $this->encrypt($value); }

    // ── municipality ───────────────────────────────────────────────────────
    public function getMunicipalityAttribute(?string $value): ?string { return $this->decrypt($value); }
    public function setMunicipalityAttribute(?string $value): void    { $this->attributes['municipality'] = $this->encrypt($value); }

    // ── province ───────────────────────────────────────────────────────────
    public function getProvinceAttribute(?string $value): ?string { return $this->decrypt($value); }
    public function setProvinceAttribute(?string $value): void    { $this->attributes['province'] = $this->encrypt($value); }

    // ── parent_guardian_name ───────────────────────────────────────────────
    public function getParentGuardianNameAttribute(?string $value): ?string { return $this->decrypt($value); }
    public function setParentGuardianNameAttribute(?string $value): void    { $this->attributes['parent_guardian_name'] = $this->encrypt($value); }

    // ── parent_contact ─────────────────────────────────────────────────────
    public function getParentContactAttribute(?string $value): ?string { return $this->decrypt($value); }
    public function setParentContactAttribute(?string $value): void    { $this->attributes['parent_contact'] = $this->encrypt($value); }

    // ── parent_email ───────────────────────────────────────────────────────
    public function getParentEmailAttribute(?string $value): ?string { return $this->decrypt($value); }
    public function setParentEmailAttribute(?string $value): void    { $this->attributes['parent_email'] = $this->encrypt($value); }

    // ── Relationships ────────────────────────────────────────────────────────

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function entranceTestResult(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EntranceTestResult::class, 'applicant_id');
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'under_review']);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ]));
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'APP-' . date('Y') . '-' . strtoupper(Str::random(6));
        } while (static::where('reference_number', $ref)->exists());

        return $ref;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['pending', 'under_review']);
    }
}
