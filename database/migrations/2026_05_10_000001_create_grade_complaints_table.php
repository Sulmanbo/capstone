<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('section_subject_id')->constrained('section_subjects')->cascadeOnDelete();
            $table->foreignId('grading_quarter_id')->nullable()->constrained('grading_quarters')->nullOnDelete();
            $table->foreignId('grade_id')->nullable()->constrained('grades')->nullOnDelete();
            $table->text('reason');
            $table->enum('status', ['pending', 'under_review', 'resolved', 'dismissed'])->default('pending');
            $table->text('response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status'], 'gc_student_status_idx');
            $table->index(['section_subject_id', 'status'], 'gc_ss_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_complaints');
    }
};
