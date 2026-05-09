<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create grading_quarters table
     * 
     * Constraints enforced:
     * - Only one quarter can be "active" per academic year (enforced in Model)
     * - quarter_number should be 1, 2, 3, or 4
     * - Foreign key to academic_years
     */
    public function up(): void
    {
        Schema::create('grading_quarters', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->onDelete('cascade')
                  ->index();
            
            $table->integer('quarter_number');  // 1, 2, 3, 4
            $table->string('quarter_name');     // e.g., "1st Quarter", "First Quarter"
            
            $table->date('start_date');
            $table->date('end_date');
            
            $table->enum('status', ['active', 'inactive', 'archived'])->default('inactive')->index();
            $table->boolean('is_active')->default(false)->index();  // Denormalized for performance
            
            $table->timestamps();
            
            // Unique constraint: Only one entry per academic year and quarter number
            $table->unique(['academic_year_id', 'quarter_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grading_quarters');
    }
};
