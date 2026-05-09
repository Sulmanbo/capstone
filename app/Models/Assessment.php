<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Assessment Model
 *
 * Faculty-created task (quiz, assignment, project, exam) tied to
 * a section_subject. The category maps to a DepEd grade component.
 */
class Assessment extends Model
{
    protected $table = 'assessments';

    protected $fillable = [
        'section_subject_id',
        'title',
        'description',
        'type',
        'category',
        'max_score',
        'due_date',
        'posted_by',
        'status',
    ];

    protected $casts = [
        'max_score' => 'float',
        'due_date'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function sectionSubject(): BelongsTo
    {
        return $this->belongsTo(SectionSubject::class, 'section_subject_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(AssessmentScore::class, 'assessment_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'posted')
                     ->where('due_date', '>', now());
    }

    public function scopeDueSoon($query, int $hours = 24)
    {
        return $query->where('status', 'posted')
                     ->whereBetween('due_date', [now(), now()->addHours($hours)]);
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

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->status === 'posted' && $this->due_date->isPast();
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'written_work'          => 'Written Work',
            'performance_task'      => 'Performance Task',
            'quarterly_assessment'  => 'Quarterly Assessment',
            default                 => ucfirst($this->category),
        };
    }
}
