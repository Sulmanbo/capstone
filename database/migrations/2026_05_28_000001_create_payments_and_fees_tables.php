<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pay-first enrollment policy (client requirement):
 *
 *   1. Each grade level has an enrollment fee (enrollment_fees).
 *   2. The student picks a school account (BDO / BPI / GCash / ...), transfers
 *      manually, uploads a receipt, and submits a Payment record (status: pending).
 *   3. The registrar verifies the proof and flips the Payment to 'paid'.
 *   4. Only then can the registrar enlist the student into a section.
 *
 * Terminology per mentor: putting a student in a section is "enlistment";
 * "enrollment" requires payment first.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Fee schedule, one row per grade level per academic year ─────────
        Schema::create('enrollment_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->string('grade_level', 20);          // "Grade 7", "Grade 8", ...
            $table->decimal('amount', 10, 2);           // e.g. 25000.00
            $table->string('currency', 3)->default('PHP');
            $table->timestamps();

            $table->unique(['academic_year_id', 'grade_level']);
        });

        // ── Payments (bank transfer / e-wallet, manually confirmed) ────────
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->string('grade_level', 20);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PHP');

            // Which configured school account the student paid into
            // (matches the 'id' field in config/payments.php accounts array)
            $table->string('account_id', 30);           // 'bdo' | 'bpi' | 'gcash' | ...
            $table->string('account_label', 50);        // snapshot at time of payment
            $table->string('account_number', 50);       // snapshot at time of payment

            // pending  → student submitted, awaiting registrar verification
            // paid     → registrar confirmed
            // failed   → registrar rejected (with notes)
            // refunded → reversed
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded'])
                  ->default('pending')
                  ->index();

            // Proof of payment
            $table->string('proof_path');               // student's uploaded receipt
            $table->string('reference_number', 100);    // bank/GCash reference typed by student

            // Confirmation metadata
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()
                  ->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['student_id', 'academic_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('enrollment_fees');
    }
};
