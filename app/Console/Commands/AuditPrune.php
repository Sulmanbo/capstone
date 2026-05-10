<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AuditPrune extends Command
{
    protected $signature = 'audit:prune
        {--days=365   : Delete non-critical audit logs older than this many days}
        {--dry-run    : Show what would be deleted without actually deleting}';

    protected $description = 'Prune old audit log entries, preserving security-critical events indefinitely';

    // These action types are NEVER pruned — compliance / security requirement.
    // They must remain to satisfy RA 10173 data breach accountability obligations.
    private const PERMANENT = [
        AuditLog::LOGIN_FAILED,
        AuditLog::ACCOUNT_LOCKED,
        AuditLog::PRIVILEGE_VIOLATION,
        AuditLog::INJECTION_BLOCKED,
        AuditLog::CREATE_USER,
        AuditLog::UPDATE_USER,
        AuditLog::DEACTIVATE_USER,
        AuditLog::DELETE_RECORD,
        AuditLog::PASSWORD_RESET,
        AuditLog::PASSWORD_CHANGED,
    ];

    public function handle(): int
    {
        $days   = max(1, (int) $this->option('days'));
        $dryRun = (bool) $this->option('dry-run');
        $cutoff = now()->subDays($days);

        $this->line('');
        $this->info("Audit Prune — entries older than <comment>{$days} day(s)</comment> (before <comment>{$cutoff->toDateString()}</comment>)");

        if ($dryRun) {
            $this->warn('  DRY RUN — no rows will be deleted.');
        }

        $pruneable = AuditLog::where('created_at', '<', $cutoff)
            ->whereNotIn('action_type', self::PERMANENT);

        $total = $pruneable->count();

        if ($total === 0) {
            $this->info('  Nothing to prune.');
            $this->line('');
            return self::SUCCESS;
        }

        // Show per-type breakdown
        $breakdown = AuditLog::where('created_at', '<', $cutoff)
            ->whereNotIn('action_type', self::PERMANENT)
            ->selectRaw('action_type, COUNT(*) as cnt')
            ->groupBy('action_type')
            ->orderByDesc('cnt')
            ->get();

        $this->table(
            ['Action Type', 'Rows'],
            $breakdown->map(fn($r) => [$r->action_type, number_format($r->cnt)])->all()
        );

        $this->line("  Total to prune:  <comment>{$total}</comment>");
        $this->line("  Protected types: <comment>" . count(self::PERMANENT) . "</comment> (never deleted)");
        $this->line('');

        if ($dryRun) {
            $this->warn('  Dry-run complete — rerun without --dry-run to apply.');
            $this->line('');
            return self::SUCCESS;
        }

        if (! $this->confirm("  Permanently delete {$total} audit log row(s)?", false)) {
            $this->info('  Aborted.');
            return self::SUCCESS;
        }

        $deleted = $pruneable->delete();

        $this->info("  Pruned {$deleted} row(s).");
        $this->line('');

        Log::info('audit:prune completed', [
            'rows_deleted' => $deleted,
            'days'         => $days,
            'cutoff'       => $cutoff->toDateString(),
            'protected'    => self::PERMANENT,
        ]);

        return self::SUCCESS;
    }
}
