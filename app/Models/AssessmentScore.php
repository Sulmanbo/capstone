<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AssessmentScore Model
 *
 * One row = one student's score on one assessment.
 * score is nullable to allow placeholder rows before grading.
 */
class AssessmentScore extends Model
{
    protected $table = 'assessment_scores';

    protected $fillable = [
        'assessment_id',
        'enrollment_id',
        'score',
        'submitted_at',
        'graded_at',
        'feedback',
    ];

    protected $casts = [
        'score'        => 'float',
        'submitted_at' => 'datetime',
        'graded_at'    => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Return score as a percentage of the assessment's max_score.
     */
    public function getPercentageAttribute(): ?float
    {
        if (is_null($this->score) || $this->assessment?->max_score == 0) {
            return null;
        }
        return round(($this->score / $this->assessment->max_score) * 100, 2);
    }

    public function isGraded(): bool
    {
        return !is_null($this->score);
    }
}
