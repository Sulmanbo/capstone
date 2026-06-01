<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adviser-driven design:
 *
 * Schedules are the calendar entity: section + subject + classroom + days/times.
 * They are created first; faculty assignment is the LAST step and may be left
 * as TBA (To Be Announced) until a teacher is decided.
 *
 * Kept separate from section_subjects:
 *   - section_subjects represents the academic linkage (this section studies
 *     this subject under this teacher, for grading and class-list purposes).
 *   - schedules represents the calendar placement (when/where the class meets).
 *
 * One section_subject may map to one schedule (1:1 for a single weekly block)
 * but we keep them in separate tables so a "schedule" can exist as TBA without
 * forcing a faculty_id, which section_subjects historically required.
 *
 * The link to section_subjects is set when faculty is assigned.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // Year is the umbrella scope for cascading dropdowns
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();

            // What gets scheduled
            $table->foreignId('section_id')
                  ->constrained('sections')
                  ->cascadeOnDelete();
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->cascadeOnDelete();

            // Where (nullable while room is being decided)
            $table->foreignId('classroom_id')
                  ->nullable()
                  ->constrained('classrooms')
                  ->nullOnDelete();

            // Who (nullable for TBA per adviser feedback)
            $table->foreignId('faculty_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // When
            $table->json('schedule_days');     // ["monday","wednesday","friday"]
            $table->time('start_time');
            $table->time('end_time');

            // Lifecycle
            $table->enum('status', ['tba', 'assigned', 'cancelled'])
                  ->default('tba')
                  ->index();

            // Optional link back to the academic linkage row
            // (set when faculty is assigned and a section_subject is created/found)
            $table->foreignId('section_subject_id')
                  ->nullable()
                  ->constrained('section_subjects')
                  ->nullOnDelete();

            $table->timestamps();

            // Prevent scheduling the SAME subject twice for the same section
            // in the same year (adviser explicitly listed this as a duplicate rule)
            $table->unique(
                ['academic_year_id', 'section_id', 'subject_id'],
                'schedules_unique_subject_per_section'
            );

            // Indexes that the conflict service hits hardest
            $table->index(['academic_year_id', 'faculty_id']);
            $table->index(['academic_year_id', 'classroom_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
