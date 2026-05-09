<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: password_otps
 *
 * Stores 6-digit OTP codes for password recovery.
 * The otp_hash column holds a bcrypt hash of the plain OTP.
 * Each email can only have ONE active OTP at a time (upsert on re-request).
 * Expires after 10 minutes. Max 3 attempts before the OTP is invalidated.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email_hash', 64)->index(); // SHA-256 of email (no plain email stored)
            $table->string('otp_hash');                // bcrypt hash of the 6-digit OTP
            $table->integer('attempts')->default(0);   // wrong-guess counter (max 3)
            $table->timestamp('expires_at');           // OTP expiry (10 min from creation)
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_otps');
    }
};
