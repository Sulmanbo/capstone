<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();

            // Personal information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();          // Jr., Sr., III
            $table->date('date_of_birth');
            $table->enum('sex', ['Male', 'Female']);
            $table->string('lrn', 12)->nullable();         // may not have one yet
            $table->string('nationality')->default('Filipino');

            // Address
            $table->text('address');
            $table->string('barangay')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 10)->nullable();

            // Previous school
            $table->string('previous_school')->nullable();
            $table->string('previous_grade_level')->nullable();
            $table->string('school_year_completed')->nullable();  // e.g. "2024-2025"

            // Applying for
            $table->string('applying_for_grade');           // e.g. "Grade 7"
            $table->string('applying_for_year')->nullable(); // e.g. "2025-2026"

            // Parent / guardian
            $table->string('parent_guardian_name');
            $table->string('relationship');                 // Mother, Father, Guardian
            $table->string('parent_contact');
            $table->string('parent_email')->nullable();

            // Submission tracking
            $table->string('reference_number', 20)->unique();
            $table->enum('status', ['pending', 'under_review', 'accepted', 'rejected', 'enrolled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'applying_for_grade'], 'app_status_grade_idx');
            $table->index('lrn');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
