<?php

declare(strict_types=1);

$origins = env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://localhost:5173,http://localhost:4173');
$origins = is_string($origins) ? $origins : '';
$allowedOrigins = array_values(array_filter(array_map('trim', explode(',', $origins)), static fn ($v) => $v !== ''));

return [
    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    | Batasi hanya untuk API.
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    | Preflight adalah OPTIONS, jadi pastikan methods mencakup OPTIONS.
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    | Isi lewat env agar deploy demo tidak perlu edit source.
    */
    'allowed_origins' => $allowedOrigins,

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    | Agar tidak mentok preflight saat FE menambah header custom (Idempotency-Key,
    | X-Request-ID, X-Admin-Key, Authorization), untuk demo aman pakai ['*'].
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    | Header yang boleh dibaca JS browser (default tidak semua header terbaca).
    | - X-Request-ID dipakai untuk tracing (CorrelationIdMiddleware)
    | - Idempotency-Key dipakai FE jika ingin membaca kembali key dari response
    */
    'exposed_headers' => [
        'X-Request-ID',
        'Idempotency-Key',
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
    ],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    | Cache hasil preflight (OPTIONS) agar dev/demo lebih nyaman.
    */
    'max_age' => (int) env('CORS_MAX_AGE', 600),

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    | Untuk admin demo header-based biasanya TIDAK butuh cookies -> default false.
    | Kalau Anda pakai auth cookie/sanctum, set true lewat env.
    */
    'supports_credentials' => filter_var(
        env('CORS_SUPPORTS_CREDENTIALS', false),
        FILTER_VALIDATE_BOOL
    ),
];
