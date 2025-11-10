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
    | Provider Tambahan (opsional)
    |--------------------------------------------------------------------------
    | Isi variabel ENV-nya di .env sesuai kebutuhan. Simulator akan membaca
    | konfigurasi ini via middleware / verifier kamu.
    */

    // Stripe — header: Stripe-Signature (HMAC SHA-256 atas "t.payload")
    'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    // PayPal — Verify Webhook Signature (gunakan kredensial API)
    'paypal_env'            => env('PAYPAL_ENV', 'sandbox'), // sandbox|live
    'paypal_webhook_id'     => env('PAYPAL_WEBHOOK_ID'),
    'paypal_client_id'      => env('PAYPAL_CLIENT_ID'),
    'paypal_client_secret'  => env('PAYPAL_CLIENT_SECRET'),

    // Paddle — signing secret (atau public key untuk skema lama)
    'paddle_signing_secret' => env('PADDLE_SIGNING_SECRET'),
    'paddle_public_key'     => env('PADDLE_PUBLIC_KEY'),

    // Lemon Squeezy — header: X-Signature (HMAC SHA-256 raw body)
    'ls_webhook_secret'     => env('LS_WEBHOOK_SECRET'),

    // Airwallex — header: x-timestamp + x-signature (base64 HMAC SHA-256 atas ts+body)
    'airwallex_webhook_secret' => env('AIRWALLEX_WEBHOOK_SECRET'),

    // Tripay — header: X-Callback-Signature (HMAC SHA-256 raw JSON body)
    'tripay_private_key'    => env('TRIPAY_PRIVATE_KEY'),

    // DOKU — header: Signature (HMACSHA256=base64(...))
    'doku_client_id'        => env('DOKU_CLIENT_ID'),
    'doku_secret_key'       => env('DOKU_SECRET_KEY'),
    'doku_request_target'   => env('DOKU_REQUEST_TARGET', '/'),

    // DANA — Asymmetric Signature (RSA public key)
    'dana_public_key'       => env('DANA_PUBLIC_KEY'),

    // OY! Indonesia — callback auth/whitelist (opsional)
    'oy_callback_secret'    => env('OY_CALLBACK_SECRET'),
    'oy_ip_whitelist'       => env('OY_IP_WHITELIST'),

    // Payoneer — shared secret (tergantung produk Checkout/webhook)
    'payoneer_shared_secret'=> env('PAYONEER_SHARED_SECRET'),
    'payoneer_merchant_id'  => env('PAYONEER_MERCHANT_ID'),

    // Skrill — IPN (MD5 signature)
    'skrill_merchant_id'    => env('SKRILL_MERCHANT_ID'),
    'skrill_email'          => env('SKRILL_EMAIL'),
    'skrill_md5_secret'     => env('SKRILL_MD5_SECRET'),

    // Amazon Buy with Prime — header: x-amzn-signature (RSA)
    'amzn_bwp_public_key'   => env('AMZN_BWP_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Allowlist Provider untuk Routing
    |--------------------------------------------------------------------------
    | Dipakai di routes/api.php -> whereIn('provider', [...]).
    | Tambahkan/kurangi sesuai yang kamu dukung di middleware/verifier.
    */
    'providers_allowlist' => [
        // existing
        'mock', 'xendit', 'midtrans',

        // tambahan
        'stripe',
        'paypal',
        'paddle',
        'lemonsqueezy',
        'airwallex',
        'tripay',
        'doku',
        'dana',
        'oy',
        'payoneer',
        'skrill',
        'amazon_bwp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Opsi Lain (opsional)
    |--------------------------------------------------------------------------
    | Kamu bisa menambahkan flag/konfigurasi lain di sini jika diperlukan.
    | Misal: maksimal percobaan retry, atau window idempotensi.
    */
    'max_retry_attempts' => env('TENRUSL_MAX_RETRY_ATTEMPTS', 5),
    'idempotency_ttl'    => env('TENRUSL_IDEMPOTENCY_TTL', 3600),
];
