<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Injection Defense Mode
    |--------------------------------------------------------------------------
    | 'block'   — abort matching requests with 403 (production default)
    | 'monitor' — log only, allow request through (dev/test default)
    */
    'injection_defense_mode' => env('INJECTION_DEFENSE_MODE', 'block'),
];
