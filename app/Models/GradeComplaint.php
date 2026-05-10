<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeComplaint extends Model
{
    protected $fillable = [
        'student_id',
        'section_subject_id',
        'grading_quarter_id',
        'grade_id',
        'reason',
        'status',
        'response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // ── Relationships ───────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function sectionSubject(): BelongsTo
    {
        return $this->belongsTo(SectionSubject::class, 'section_subject_id');
    }

    public function gradingQuarter(): BelongsTo
    {
        return $this->belongsTo(GradingQuarter::class, 'grading_quarter_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['pending', 'under_review']);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['pending', 'under_review']);
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'dismissed']);
    }
}
