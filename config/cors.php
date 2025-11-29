<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | Di sini kita batasi CORS hanya untuk endpoint API (dan sanctum jika
    | digunakan). Dengan begitu, route web biasa tidak ikut terbuka.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | Untuk demo / API generic, kita izinkan semua method. Jika mau lebih ketat,
    | bisa diganti ke ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'].
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Origin boleh diatur via env untuk fleksibilitas. Nilai default cukup
    | realistis untuk development (React/Vite di port umum).
    |
    | Contoh .env:
    |   CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
    |
    */

    'allowed_origins' => explode(
        ',',
        env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000,http://localhost:5173,http://localhost:4173')
    ),

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Jika ingin pakai pola (regex), gunakan opsi ini. Untuk demo kita kosongkan.
    |
    */

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Untuk development/demo, izinkan semua header custom dengan ['*'] supaya
    | tidak sering mentok CORS saat menambah header baru (Authorization,
    | Idempotency-Key, X-Request-ID, dll).
    |
    | Di produksi, sebaiknya dipersempit ke daftar eksplisit.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Header yang boleh dibaca JavaScript di browser (Access-Control-Expose-Headers).
    | Di sini kita expose header tracing & rate limiting agar bisa dipakai frontend.
    |
    */

    'exposed_headers' => [
        'X-Request-ID',
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
    ],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | Lama (detik) browser boleh cache hasil preflight (OPTIONS). 600 detik
    | (= 10 menit) cukup nyaman untuk demo & dev.
    |
    */

    'max_age' => 600,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Set true jika ingin mengirim cookie / Authorization header antar-origin.
    | Ini umum untuk SPA+API yang butuh auth berbasis cookie atau bearer token.
    |
    */

    'supports_credentials' => true,
];
