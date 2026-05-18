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
 * A grade can also be marked as dropped (dropped_at is set) at any stage
 * by faculty to record mid-quarter withdrawals.
 *
 * computeFinalGrade() uses subject-specific weights when configured,
 * otherwise falls back to config/academic.php global weights.
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
        'dropped_at',
        'drop_reason',
        'dropped_by',
    ];

    protected $casts = [
        'written_work'          => 'float',
        'performance_task'      => 'float',
        'quarterly_assessment'  => 'float',
        'final_grade'           => 'float',
        'submitted_at'          => 'datetime',
        'finalized_at'          => 'datetime',
        'dropped_at'            => 'datetime',
    ];

    // ── Boot — immutability guard ──────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::updating(function (Grade $model) {
            // Allow drop/reinstate updates on locked grades but block all other edits
            $onlyDropFields = collect($model->getDirty())->keys()
                ->diff(['dropped_at', 'drop_reason', 'dropped_by'])
                ->isEmpty();

            if ($model->getOriginal('status') === 'locked' && !$onlyDropFields) {
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

    public function droppedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dropped_by');
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

    public function scopeNotDropped($query)
    {
        return $query->whereNull('dropped_at');
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
     * Returns null if dropped, if any component is missing, or on error.
     * Uses subject-specific weights when configured, otherwise the global config.
     */
    public function computeFinalGrade(): ?float
    {
        if ($this->isDropped()) {
            return null;
        }

        if (is_null($this->written_work)
            || is_null($this->performance_task)
            || is_null($this->quarterly_assessment))
        {
            return null;
        }

        $subject = $this->relationLoaded('sectionSubject')
            ? $this->sectionSubject?->subject
            : $this->sectionSubject?->load('subject')?->subject;

        $w = $subject?->getGradeWeights() ?? config('academic.grade_weights');

        return round(
            ($this->written_work         * $w['written_work']) +
            ($this->performance_task     * $w['performance_task']) +
            ($this->quarterly_assessment * $w['quarterly_assessment']),
            2
        );
    }

    /**
     * Return the DepEd descriptor label for the stored final_grade.
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
        return !$this->isDropped()
            && !is_null($this->final_grade)
            && $this->final_grade >= config('academic.passing_grade');
    }

    public function isEditable(): bool
    {
        return $this->status !== 'locked';
    }

    public function isDropped(): bool
    {
        return $this->dropped_at !== null;
    }
}
