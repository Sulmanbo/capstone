<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create curriculum_mappings table
     * 
     * Maps subjects to grade levels within academic years.
     * Creates the hierarchy that allows automatic generation of student class lists.
     * 
     * Constraints:
     * - One subject per grade level per academic year
     * - Foreign keys to academic_years and subjects
     */
    public function up(): void
    {
        Schema::create('curriculum_mappings', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->onDelete('cascade')
                  ->index();
            
            $table->string('grade_level'); // e.g., "Grade 7", "Grade 8", "Grade 9", etc.
            
            $table->foreignId('subject_id')
                  ->constrained('subjects')
                  ->onDelete('cascade')
                  ->index();
            
            $table->boolean('is_required')->default(true);        // Required or elective
            $table->integer('sequence_order')->default(0);        // Order in curriculum
            
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            
            $table->timestamps();
            
            // Unique constraint: One subject per grade level per academic year
            $table->unique(['academic_year_id', 'grade_level', 'subject_id'], 'curriculum_unique_subject_per_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_mappings');
    }
};
