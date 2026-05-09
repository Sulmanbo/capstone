<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the foreign key constraint from users.section_id → sections.id.
 *
 * This is a follow-up to 300000 (which added the column) because
 * the sections table did not yet exist at that point.
 *
 * Note: SQLite's ALTER TABLE does not enforce FK constraints at the
 * schema level; the constraint is meaningful on MySQL/PostgreSQL in
 * production and is modelled via Eloquent relationships in both cases.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('section_id')
                  ->references('id')->on('sections')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['section_id']);
        });
    }
};
