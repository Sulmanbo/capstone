<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create academic_years table
     * 
     * Constraints enforced:
     * - Only one academic year can be "active" at any time (enforced in Model)
     * - status must be one of: 'active', 'inactive', 'archived'
     */
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            
            $table->string('year_label')->unique();  // e.g., "2025-2026"
            $table->date('start_date');
            $table->date('end_date');
            
            $table->enum('status', ['active', 'inactive', 'archived'])->default('inactive')->index();
            $table->boolean('is_active')->default(false)->index();  // Denormalized for query performance
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
