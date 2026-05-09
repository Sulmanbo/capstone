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
    const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'actor_name',
        'action_type',
        'data_payload',
        'source_ip',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ── Action Type Constants ──────────────────────────────────────────────
    const LOGIN_SUCCESS        = 'LOGIN_SUCCESS';
    const LOGIN_FAILED         = 'LOGIN_FAILED';
    const LOGOUT               = 'LOGOUT';
    const ACCOUNT_LOCKED       = 'ACCOUNT_LOCKED';
    const PASSWORD_RESET       = 'PASSWORD_RESET';
    const PASSWORD_CHANGED     = 'PASSWORD_CHANGED';
    const CREATE_USER          = 'CREATE_USER';
    const UPDATE_USER          = 'UPDATE_USER';
    const DEACTIVATE_USER      = 'DEACTIVATE_USER';
    const UPDATE_GRADE         = 'UPDATE_GRADE';
    const LOCK_SECTION         = 'LOCK_SECTION';
    const DELETE_RECORD        = 'DELETE_RECORD';
    const PRIVILEGE_VIOLATION  = 'PRIVILEGE_VIOLATION';
    const EXPORT_REPORT        = 'EXPORT_REPORT';
    const INJECTION_BLOCKED    = 'INJECTION_BLOCKED';

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
            $user = auth()->user();

            static::create([
                'user_id'      => $userId     ?? $user?->id,
                'actor_name'   => $actorName  ?? $user?->full_name ?? 'System',
                'action_type'  => $actionType,
                'data_payload' => $payload ? json_encode($payload) : null,
                'source_ip'    => Request::ip(),
                'user_agent'   => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Never let audit logging crash the main request
            \Log::error('AuditLog::record failed: ' . $e->getMessage());
        }
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
