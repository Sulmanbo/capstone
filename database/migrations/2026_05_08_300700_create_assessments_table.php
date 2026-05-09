<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: assessments
 *
 * Faculty-created tasks (quizzes, assignments, projects, exams).
 * category maps to one of the three DepEd grade components:
 *   written_work | performance_task | quarterly_assessment
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_subject_id')
                  ->constrained('section_subjects')
                  ->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'assignment', 'project', 'exam']);
            $table->enum('category', ['written_work', 'performance_task', 'quarterly_assessment']);
            $table->decimal('max_score', 6, 2)->default(100);
            $table->dateTime('due_date');
            $table->foreignId('posted_by')->constrained('users');
            $table->enum('status', ['draft', 'posted', 'closed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
