<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix two RESTRICT foreign keys that prevent user deletion:
 *
 *   attendance.recorded_by  → was RESTRICT (no rule = MySQL default)
 *   assessments.posted_by   → was RESTRICT (constrained() with no onDelete)
 *
 * Both become SET NULL so that deleting a user preserves historical rows
 * but clears the actor reference. Orphaned values (pointing to deleted
 * users) are nulled out before the FK is re-applied.
 *
 * Uses raw SQL throughout to avoid Doctrine (removed in Laravel 11) and
 * to guarantee the correct ALTER → UPDATE → ADD FOREIGN KEY order.
 */
return new class extends Migration
{
    private function fkExists(string $table, string $constraint): bool
    {
        return DB::select("
            SELECT 1
            FROM   INFORMATION_SCHEMA.TABLE_CONSTRAINTS
            WHERE  TABLE_SCHEMA    = DATABASE()
              AND  TABLE_NAME      = ?
              AND  CONSTRAINT_NAME = ?
              AND  CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$table, $constraint]) !== [];
    }

    public function up(): void
    {
        // ── attendance.recorded_by ─────────────────────────────────────────
        // 1. Make nullable (previous partial run may already have done this).
        DB::statement('ALTER TABLE attendance MODIFY COLUMN recorded_by BIGINT UNSIGNED NULL');
        // 2. Null out any orphaned references.
        DB::statement('
            UPDATE attendance
            SET    recorded_by = NULL
            WHERE  recorded_by IS NOT NULL
              AND  recorded_by NOT IN (SELECT id FROM users)
        ');
        // 3. Drop old FK if it still exists.
        if ($this->fkExists('attendance', 'attendance_recorded_by_foreign')) {
            DB::statement('ALTER TABLE attendance DROP FOREIGN KEY attendance_recorded_by_foreign');
        }
        // 4. Add FK with SET NULL on delete.
        DB::statement('
            ALTER TABLE attendance
            ADD CONSTRAINT attendance_recorded_by_foreign
            FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
        ');

        // ── assessments.posted_by ──────────────────────────────────────────
        // 1. Make nullable first so we can write NULLs.
        DB::statement('ALTER TABLE assessments MODIFY COLUMN posted_by BIGINT UNSIGNED NULL');
        // 2. Null out any orphaned references.
        DB::statement('
            UPDATE assessments
            SET    posted_by = NULL
            WHERE  posted_by IS NOT NULL
              AND  posted_by NOT IN (SELECT id FROM users)
        ');
        // 3. Drop old FK if it still exists.
        if ($this->fkExists('assessments', 'assessments_posted_by_foreign')) {
            DB::statement('ALTER TABLE assessments DROP FOREIGN KEY assessments_posted_by_foreign');
        }
        // 4. Add FK with SET NULL on delete.
        DB::statement('
            ALTER TABLE assessments
            ADD CONSTRAINT assessments_posted_by_foreign
            FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
        ');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE attendance DROP FOREIGN KEY attendance_recorded_by_foreign');
        DB::statement('ALTER TABLE attendance MODIFY COLUMN recorded_by BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE attendance ADD CONSTRAINT attendance_recorded_by_foreign FOREIGN KEY (recorded_by) REFERENCES users(id)');

        DB::statement('ALTER TABLE assessments DROP FOREIGN KEY assessments_posted_by_foreign');
        DB::statement('ALTER TABLE assessments MODIFY COLUMN posted_by BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE assessments ADD CONSTRAINT assessments_posted_by_foreign FOREIGN KEY (posted_by) REFERENCES users(id)');
    }
};
