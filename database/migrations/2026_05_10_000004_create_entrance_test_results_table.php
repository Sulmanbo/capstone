<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrance_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->unique()->constrained('applicants')->cascadeOnDelete();
            $table->date('test_date');
            $table->foreignId('administered_by')->nullable()->constrained('users')->nullOnDelete();

            // Per-area scores stored as JSON: {"reading": 20, "math": 18, ...}
            $table->json('scores')->nullable();

            $table->decimal('total_score',   6, 2);
            $table->decimal('max_score',     6, 2)->default(100.00);
            $table->decimal('passing_score', 6, 2)->default(75.00);
            $table->boolean('passed');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['passed', 'test_date'], 'etr_passed_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrance_test_results');
    }
};
