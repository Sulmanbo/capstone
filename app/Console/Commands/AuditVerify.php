<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Verifies the tamper-evident hash chain stored in the audit_logs table.
 *
 * For each row that has a row_hash, this command recomputes the expected
 * hash from the stored fields and compares it against the stored value.
 * It also checks that each row's prev_hash matches the previous row's
 * row_hash. Any mismatch indicates a tampered or corrupted record.
 *
 * Usage:
 *   php artisan audit:verify
 *   php artisan audit:verify --since=2025-01-01
 */
class AuditVerify extends Command
{
    protected $signature = 'audit:verify
                            {--since= : Only verify rows created on or after this date (Y-m-d)}
                            {--limit=10000 : Maximum rows to verify}';

    protected $description = 'Verify the tamper-evident hash chain in the audit_logs table';

    public function handle(): int
    {
        $since = $this->option('since');
        $limit = (int) $this->option('limit');

        $query = DB::table('audit_logs')
            ->whereNotNull('row_hash')
            ->orderBy('id')
            ->limit($limit)
            ->select(['id', 'user_id', 'action_type', 'data_payload',
                       'source_ip', 'created_at', 'prev_hash', 'row_hash']);

        if ($since) {
            $query->where('created_at', '>=', $since);
        }

        $rows = $query->get();

        if ($rows->isEmpty()) {
            $this->info('No hashed audit log rows found to verify.');
            return self::SUCCESS;
        }

        $this->info("Verifying {$rows->count()} audit log row(s)...");

        $broken    = 0;
        $prevHash  = null;

        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        foreach ($rows as $row) {
            $expected = AuditLog::computeHash(
                prevHash:   $row->prev_hash,
                actionType: $row->action_type,
                userId:     $row->user_id,
                payload:    $row->data_payload,
                sourceIp:   $row->source_ip,
                createdAt:  (int) strtotime($row->created_at),
            );

            $hashMismatch = !hash_equals($expected, $row->row_hash);
            $chainBroken  = $prevHash !== null && $row->prev_hash !== $prevHash;

            if ($hashMismatch || $chainBroken) {
                $broken++;
                $bar->clear();
                $this->error(sprintf(
                    'Row %d [%s] — %s',
                    $row->id,
                    $row->action_type,
                    $hashMismatch ? 'ROW HASH MISMATCH (row was tampered)' : 'CHAIN BREAK (prev_hash mismatch)'
                ));
                $bar->display();
            }

            $prevHash = $row->row_hash;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        if ($broken === 0) {
            $this->info("✓ All {$rows->count()} row(s) verified. Hash chain is intact.");
            return self::SUCCESS;
        }

        $this->error("{$broken} integrity violation(s) detected. Audit log may have been tampered with.");
        return self::FAILURE;
    }
}
