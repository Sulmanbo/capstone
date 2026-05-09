<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AcademicYear Model
 *
 * Represents an institutional academic year (e.g., 2025-2026).
 * Only ONE academic year can be "Active" at any given time.
 *
 * Attributes:
 * - year_label: String (e.g., "2025-2026")
 * - start_date: Date
 * - end_date: Date
 * - status: Enum ('active', 'inactive', 'archived')
 * - is_active: Boolean (denormalized for performance) — Should mirror status == 'active'
 *
 * Logic:
 * - When an academic year is set to 'active', all others are automatically set to 'inactive'
 * - Only one academic year can be active at a time
 */
class AcademicYear extends Model
{
    protected $table = 'academic_years';

    protected $fillable = [
        'year_label',
        'start_date',
        'end_date',
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
     * When an academic year is marked as active, all others become inactive.
     * This enforces the single-active constraint.
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // If this record is being set to 'active', deactivate all others
            if ($model->isDirty('status') && $model->status === 'active') {
                self::where('id', '!=', $model->id)->update([
                    'status' => 'inactive',
                    'is_active' => false,
                ]);
            }
        });

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
