<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * SectionSubject Model
 *
 * Links a subject to a section for an academic year, recording who
 * teaches it, the room, and the weekly schedule.
 *
 * schedule_days is stored as a JSON array of lowercase weekday names:
 *   ["monday", "wednesday", "friday"]
 */
class SectionSubject extends Model
{
    protected $table = 'section_subjects';

    protected $fillable = [
        'section_id',
        'subject_id',
        'faculty_id',
        'academic_year_id',
        'room',
        'schedule_days',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'schedule_days' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'section_subject_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'section_subject_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'section_subject_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeForFaculty($query, int $facultyId)
    {
        return $query->where('faculty_id', $facultyId);
    }

    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('section_id', $sectionId);
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

    /** True if this class meets on the given weekday (lowercase). */
    public function meetsOn(string $dayLower): bool
    {
        return in_array($dayLower, $this->schedule_days ?? []);
    }

    /** True if this class meets today. */
    public function meetsToday(): bool
    {
        return $this->meetsOn(strtolower(now()->format('l')));
    }

    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5) . '–' . substr($this->end_time, 0, 5);
    }

    public function getScheduleDaysLabelAttribute(): string
    {
        $map = [
            'monday'    => 'Mon', 'tuesday'  => 'Tue', 'wednesday' => 'Wed',
            'thursday'  => 'Thu', 'friday'   => 'Fri', 'saturday'  => 'Sat',
        ];
        return implode(', ', array_map(
            fn($d) => $map[$d] ?? ucfirst($d),
            $this->schedule_days ?? []
        ));
    }

    // ── View-compatibility aliases ─────────────────────────────────────────
    // These match the flat property names the views expect, avoiding the need
    // to touch every Blade template when replacing FacultySchedule.

    public function getSubjectNameAttribute(): ?string
    {
        return $this->subject?->subject_name;
    }

    public function getSectionNameAttribute(): ?string
    {
        return $this->section?->section_name;
    }

    public function getDaysAttribute(): array
    {
        return $this->schedule_days ?? [];
    }

    public function getDaysLabelAttribute(): string
    {
        return $this->schedule_days_label;
    }
}
