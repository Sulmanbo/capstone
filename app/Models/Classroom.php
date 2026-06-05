<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Classroom Model
 *
 * A physical room (or lab) where classes are scheduled. Scoped to one
 * academic year so the registrar can rebuild the room roster each year
 * without losing historical schedules.
 */
class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classrooms';

    protected $fillable = [
        'academic_year_id',
        'room_name',
        'building',
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

    public function sectionSubjects(): HasMany
    {
        return $this->hasMany(SectionSubject::class, 'classroom_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForYear($query, int $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getDisplayNameAttribute(): string
    {
        return $this->building
            ? "{$this->building} — {$this->room_name}"
            : $this->room_name;
    }
}
