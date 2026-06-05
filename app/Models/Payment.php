<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Payment
 *
 * One payment submission by a student toward their enrollment fee.
 *
 * Flow:
 *   - Student picks one of the school's configured accounts (BDO/BPI/GCash),
 *     transfers manually using their banking or e-wallet app, then uploads
 *     a receipt screenshot + reference number here.
 *   - Status starts 'pending'. Registrar verifies the proof and flips it to
 *     'paid' (or 'failed' if the proof is invalid).
 *
 * A student is considered "paid" (and thus enlistable) when they have at
 * least one Payment with status 'paid' for the academic year in question.
 */
class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'grade_level',
        'amount',
        'currency',
        'account_id',
        'account_label',
        'account_number',
        'status',
        'proof_path',
        'reference_number',
        'paid_at',
        'confirmed_by',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForYear($query, int $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Has this student fully paid for the given academic year?
     * The enlistment gate uses this.
     */
    public static function studentHasPaid(int $studentId, int $academicYearId): bool
    {
        return static::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'paid')
            ->exists();
    }
}
