<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: section_subjects
 *
 * Joins a section to a subject, recording which faculty member teaches
 * it, the room, and the weekly schedule. This is the anchor for grades,
 * attendance, and assessments.
 *
 * schedule_days — JSON array of lowercase weekday names,
 *                 e.g. ["monday", "wednesday", "friday"]
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                  ->constrained('sections')
                  ->cascadeOnDelete();
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();
            $table->foreignId('faculty_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->string('room', 50)->nullable();
            $table->json('schedule_days');   // ["monday","wednesday","friday"]
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            // A subject can only be taught once per section per academic year
            $table->unique(['section_id', 'subject_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_subjects');
    }
};
