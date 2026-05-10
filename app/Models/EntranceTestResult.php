<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntranceTestResult extends Model
{
    // Standard test areas for DepEd-style entrance exams
    public const TEST_AREAS = [
        'reading'   => 'Reading Comprehension',
        'math'      => 'Mathematics',
        'science'   => 'Science',
        'filipino'  => 'Filipino',
        'general'   => 'General Information',
    ];

    protected $fillable = [
        'applicant_id',
        'test_date',
        'administered_by',
        'scores',
        'total_score',
        'max_score',
        'passing_score',
        'passed',
        'notes',
    ];

    protected $casts = [
        'test_date'     => 'date',
        'scores'        => 'array',
        'total_score'   => 'float',
        'max_score'     => 'float',
        'passing_score' => 'float',
        'passed'        => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class, 'applicant_id');
    }

    public function administeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    // ── Computed ─────────────────────────────────────────────────────────────

    public function getPercentageAttribute(): float
    {
        if (!$this->max_score) {
            return 0;
        }
        return round(($this->total_score / $this->max_score) * 100, 2);
    }

    public function getScoreForArea(string $key): ?float
    {
        return $this->scores[$key] ?? null;
    }

    public function getMaxPerArea(): float
    {
        $count = count(self::TEST_AREAS);
        return $count > 0 ? round($this->max_score / $count, 2) : 0;
    }
}
