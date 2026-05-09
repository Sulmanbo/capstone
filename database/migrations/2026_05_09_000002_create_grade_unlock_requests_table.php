<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: grade_unlock_requests
 *
 * Faculty submit an unlock request when their finalized/locked grades need
 * correction. Registrar or admin reviews and approves or denies.
 *
 * Workflow:
 *   faculty → pending → registrar approves (grades → finalized) or denies
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_unlock_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('section_subject_id')
                  ->constrained('section_subjects')
                  ->cascadeOnDelete();

            $table->foreignId('grading_quarter_id')
                  ->constrained('grading_quarters')
                  ->cascadeOnDelete();

            $table->foreignId('requested_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->text('reason');

            $table->enum('status', ['pending', 'approved', 'denied'])->default('pending');

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();

            $table->timestamps();

            $table->index(['section_subject_id', 'grading_quarter_id'], 'gur_ss_quarter_idx');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_unlock_requests');
    }
};
