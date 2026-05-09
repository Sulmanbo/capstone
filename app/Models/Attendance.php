<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Attendance Model
 *
 * One row = one student's attendance status for one subject on one date.
 * The DB unique constraint prevents duplicate entries.
 */
class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'enrollment_id',
        'section_subject_id',
        'date',
        'status',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function sectionSubject(): BelongsTo
    {
        return $this->belongsTo(SectionSubject::class, 'section_subject_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeExcused($query)
    {
        return $query->where('status', 'excused');
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForActiveAcademicYear($query)
    {
        $activeYear = AcademicYear::where('status', 'active')->first();
        if (!$activeYear) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereHas(
            'sectionSubject',
            fn($q) => $q->where('academic_year_id', $activeYear->id)
        );
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isPresent(): bool
    {
        return in_array($this->status, ['present', 'late']);
    }
}
