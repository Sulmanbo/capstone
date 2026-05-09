<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: grades
 *
 * One row = one student's grade for one subject in one quarter.
 *
 * DepEd component weights (config/academic.php):
 *   written_work:          30 %
 *   performance_task:      50 %
 *   quarterly_assessment:  20 %
 *
 * final_grade is computed and stored (denormalized) so reports can
 * be generated without re-calculating.
 *
 * Status workflow: draft → submitted → finalized → locked
 * Once locked, Grade::updating() prevents any further changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                  ->constrained('enrollments')
                  ->cascadeOnDelete();
            $table->foreignId('section_subject_id')
                  ->constrained('section_subjects')
                  ->cascadeOnDelete();
            $table->foreignId('grading_quarter_id')
                  ->constrained('grading_quarters')
                  ->cascadeOnDelete();

            // DepEd components (all nullable — may not be entered yet)
            $table->decimal('written_work', 5, 2)->nullable();
            $table->decimal('performance_task', 5, 2)->nullable();
            $table->decimal('quarterly_assessment', 5, 2)->nullable();

            // Computed and stored
            $table->decimal('final_grade', 5, 2)->nullable();

            $table->enum('status', ['draft', 'submitted', 'finalized', 'locked'])
                  ->default('draft');

            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();

            $table->timestamp('finalized_at')->nullable();
            $table->unsignedBigInteger('finalized_by')->nullable();
            $table->foreign('finalized_by')->references('id')->on('users')->nullOnDelete();

            $table->text('remarks')->nullable();
            $table->timestamps();

            // One grade record per student per subject per quarter
            $table->unique(['enrollment_id', 'section_subject_id', 'grading_quarter_id'], 'grades_enroll_ss_quarter_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
