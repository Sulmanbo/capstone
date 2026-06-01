<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AcademicYear Model
 *
 * Represents an institutional academic year (e.g., 2025-2026).
 *
 * Multiple academic years may be active simultaneously so the registrar can
 * build next year's schedules while the current year is still in progress.
 *
 * Attributes:
 * - year_label: String (e.g., "2025-2026")
 * - start_date: Date
 * - end_date:   Date
 * - term_type:  Enum ('quarterly', 'semestral') — institution may switch per year
 * - status:     Enum ('active', 'inactive', 'archived')
 * - is_active:  Boolean (denormalized for performance) — mirrors status == 'active'
 */
class AcademicYear extends Model
{
    protected $table = 'academic_years';

    protected $fillable = [
        'year_label',
        'start_date',
        'end_date',
        'term_type',
        'status',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────
    public function quarters(): HasMany
    {
        return $this->hasMany(GradingQuarter::class, 'academic_year_id');
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(CurriculumMapping::class, 'academic_year_id');
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class, 'academic_year_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'academic_year_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    // ── Mutators ───────────────────────────────────────────────────────────
    /**
     * Per adviser feedback, multiple academic years may be active simultaneously
     * so the registrar can build next year's schedules while the current year
     * is still in progress. The single-active rule (previously enforced here)
     * has been lifted. The `is_active` denormalized column is kept in sync
     * with `status` via the `saved()` hook below.
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            // Ensure is_active boolean matches status
            if ($model->status === 'active' && !$model->is_active) {
                $model->update(['is_active' => true]);
            } elseif ($model->status !== 'active' && $model->is_active) {
                $model->update(['is_active' => false]);
            }
        });
    }

    // ── Helpers ────────────────────────────────────────────────────────────
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function canBeDeactivated(): bool
    {
        // Prevent deactivating if there are active quarters
        return !$this->quarters()->where('status', 'active')->exists();
    }
}
