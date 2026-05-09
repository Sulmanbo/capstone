<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: sections
 *
 * A section is one class group (e.g. "Grade 7 – St. Therese") within
 * an academic year. The same section name can be reused across years.
 *
 * adviser_id references the homeroom teacher (faculty user).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_name', 100);
            $table->string('grade_level', 20);
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->unsignedBigInteger('adviser_id')->nullable();
            $table->foreign('adviser_id')
                  ->references('id')->on('users')
                  ->nullOnDelete();
            $table->integer('capacity')->default(40);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            // One section name per grade level per academic year
            $table->unique(['academic_year_id', 'grade_level', 'section_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
