<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table: audit_logs
 *
 * Immutable write-only log repository.
 * No UPDATE or DELETE should ever be run on this table.
 * Records all CRUD operations for non-repudiation (RA 10173).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Actor — who performed the action
            $table->unsignedBigInteger('user_id')->nullable();  // null = system/unauthenticated
            $table->string('actor_name', 200)->nullable();      // denormalized snapshot (encrypted name at time of log)

            // Action
            $table->string('action_type', 60);   // e.g. LOGIN_SUCCESS, UPDATE_GRADE, ACCOUNT_LOCKED
            $table->text('data_payload')->nullable();   // JSON: { before: ..., after: ..., target: ... }

            // Context
            $table->string('source_ip', 45)->nullable();
            $table->string('user_agent', 300)->nullable();

            // Immutable timestamp — no updated_at
            $table->timestamp('created_at')->useCurrent();

            // Indexes for fast filtering in the audit viewer
            $table->index('user_id');
            $table->index('action_type');
            $table->index('created_at');
            $table->index('source_ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
