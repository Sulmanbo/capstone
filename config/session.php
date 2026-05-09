<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Session Driver
    |--------------------------------------------------------------------------
    | Using 'database' so sessions are stored in MySQL and can be
    | invalidated server-side on deactivation/logout.
    */
    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime — 30 minutes (spec requirement)
    |--------------------------------------------------------------------------
    */
    'lifetime' => env('SESSION_LIFETIME', 30),

    'expire_on_close' => true,

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    | Encrypt all session data at rest using APP_KEY (AES-256).
    */
    'encrypt' => env('SESSION_ENCRYPT', true),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection & Table
    |--------------------------------------------------------------------------
    */
    'connection' => env('SESSION_CONNECTION'),
    'table'      => env('SESSION_TABLE', 'sessions'),
    'store'      => env('SESSION_STORE'),

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery
    |--------------------------------------------------------------------------
    */
    'lottery' => [2, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie
    |--------------------------------------------------------------------------
    */
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'encrypted'), '_').'_session'
    ),

    'path'      => '/',
    'domain'    => env('SESSION_DOMAIN'),
    'secure'    => env('SESSION_SECURE_COOKIE', false), // set true in production with HTTPS
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,

];
