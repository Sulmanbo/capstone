<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies security response headers to every web response.
 *
 * CSP uses 'unsafe-inline' for scripts and styles because the views
 * use inline style attributes and inline <script> blocks throughout.
 * A nonce-based CSP would require refactoring every view; this gives
 * the remaining protections (frame-ancestors, form-action, object-src)
 * without breaking the UI.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options',           'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options',    'nosniff');
        $response->headers->set('X-XSS-Protection',          '1; mode=block');
        $response->headers->set('Referrer-Policy',           'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy',        'camera=(), microphone=(), geolocation=(), payment=()');

        // HSTS — only send over HTTPS (XAMPP dev runs plain HTTP)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob:",
            "font-src 'self'",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // Remove server fingerprinting headers if still present
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
