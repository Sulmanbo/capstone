<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Schedule Model
 *
 * Represents a calendar placement of a section-subject pair at a specific
 * day/time/room. Faculty assignment is optional — schedules begin as TBA
 * and transition to "assigned" once a teacher is chosen.
 *
 * Conflict rules enforced via ScheduleConflictService at controller level:
 *   - Faculty time conflict
 *   - Classroom time conflict
 *   - Minimum 2-hour duration
 *
 * Database-level rules:
 *   - schedules_unique_subject_per_section: no duplicate subject in a section/year
 *
 * Field shape:
 *   - schedule_days: JSON array of lowercase weekday names
 *   - start_time, end_time: HH:MM:SS
 */
class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'academic_year_id',
        'section_id',
        'subject_id',
        'classroom_id',
        'faculty_id',
        'schedule_days',
        'start_time',
        'end_time',
        'status',
        'section_subject_id',
    ];

    protected $casts = [
        'schedule_days' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'faculty_id');
    }

    public function sectionSubject(): BelongsTo
    {
        return $this->belongsTo(SectionSubject::class, 'section_subject_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeTba($query)
    {
        return $query->where('status', 'tba');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeForYear($query, int $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isTba(): bool
    {
        return $this->status === 'tba' || empty($this->faculty_id);
    }

    public function durationInHours(): float
    {
        $s = strtotime($this->start_time);
        $e = strtotime($this->end_time);
        return $s && $e ? ($e - $s) / 3600 : 0;
    }
}
