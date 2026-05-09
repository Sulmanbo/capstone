<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

/**
 * Grade Model
 *
 * Stores one student's grade for one subject in one quarter.
 *
 * Workflow: draft → submitted → finalized → locked
 *
 * Once the status reaches 'locked', the updating() boot hook
 * throws a RuntimeException to prevent any further edits.
 *
 * computeFinalGrade() uses the DepEd weights from config/academic.php:
 *   WW 30% + PT 50% + QA 20%
 */
class Grade extends Model
{
    protected $table = 'grades';

    protected $fillable = [
        'enrollment_id',
        'section_subject_id',
        'grading_quarter_id',
        'written_work',
        'performance_task',
        'quarterly_assessment',
        'final_grade',
        'status',
        'submitted_at',
        'submitted_by',
        'finalized_at',
        'finalized_by',
        'remarks',
    ];

    protected $casts = [
        'written_work'          => 'float',
        'performance_task'      => 'float',
        'quarterly_assessment'  => 'float',
        'final_grade'           => 'float',
        'submitted_at'          => 'datetime',
        'finalized_at'          => 'datetime',
    ];

    // ── Boot — immutability guard ──────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function (Grade $model) {
            if ($model->getOriginal('status') === 'locked') {
                throw new RuntimeException('Locked grade records cannot be modified.');
            }
        });
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function sectionSubject(): BelongsTo
    {
        return $this->belongsTo(SectionSubject::class, 'section_subject_id');
    }

    public function gradingQuarter(): BelongsTo
    {
        return $this->belongsTo(GradingQuarter::class, 'grading_quarter_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    public function scopeForActiveAcademicYear($query)
    {
        $activeYear = AcademicYear::where('status', 'active')->first();
        if (!$activeYear) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereHas(
            'sectionSubject',
            fn($q) => $q->where('academic_year_id', $activeYear->id)
        );
    }

    // ── Business Logic ─────────────────────────────────────────────────────

    /**
     * Compute the DepEd final grade from the three components.
     * Returns null if any component is missing.
     */
    public function computeFinalGrade(): ?float
    {
        if (is_null($this->written_work)
            || is_null($this->performance_task)
            || is_null($this->quarterly_assessment))
        {
            return null;
        }

        $w = config('academic.grade_weights');

        return round(
            ($this->written_work         * $w['written_work']) +
            ($this->performance_task     * $w['performance_task']) +
            ($this->quarterly_assessment * $w['quarterly_assessment']),
            2
        );
    }

    /**
     * Return the DepEd descriptor label for the stored final_grade.
     * DepEd rounds grades to the nearest whole number before applying
     * the descriptor table (DepEd Order No. 8 s. 2015).
     */
    public function getDescriptorAttribute(): ?string
    {
        if (is_null($this->final_grade)) {
            return null;
        }
        $rounded = (int) round($this->final_grade);
        foreach (config('academic.descriptors') as $d) {
            if ($rounded >= $d['min'] && $rounded <= $d['max']) {
                return $d['label'];
            }
        }
        return null;
    }

    public function isPassing(): bool
    {
        return !is_null($this->final_grade)
            && $this->final_grade >= config('academic.passing_grade');
    }

    public function isEditable(): bool
    {
        return $this->status !== 'locked';
    }
}
