<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Enrollment Model
 *
 * One row = one student enrolled in one section for one academic year.
 * The DB unique constraint (student_id, academic_year_id) is the
 * authoritative guard against double-enrollment; this model exposes
 * it as a validation helper as well.
 */
class Enrollment extends Model
{
    protected $table = 'enrollments';

    protected $fillable = [
        'student_id',
        'section_id',
        'academic_year_id',
        'status',
        'enrolled_at',
        'dropped_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'dropped_at'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'enrollment_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'enrollment_id');
    }

    public function assessmentScores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'enrollment_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeForAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeForActiveAcademicYear($query)
    {
        $activeYear = AcademicYear::where('status', 'active')->first();
        return $activeYear
            ? $query->where('academic_year_id', $activeYear->id)
            : $query->whereRaw('1 = 0');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'enrolled';
    }

    /**
     * Check whether a student already has an active enrollment for
     * the given academic year. Used before creating a new enrollment.
     */
    public static function existsForStudentAndYear(int $studentId, int $academicYearId): bool
    {
        return static::where('student_id', $studentId)
                     ->where('academic_year_id', $academicYearId)
                     ->exists();
    }
}
