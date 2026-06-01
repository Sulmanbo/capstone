<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * EnrollmentFee
 *
 * The fee a student of a given grade level must pay for a given academic year
 * before the registrar can enlist them into a section.
 */
class EnrollmentFee extends Model
{
    protected $table = 'enrollment_fees';

    protected $fillable = [
        'academic_year_id',
        'grade_level',
        'amount',
        'currency',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Resolve the fee for a grade level in an academic year.
     */
    public static function resolve(int $academicYearId, string $gradeLevel): ?self
    {
        return static::where('academic_year_id', $academicYearId)
            ->where('grade_level', $gradeLevel)
            ->first();
    }
}
