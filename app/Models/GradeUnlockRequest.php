<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradeUnlockRequest extends Model
{
    protected $table = 'grade_unlock_requests';

    protected $fillable = [
        'section_subject_id',
        'grading_quarter_id',
        'requested_by',
        'reason',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function sectionSubject(): BelongsTo
    {
        return $this->belongsTo(SectionSubject::class, 'section_subject_id');
    }

    public function gradingQuarter(): BelongsTo
    {
        return $this->belongsTo(GradingQuarter::class, 'grading_quarter_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForFaculty($query, int $facultyId)
    {
        return $query->whereHas(
            'sectionSubject',
            fn($q) => $q->where('faculty_id', $facultyId)
        );
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
