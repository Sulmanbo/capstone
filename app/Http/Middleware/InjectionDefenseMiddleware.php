<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AuditLog;
use App\Models\ThreatEvent;

/**
 * InjectionDefenseMiddleware
 *
 * SECONDARY defense layer for anomaly detection and logging.
 * Eloquent's parameter binding is the PRIMARY defense against SQL injection;
 * this middleware exists to catch obvious attack patterns early and produce
 * audit/threat trails for security review.
 *
 * Modes (config/security.php → injection_defense_mode):
 *   - block:   abort matching requests with 403 (default in production)
 *   - monitor: log only, allow request through (use in local/dev to avoid
 *              false positives blocking legitimate developer testing)
 */
class InjectionDefenseMiddleware
{
    /**
     * Laravel internal fields — never scan these.
     * _method contains values like DELETE, PUT, PATCH — legitimate spoofing.
     */
    private array $exemptFields = [
        '_token',
        '_method',
        'remember',
        'password',             // never scan passwords
        'password_confirmation',
        'current_password',
    ];

    /**
     * Forbidden patterns — must be contextual to avoid false positives.
     * Each pattern targets dangerous SQL/XSS combinations, not bare keywords.
     */
    private array $forbiddenPatterns = [
        // ── SQL Injection (context-aware) ──────────────────────────────────
        // Require keyword + space + table/clause to avoid matching normal words
        '/\bDELETE\s+FROM\b/i',
        '/\bDROP\s+(TABLE|DATABASE|INDEX|VIEW)\b/i',
        '/\bTRUNCATE\s+TABLE\b/i',
        '/\bINSERT\s+INTO\b/i',
        '/\bSELECT\s+.+\s+FROM\b/i',
        '/\bUNION\s+(ALL\s+)?SELECT\b/i',
        '/\bUPDATE\s+\w+\s+SET\b/i',
        '/\bEXEC(\s|\()/i',
        '/\bEXECUTE\s*\(/i',
        '/\bxp_\w+/i',                          // SQL Server extended procs
        '/\bINFORMATION_SCHEMA\b/i',
        '/\bSYS\.(TABLES|COLUMNS|OBJECTS)\b/i',

        // SQL comment injection (only dangerous when combined with quotes)
        "/'\s*--/",                              // ' --
        "/'\s*\/\*/",                            // ' /*
        '/;\s*(DROP|DELETE|INSERT|UPDATE)\b/i',  // ; DROP ...

        // Classic ' OR 1=1 / ' AND 1=1 style — require digit=digit or quoted-string=quoted-string
        '/[\'"]\s*(OR|AND)\s+\d+\s*=\s*\d+/i',
        '/[\'"]\s*(OR|AND)\s+[\'"][^\'"]+[\'"]\s*=\s*[\'"]/i',

        // ── XSS ───────────────────────────────────────────────────────────
        '/<script\b[^>]*>/i',
        '/<\/script>/i',
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/on(load|click|dblclick|mouseover|mouseout|mousemove|error|focus|blur|focusin|focusout|submit|change|keyup|keydown|keypress|input|dragstart|drop|paste|copy|cut|contextmenu|wheel|scroll|resize|hashchange|beforeunload|unload)\s*=/i',
        '/<iframe\b/i',
        '/<object\b/i',
        '/<embed\b/i',
        '/<svg\b[^>]*on\w+/i',                  // SVG event handlers
        '/data\s*:\s*text\/html/i',              // data: URI XSS

        // ── Path traversal ─────────────────────────────────────────────────
        '/\.\.[\/\\\\]/',                        // ../ or ..\

        // ── Null bytes ─────────────────────────────────────────────────────
        '/\x00/',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $mode = config('security.injection_defense_mode', 'block');

        // Scan user-supplied text fields
        $inputToScan = $request->except($this->exemptFields);
        $detected    = $this->containsMaliciousInput($inputToScan);

        // Also scan uploaded file original names for path traversal / null bytes
        if (!$detected) {
            foreach ($request->allFiles() as $file) {
                $files = is_array($file) ? $file : [$file];
                foreach ($files as $f) {
                    if ($f && $this->containsMaliciousInput($f->getClientOriginalName())) {
                        $detected = true;
                        break 2;
                    }
                }
            }
        }

        $blocked = $detected && $mode === 'block';

        if ($detected) {
            AuditLog::record(AuditLog::INJECTION_BLOCKED, [
                'mode'   => $mode,
                'route'  => $request->path(),
                'method' => $request->method(),
            ]);
            ThreatEvent::record(
                'injection_attempt',
                $mode === 'block' ? 'high' : 'low',
                'Injection Pattern Detected',
                "Suspicious pattern in request to {$request->path()} (mode: {$mode})",
                auth()->id()
            );
        }

        if ($blocked) {
            // Return a real response (not abort) so SecurityHeaders middleware
            // can still apply its headers to this 403 response.
            return response()->view('errors.403', [
                'message' => 'Request blocked: forbidden characters detected.',
            ], 403);
        }

        return $next($request);
    }

    /**
     * Recursively scan input values (handles nested arrays).
     * Only scans string values — ignores numbers, booleans, nulls.
     */
    private function containsMaliciousInput(mixed $input): bool
    {
        if (is_array($input)) {
            foreach ($input as $value) {
                if ($this->containsMaliciousInput($value)) {
                    return true;
                }
            }
            return false;
        }

        if (!is_string($input) || trim($input) === '') {
            return false;
        }

        foreach ($this->forbiddenPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}

