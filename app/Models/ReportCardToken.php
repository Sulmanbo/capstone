<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ReportCardToken extends Model
{
    public $timestamps = false;

    protected $table = 'report_card_tokens';

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'quarter_number',
        'token',
        'data_hash',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'generated_at'   => 'datetime',
        'quarter_number' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public static function generateToken(): string
    {
        return hash('sha256', Str::uuid() . microtime(true) . random_bytes(16));
    }

    /**
     * Compute a deterministic SHA-256 fingerprint of an array of grade rows.
     * Re-run on verify to detect any DB-level tampering after PDF generation.
     */
    public static function hashGradeData(array $gradeRows): string
    {
        ksort($gradeRows);
        return hash('sha256', json_encode($gradeRows, JSON_UNESCAPED_UNICODE));
    }
}
