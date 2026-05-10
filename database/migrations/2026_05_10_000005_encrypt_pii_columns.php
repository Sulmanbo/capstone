<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Widen PII columns to TEXT so they can hold AES-256 / Crypt::encryptString()
 * output (~180-300 chars). The date_of_birth column is also changed from DATE
 * to TEXT because the encrypted value is stored as a ciphertext string.
 *
 * Fields left as-is (plain text, needed for LIKE search):
 *   applicants: first_name, middle_name, last_name, lrn, reference_number
 *   users: first_name, last_name, username, lrn, employee_number
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── applicants ─────────────────────────────────────────────────────
        Schema::table('applicants', function (Blueprint $table) {
            $table->text('date_of_birth')->change();                     // was date
            $table->text('parent_guardian_name')->change();              // was string
            $table->text('parent_contact')->change();                    // was string
            $table->text('parent_email')->nullable()->change();          // was string nullable
            $table->text('barangay')->nullable()->change();              // was string nullable
            $table->text('municipality')->nullable()->change();          // was string nullable
            $table->text('province')->nullable()->change();              // was string nullable
            // address is already TEXT — no change required
        });

        // ── users ──────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();                  // was string(20)
            $table->text('address')->nullable()->change();               // was string(255)
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->date('date_of_birth')->change();
            $table->string('parent_guardian_name')->change();
            $table->string('parent_contact')->change();
            $table->string('parent_email')->nullable()->change();
            $table->string('barangay')->nullable()->change();
            $table->string('municipality')->nullable()->change();
            $table->string('province')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->change();
            $table->string('address', 255)->nullable()->change();
        });
    }
};
