<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CurriculumMapping Model
 *
 * Defines which subjects are required for specific Grade Levels.
 * Creates a hierarchy that allows the system to automatically know
 * which subjects a student must have based on their grade level.
 *
 * Attributes:
 * - academic_year_id: Foreign Key (AcademicYear)
 * - grade_level: String (e.g., "Grade 7", "Grade 8", "Grade 9", etc.)
 * - subject_id: Foreign Key (Subject)
 * - is_required: Boolean (Default: true — subject is mandatory for this grade level)
 * - sequence_order: Integer (Order of subject in the curriculum)
 * - status: Enum ('active', 'inactive')
 *
 * Logic:
 * - Links subjects to grade levels within a specific academic year
 * - A student's class list is automatically generated based on their grade level
 * - Multiple subjects can be assigned to the same grade level
 * - Subjects can be marked as required or elective
 */
class CurriculumMapping extends Model
{
    protected $table = 'curriculum_mappings';

    protected $fillable = [
        'academic_year_id',
        'grade_level',
        'subject_id',
        'is_required',
        'sequence_order',
        'status',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'sequence_order' => 'integer',
    ];

    // ── Unique Constraints ─────────────────────────────────────────────────
    // One subject per grade level per academic year (enforced via database unique constraint)
    protected $uniqueIdentifier = ['academic_year_id', 'grade_level', 'subject_id'];

    // ── Relationships ──────────────────────────────────────────────────────
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeElective($query)
    {
        return $query->where('is_required', false);
    }

    public function scopeForGradeLevel($query, $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sequence_order', 'asc')->orderBy('created_at', 'asc');
    }

    // ── Helpers ────────────────────────────────────────────────────────────
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRequired(): bool
    {
        return $this->is_required === true;
    }

    /**
     * Get the display name combining grade level and subject
     */
    public function getDisplayName(): string
    {
        $type = $this->is_required ? 'Required' : 'Elective';
        return "{$this->grade_level} - {$this->subject->getDisplayName()} ({$type})";
    }

    /**
     * Get all subjects for a specific grade level in active academic year
     */
    public static function getSubjectsForGradeLevel($gradeLevel, $academicYearId = null)
    {
        $query = self::active()
            ->forGradeLevel($gradeLevel)
            ->ordered()
            ->with('subject');

        if ($academicYearId) {
            $query->forAcademicYear($academicYearId);
        } else {
            // Use current active academic year
            $activeYear = AcademicYear::where('status', 'active')->first();
            if ($activeYear) {
                $query->forAcademicYear($activeYear->id);
            }
        }

        return $query->get();
    }
}
