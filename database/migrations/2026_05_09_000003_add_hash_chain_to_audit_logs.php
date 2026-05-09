<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Adds tamper-evident hash chain columns to audit_logs.
 *
 * prev_hash — SHA-256 of the immediately preceding row's row_hash.
 *             NULL only for the very first row in the table.
 * row_hash  — SHA-256 of (prev_hash + action_type + user_id +
 *             data_payload + source_ip + created_at_unix).
 *
 * If any row is edited after insertion the recomputed hash will not
 * match the stored row_hash, and all subsequent prev_hash pointers
 * will be broken — detectable by `php artisan audit:verify`.
 *
 * Existing rows are left with NULL hashes; verification treats the
 * first NULL-hashed row as the genesis (chain start).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->char('prev_hash', 64)->nullable()->after('user_agent');
            $table->char('row_hash',  64)->nullable()->after('prev_hash');
            $table->index('row_hash');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['row_hash']);
            $table->dropColumn(['prev_hash', 'row_hash']);
        });
    }
};
