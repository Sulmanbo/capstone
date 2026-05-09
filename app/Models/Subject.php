<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Subject Model
 *
 * Master database of all subjects offered by the institution.
 * Each subject has a unique, immutable Subject ID for database normalization.
 *
 * Attributes:
 * - subject_id: String (Immutable identifier — e.g., "MATH-001", "ENG-101")
 * - subject_code: String (Unique code — e.g., "MTH101", "ENG101")
 * - subject_name: String (e.g., "Mathematics", "English Language")
 * - description: Text (Optional detailed description)
 * - credits: Integer (Number of credit hours, if applicable)
 * - status: Enum ('active', 'inactive')
 *
 * Logic:
 * - subject_id is generated once and is immutable (never changes)
 * - subject_code must be unique
 * - Only active subjects can be assigned to curricula
 */
class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = [
        'subject_id',
        'subject_code',
        'subject_name',
        'description',
        'credits',
        'status',
    ];

    protected $casts = [
        'credits' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────
    public function curriculumMappings(): HasMany
    {
        return $this->hasMany(CurriculumMapping::class, 'subject_id', 'id');
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

    public function scopeByCode($query, $code)
    {
        return $query->where('subject_code', $code);
    }

    // ── Boot / Mutators ────────────────────────────────────────────────────
    public static function boot()
    {
        parent::boot();

        /**
         * Generate immutable subject_id on creation.
         * Format: SUBJ-{XXXXXXXXX} (9 random alphanumeric characters for uniqueness)
         */
        static::creating(function ($model) {
            if (!$model->subject_id) {
                $model->subject_id = 'SUBJ-' . Str::random(9);
            }
        });

        /**
         * Prevent modification of subject_id (immutable policy).
         */
        static::updating(function ($model) {
            if ($model->isDirty('subject_id')) {
                $model->getAttributes()['subject_id'] = $model->getOriginal('subject_id');
                $model->syncOriginal();
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
        return "{$this->subject_code} - {$this->subject_name}";
    }

    /**
     * Check if subject is used in any curriculum
     */
    public function isUsedInCurriculum(): bool
    {
        return $this->curriculumMappings()->exists();
    }
}
