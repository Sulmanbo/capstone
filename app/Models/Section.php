<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Section Model
 *
 * Represents a class group (e.g. "Grade 7 – St. Therese") within
 * one academic year. Each section has one homeroom adviser (faculty)
 * and a list of enrolled students.
 */
class Section extends Model
{
    protected $table = 'sections';

    protected $fillable = [
        'section_name',
        'grade_level',
        'academic_year_id',
        'adviser_id',
        'capacity',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'section_id');
    }

    public function sectionSubjects(): HasMany
    {
        return $this->hasMany(SectionSubject::class, 'section_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'section_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForAcademicYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeForGradeLevel($query, string $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
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
        return $this->status === 'active';
    }

    public function isFull(): bool
    {
        return $this->enrollments()->where('status', 'enrolled')->count() >= $this->capacity;
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->grade_level} – {$this->section_name}";
    }
}
