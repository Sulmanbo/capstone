<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create subjects table
     * 
     * Master database of all subjects offered by the institution.
     * Constraints enforced:
     * - subject_id is immutable (unique identifier)
     * - subject_code must be unique
     * - Only active subjects can be assigned to curricula
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            
            $table->string('subject_id')->unique();   // Immutable identifier (e.g., "SUBJ-ABC123DEF")
            $table->string('subject_code')->unique(); // Short code (e.g., "MTH101", "ENG101")
            $table->string('subject_name');           // Full name (e.g., "Mathematics", "English")
            
            $table->text('description')->nullable();  // Optional detailed description
            $table->integer('credits')->nullable();   // Credit hours (optional)
            
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
