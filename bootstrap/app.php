<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\InjectionDefenseMiddleware;
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Global middleware (runs on every request) ──────────────────────
        $middleware->web(append: [
            InjectionDefenseMiddleware::class,
            SecurityHeaders::class,
        ]);

        // ── Route-level aliases ────────────────────────────────────────────
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ── Custom 403 page ────────────────────────────────────────────────
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403) {
                return response()->view('errors.403', [
                    'message' => $e->getMessage() ?: 'You do not have permission to access this resource.',
                ], 403);
            }
        });

    })->create();
