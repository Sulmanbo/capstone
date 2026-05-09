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
 * Scans user-supplied input fields for SQL injection and XSS patterns.
 * Exempts Laravel's own internal fields (_token, _method, remember)
 * so that DELETE/PATCH/PUT form spoofing works correctly.
 *
 * SQL patterns are context-aware — they require dangerous combinations
 * (e.g. "DELETE FROM", "DROP TABLE") not just the bare keyword, which
 * would false-positive on legitimate words like "update" in a name field.
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

        // Classic ' OR 1=1 style
        "/'\s*(OR|AND)\s+['\"0-9]/i",
        "/\"\s*(OR|AND)\s+['\"]?\d/i",

        // ── XSS ───────────────────────────────────────────────────────────
        '/<script\b[^>]*>/i',
        '/<\/script>/i',
        '/javascript\s*:/i',
        '/vbscript\s*:/i',
        '/on(load|click|mouseover|error|focus|blur|submit|change|keyup|keydown)\s*=/i',
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
        // Only scan actual user input — exclude Laravel internals
        $inputToScan = $request->except($this->exemptFields);

        if ($this->containsMaliciousInput($inputToScan)) {

            AuditLog::record(
                AuditLog::INJECTION_BLOCKED,
                [
                    'route'  => $request->path(),
                    'method' => $request->method(),
                    'note'   => 'Forbidden pattern detected in request input',
                ],
                auth()->id(),
                auth()->user()?->full_name ?? 'Unauthenticated'
            );

            ThreatEvent::record(
                'injection',
                'high',
                'Injection Attempt Blocked',
                sprintf(
                    'Forbidden input pattern on [%s %s] from IP %s',
                    $request->method(),
                    $request->path(),
                    $request->ip()
                ),
                auth()->id(),
                $request->path()
            );

            abort(403, 'Request blocked: forbidden characters detected.');
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

