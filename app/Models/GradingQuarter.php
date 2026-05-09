<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * GradingQuarter Model
 *
 * Represents a quarter within an academic year (e.g., 1st Quarter, 2nd Quarter).
 * Only ONE quarter can be "Active" per academic year.
 *
 * Attributes:
 * - academic_year_id: Foreign Key (AcademicYear)
 * - quarter_number: Integer (1, 2, 3, 4)
 * - quarter_name: String (e.g., "1st Quarter")
 * - start_date: Date
 * - end_date: Date
 * - status: Enum ('active', 'inactive', 'archived')
 * - is_active: Boolean (denormalized for performance)
 *
 * Logic:
 * - When a quarter is set to 'active', all other quarters in the SAME academic year are set to 'inactive'
 * - Only one quarter per academic year can be active
 */
class GradingQuarter extends Model
{
    protected $table = 'grading_quarters';

    protected $fillable = [
        'academic_year_id',
        'quarter_number',
        'quarter_name',
        'start_date',
        'end_date',
        'status',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'quarter_number' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
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

    // ── Mutators ───────────────────────────────────────────────────────────
    /**
     * Enforce single-active constraint: Only one quarter per academic year can be active.
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // If this quarter is being set to 'active', deactivate siblings in same academic year
            if ($model->isDirty('status') && $model->status === 'active') {
                self::where('academic_year_id', $model->academic_year_id)
                    ->where('id', '!=', $model->id)
                    ->update([
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

    public function getDisplayName(): string
    {
        return "{$this->quarter_name} ({$this->academicYear->year_label})";
    }
}
