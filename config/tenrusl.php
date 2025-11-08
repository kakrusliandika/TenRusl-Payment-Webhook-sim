<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TenRusl Demo Secrets & Flags
    |--------------------------------------------------------------------------
    |
    | Nilai diambil dari environment (.env). Jangan menaruh kredensial asli di
    | file ini. Untuk demo:
    | - MOCK_SECRET: dipakai HMAC-SHA256 pada provider "mock"
    | - XENDIT_CALLBACK_TOKEN: token header x-callback-token (provider "xendit")
    | - MIDTRANS_SERVER_KEY: server key untuk perhitungan signature-key (provider "midtrans")
    |
    */

    'mock_secret'           => env('MOCK_SECRET', 'changeme'),
    'xendit_callback_token' => env('XENDIT_CALLBACK_TOKEN', 'changeme'),
    'midtrans_server_key'   => env('MIDTRANS_SERVER_KEY', 'changeme'),

    /*
    |--------------------------------------------------------------------------
    | Opsi Lain (opsional)
    |--------------------------------------------------------------------------
    | Kamu bisa menambahkan flag/konfigurasi lain di sini jika diperlukan.
    | Misal: maksimal percobaan retry, atau window idempotensi.
    */

    // 'max_retry_attempts' => env('TENRUSL_MAX_RETRY_ATTEMPTS', 5),
    // 'idempotency_ttl'    => env('TENRUSL_IDEMPOTENCY_TTL', 3600),
];
