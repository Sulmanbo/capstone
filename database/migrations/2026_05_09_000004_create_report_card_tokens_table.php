<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: report_card_tokens
 *
 * Each PDF report card has a unique verification token.
 * The data_hash is a SHA-256 of the grade data at generation time.
 * On the verify endpoint, grades are re-fetched and re-hashed;
 * a mismatch flags the document as potentially tampered.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_card_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();

            $table->unsignedTinyInteger('quarter_number')->nullable();

            $table->string('token', 64)->unique();
            $table->char('data_hash', 64);

            $table->foreignId('generated_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamp('generated_at')->useCurrent();

            $table->index('token');
            $table->index(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_tokens');
    }
};
