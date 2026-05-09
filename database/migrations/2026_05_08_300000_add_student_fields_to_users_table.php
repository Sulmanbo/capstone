<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds student-specific fields to the users table.
 *
 * parent_name and parent_contact are stored as encrypted TEXT
 * (AES-256 via Crypt facade) — same pattern as email.
 * No hash column is needed since we never look them up by value.
 *
 * section_id is nullable until Task 2 adds the sections table;
 * the FK constraint is applied in 300300_add_section_fk_to_users_table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('grade_level', 20)->nullable()->after('lrn');

            // Logical FK to sections — constraint added after sections table exists.
            $table->unsignedBigInteger('section_id')->nullable()->after('grade_level');

            $table->date('enrollment_date')->nullable()->after('section_id');

            // AES-256 encrypted (RA 10173 sensitive PII — parent contact details)
            $table->text('parent_name')->nullable()->after('enrollment_date');
            $table->text('parent_contact')->nullable()->after('parent_name');

            $table->enum('lrn_status', ['verified', 'unverified', 'pending'])
                  ->default('unverified')
                  ->after('parent_contact');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'grade_level',
                'section_id',
                'enrollment_date',
                'parent_name',
                'parent_contact',
                'lrn_status',
            ]);
        });
    }
};
