<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

/**
 * AuditLog Model — Write-only. Never update or delete rows.
 *
 * Use AuditLog::record() as the single entry point for logging
 * throughout the application to ensure consistency.
 */
class AuditLog extends Model
{
    // No updated_at column — logs are immutable
    public const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'actor_name',
        'action_type',
        'data_payload',
        'source_ip',
        'user_agent',
        'prev_hash',
        'row_hash',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ── Action Type Constants ──────────────────────────────────────────────
    public const LOGIN_SUCCESS             = 'LOGIN_SUCCESS';
    public const LOGIN_FAILED              = 'LOGIN_FAILED';
    public const LOGOUT                    = 'LOGOUT';
    public const ACCOUNT_LOCKED            = 'ACCOUNT_LOCKED';
    public const PASSWORD_RESET            = 'PASSWORD_RESET';
    public const PASSWORD_CHANGED          = 'PASSWORD_CHANGED';
    public const CREATE_USER               = 'CREATE_USER';
    public const UPDATE_USER               = 'UPDATE_USER';
    public const DEACTIVATE_USER           = 'DEACTIVATE_USER';
    public const UPDATE_GRADE              = 'UPDATE_GRADE';
    public const LOCK_SECTION              = 'LOCK_SECTION';
    public const DELETE_RECORD             = 'DELETE_RECORD';
    public const PRIVILEGE_VIOLATION       = 'PRIVILEGE_VIOLATION';
    public const EXPORT_REPORT             = 'EXPORT_REPORT';
    public const INJECTION_BLOCKED         = 'INJECTION_BLOCKED';
    public const GRADE_DRAFT_SAVED         = 'GRADE_DRAFT_SAVED';
    public const GRADE_SUBMITTED           = 'GRADE_SUBMITTED';
    public const GRADE_FINALIZED           = 'GRADE_FINALIZED';
    public const GRADE_LOCKED              = 'GRADE_LOCKED';
    public const GRADE_UNLOCKED            = 'GRADE_UNLOCKED';
    public const GRADE_UNLOCK_REQUESTED    = 'GRADE_UNLOCK_REQUESTED';
    public const GRADE_UNLOCK_APPROVED     = 'GRADE_UNLOCK_APPROVED';
    public const GRADE_UNLOCK_DENIED       = 'GRADE_UNLOCK_DENIED';
    public const GRADE_COMPLAINT_SUBMITTED = 'GRADE_COMPLAINT_SUBMITTED';
    public const GRADE_COMPLAINT_RESPONDED = 'GRADE_COMPLAINT_RESPONDED';
    public const GRADE_COMPLAINT_DISMISSED = 'GRADE_COMPLAINT_DISMISSED';
    public const APPLICANT_STATUS_UPDATED  = 'APPLICANT_STATUS_UPDATED';
    public const ENTRANCE_TEST_RECORDED    = 'ENTRANCE_TEST_RECORDED';

    // ══════════════════════════════════════════════════════════════════════
    // STATIC HELPER — use this everywhere instead of ::create() directly
    // ══════════════════════════════════════════════════════════════════════

    /**
     * Record an audit event.
     *
     * @param string      $actionType  One of the constants above
     * @param array|null  $payload     ['before' => ..., 'after' => ..., 'target' => ...]
     * @param int|null    $userId      Override the auth user (e.g. for failed logins)
     * @param string|null $actorName   Override actor name
     */
    public static function record(
        string  $actionType,
        ?array  $payload    = null,
        ?int    $userId     = null,
        ?string $actorName  = null
    ): void {
        try {
            $user       = auth()->user();
            $resolvedId = $userId    ?? $user?->id;
            $resolvedName = $actorName ?? $user?->full_name ?? 'System';
            $encodedPayload = $payload ? json_encode($payload) : null;
            $sourceIp   = Request::ip();
            $createdAt  = now();

            // ── Hash chain ─────────────────────────────────────────────────
            // Lock the table momentarily so concurrent requests don't read
            // the same "last row" and produce a fork in the chain.
            \DB::transaction(function () use (
                $resolvedId, $resolvedName, $actionType,
                $encodedPayload, $sourceIp, $createdAt
            ) {
                $prevHash = \DB::table('audit_logs')
                    ->lockForUpdate()
                    ->orderByDesc('id')
                    ->value('row_hash')
                    ?? hash('sha256', 'genesis');

                $rowHash = static::computeHash(
                    prevHash:   $prevHash,
                    actionType: $actionType,
                    userId:     $resolvedId,
                    payload:    $encodedPayload,
                    sourceIp:   $sourceIp,
                    createdAt:  $createdAt->timestamp,
                );

                static::create([
                    'user_id'      => $resolvedId,
                    'actor_name'   => $resolvedName,
                    'action_type'  => $actionType,
                    'data_payload' => $encodedPayload,
                    'source_ip'    => $sourceIp,
                    'user_agent'   => Request::userAgent(),
                    'prev_hash'    => $prevHash,
                    'row_hash'     => $rowHash,
                    'created_at'   => $createdAt,
                ]);
            });
        } catch (\Exception $e) {
            // Never let audit logging crash the main request
            \Log::error('AuditLog::record failed: ' . $e->getMessage());
        }
    }

    /**
     * Compute the deterministic SHA-256 hash for a single audit row.
     * The exact same formula is used by AuditVerify for chain validation.
     */
    public static function computeHash(
        string  $prevHash,
        string  $actionType,
        ?int    $userId,
        ?string $payload,
        ?string $sourceIp,
        int     $createdAt
    ): string {
        $content = implode('::', [
            $prevHash,
            $actionType,
            $userId    ?? 'null',
            $payload   ?? '',
            $sourceIp  ?? '',
            $createdAt,
        ]);
        return hash('sha256', $content);
    }

    // ── Relationships ──────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────────
    public function scopeByAction($query, string $action)
    {
        return $query->where('action_type', $action);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
