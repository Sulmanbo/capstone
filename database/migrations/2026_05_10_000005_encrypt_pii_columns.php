<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Widen PII columns on the users table to TEXT so they can hold AES-256 /
 * Crypt::encryptString() output (~180-300 chars).
 *
 * Fields left as-is (plain text, needed for LIKE search):
 *   users: first_name, last_name, username, lrn, employee_number
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('phone')->nullable()->change();                  // was string(20)
            $table->text('address')->nullable()->change();               // was string(255)
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->change();
            $table->string('address', 255)->nullable()->change();
        });
    }
};
