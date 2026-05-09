<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AuditLog;
use App\Models\ThreatEvent;

/**
 * RoleMiddleware — Privilege Escalation Monitor
 *
 * Validates the active session role against the requested route.
 * If a lower-privileged user attempts to access a protected route,
 * the request is terminated, a PRIVILEGE_VIOLATION is logged to both
 * the audit trail and threat events, and a 403 is returned.
 *
 * Usage in routes:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,faculty')   // multiple allowed roles
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Map role string aliases to role_id values
        $roleMap = [
            'admin'     => '04',
            'registrar' => '03',
            'faculty'   => '02',
            'student'   => '01',
        ];

        // Resolve allowed role IDs from the middleware parameter(s)
        $allowedIds = array_map(fn($r) => $roleMap[$r] ?? $r, $roles);

        if (!$user || !in_array($user->role_id, $allowedIds)) {

            // ── Log privilege violation ────────────────────────────────────
            AuditLog::record(
                AuditLog::PRIVILEGE_VIOLATION,
                [
                    'attempted_route' => $request->path(),
                    'required_roles'  => $roles,
                    'user_role'       => $user?->role_id ?? 'unauthenticated',
                ],
                $user?->id,
                $user?->full_name ?? 'Unknown'
            );

            ThreatEvent::record(
                'privilege_escalation',
                'high',
                'Privilege Escalation Attempt',
                sprintf(
                    'User (role: %s) attempted to access restricted route [%s] requiring role(s): %s',
                    $user?->role_label ?? 'unauthenticated',
                    $request->path(),
                    implode(', ', $roles)
                ),
                $user?->id,
                $request->path()
            );

            // ── Return 403 ─────────────────────────────────────────────────
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden. Insufficient privileges.'], 403);
            }

            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
