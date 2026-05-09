<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: users
 *
 * Stores all platform users — Students (R01), Faculty (R02),
 * Registrar (R03), and Admins (R04).
 *
 * PII fields (first_name, last_name, email) are AES-256 encrypted
 * via Laravel's Crypt facade BEFORE being written to this table.
 *
 * Passwords are bcrypt-hashed (Laravel default, cost factor 12).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // ── Identity ───────────────────────────────────────────────────────────
            // first_name and last_name are plain text for searchability and admin use.
            // email is AES-256 encrypted (RA 10173 sensitive PII).
            $table->string('first_name', 100);           // plain text
            $table->string('last_name', 100);            // plain text
            $table->text('email');                       // AES-256 encrypted
            $table->string('email_hash', 64)->unique();  // SHA-256 of lowercase email for lookups

            // ── Login Credentials ──────────────────────────────────────────
            $table->string('username', 100)->unique();  // auto-generated or LRN/Employee No.
            $table->string('password');                 // bcrypt hash (cost 12)

            // ── Role & Unique Identifiers ──────────────────────────────────
            // role_id: 01=Student, 02=Faculty, 03=Registrar, 04=Admin
            $table->enum('role_id', ['01', '02', '03', '04'])->default('01');
            $table->string('lrn', 12)->nullable()->unique();              // Learner Reference Number (students)
            $table->string('employee_number', 20)->nullable()->unique();  // Faculty / Admin / Registrar

            // ── Security Flags ─────────────────────────────────────────────
            $table->boolean('password_reset_required')->default(true);   // forces reset on first login
            $table->integer('failed_attempts')->default(0);              // brute-force counter
            $table->timestamp('locked_until')->nullable();               // null = not locked

            // ── Account Status ─────────────────────────────────────────────
            $table->enum('status', ['active', 'deactivated', 'locked'])->default('active');

            // ── Timestamps ─────────────────────────────────────────────────
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
