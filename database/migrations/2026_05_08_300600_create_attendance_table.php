<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: attendance
 *
 * One row = one student's attendance for one subject on one date.
 * The unique constraint prevents duplicate records for the same
 * student × subject × date combination.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                  ->constrained('enrollments')
                  ->cascadeOnDelete();
            $table->foreignId('section_subject_id')
                  ->constrained('section_subjects')
                  ->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])
                  ->default('present');
            $table->string('remarks', 255)->nullable();
            $table->unsignedBigInteger('recorded_by');
            $table->foreign('recorded_by')->references('id')->on('users');
            $table->timestamps();

            $table->unique(['enrollment_id', 'section_subject_id', 'date'], 'attendance_enroll_ss_date_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
