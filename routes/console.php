<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prune non-critical audit logs older than 1 year every Sunday at 02:00.
// Security-critical events (LOGIN_FAILED, ACCOUNT_LOCKED, etc.) are never deleted.
Schedule::command('audit:prune --days=365 --no-interaction')->weekly()->sundays()->at('02:00');
