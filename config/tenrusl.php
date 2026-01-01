<?php

declare(strict_types=1);

$csv = static function (?string $value): array {
    if ($value === null) {
        return [];
    }

    $value = trim($value);
    if ($value === '') {
        return [];
    }

    $parts = array_map('trim', explode(',', $value));
    $parts = array_values(array_filter($parts, static fn ($v) => $v !== ''));

    return $parts;
};

$appEnv = (string) env('APP_ENV', 'production');
$isProduction = strtolower($appEnv) === 'production';

$providersCatalog = [
    'mock',
    'xendit',
    'midtrans',
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
];

// Fail-safe default:
// - production: deny-by-default (allowlist kosong sampai Anda set env TENRUSL_PROVIDERS_ALLOWLIST)
// - non-production: default ke seluruh catalog agar gampang untuk demo/dev
$providersAllowlist = $csv(env('TENRUSL_PROVIDERS_ALLOWLIST'));
if ($providersAllowlist === []) {
    $providersAllowlist = $isProduction ? [] : $providersCatalog;
}

// Demo secrets: jangan pernah punya default "changeme" di production.
$demoSecretDefault = $isProduction ? '' : 'changeme';

return [
    /*
    |--------------------------------------------------------------------------
    | Demo secrets (read from .env)
    |--------------------------------------------------------------------------
    */
    'mock_secret' => env('TENRUSL_MOCK_SECRET', env('MOCK_SECRET', $demoSecretDefault)),
    'xendit_callback_token' => env('TENRUSL_XENDIT_CALLBACK_TOKEN', env('XENDIT_CALLBACK_TOKEN', $demoSecretDefault)),
    'midtrans_server_key' => env('TENRUSL_MIDTRANS_SERVER_KEY', env('MIDTRANS_SERVER_KEY', $demoSecretDefault)),

    /*
    |--------------------------------------------------------------------------
    | Optional provider secrets
    |--------------------------------------------------------------------------
    */
    'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'paypal_env' => env('PAYPAL_ENV', $isProduction ? 'live' : 'sandbox'),
    'paypal_webhook_id' => env('PAYPAL_WEBHOOK_ID'),
    'paypal_client_id' => env('PAYPAL_CLIENT_ID'),
    'paypal_client_secret' => env('PAYPAL_CLIENT_SECRET'),
    'paddle_signing_secret' => env('PADDLE_SIGNING_SECRET'),
    'paddle_public_key' => env('PADDLE_PUBLIC_KEY'),
    'ls_webhook_secret' => env('LS_WEBHOOK_SECRET'),
    'airwallex_webhook_secret' => env('AIRWALLEX_WEBHOOK_SECRET'),
    'tripay_private_key' => env('TRIPAY_PRIVATE_KEY'),
    'doku_client_id' => env('DOKU_CLIENT_ID'),
    'doku_secret_key' => env('DOKU_SECRET_KEY'),
    'doku_request_target' => env('DOKU_REQUEST_TARGET', '/'),
    'dana_public_key' => env('DANA_PUBLIC_KEY'),
    'oy_callback_secret' => env('OY_CALLBACK_SECRET'),
    'oy_ip_whitelist' => env('OY_IP_WHITELIST'),
    'payoneer_shared_secret' => env('PAYONEER_SHARED_SECRET'),
    'payoneer_merchant_id' => env('PAYONEER_MERCHANT_ID'),
    'skrill_merchant_id' => env('SKRILL_MERCHANT_ID'),
    'skrill_email' => env('SKRILL_EMAIL'),
    'skrill_md5_secret' => env('SKRILL_MD5_SECRET'),
    'amzn_bwp_public_key' => env('AMZN_BWP_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Provider allowlist
    |--------------------------------------------------------------------------
    | Set via env:
    | TENRUSL_PROVIDERS_ALLOWLIST=mock,xendit,midtrans
    |
    | Notes:
    | - production default = deny-by-default (kosong) sampai env diset.
    | - non-production default = semua provider di catalog.
    */
    'providers_allowlist' => $providersAllowlist,

    /*
    |--------------------------------------------------------------------------
    | Admin access knobs
    |--------------------------------------------------------------------------
    | Dipakai oleh PaymentsController::adminIndex() (header-based).
    | Fail-closed: kalau key kosong, endpoint admin ditolak.
    */
    'admin_demo_key' => env('TENRUSL_ADMIN_DEMO_KEY', env('ADMIN_DEMO_KEY', '')),
    'admin_key' => env('TENRUSL_ADMIN_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Processing lease / retry knobs
    |--------------------------------------------------------------------------
    */
    // Lease untuk job processing lock (mencegah double-process di worker berbeda).
    'processing_lease_seconds' => (int) env('TENRUSL_PROCESSING_LEASE_SECONDS', $isProduction ? 120 : 60),

    // Minimal lease antar eksekusi retry command (mencegah spam retry loop).
    'retry_min_lease_ms' => (int) env('TENRUSL_RETRY_MIN_LEASE_MS', 250),

    // Retry policy
    'max_retry_attempts' => (int) env('TENRUSL_MAX_RETRY_ATTEMPTS', $isProduction ? 8 : 5),
    'retry_base_ms' => (int) env('TENRUSL_RETRY_BASE_MS', $isProduction ? 1000 : 500),
    'retry_cap_ms' => (int) env('TENRUSL_RETRY_CAP_MS', $isProduction ? 60000 : 30000),

    /*
    |--------------------------------------------------------------------------
    | Scheduler knobs
    |--------------------------------------------------------------------------
    */
    'scheduler_provider' => env('TENRUSL_SCHEDULER_PROVIDER', ''),
    'scheduler_backoff_mode' => env('TENRUSL_SCHEDULER_BACKOFF_MODE', 'full'), // full|equal|decorrelated
    'scheduler_limit' => (int) env('TENRUSL_SCHEDULER_LIMIT', 200),

    // Jika true, scheduler jalan via queue (worker). Jika false, jalan langsung di process scheduler.
    'scheduler_queue' => (bool) env('TENRUSL_SCHEDULER_QUEUE', false),

    /*
    |--------------------------------------------------------------------------
    | Provider metadata (logo path harus relative)
    |--------------------------------------------------------------------------
    */
    'providers_meta' => [
        'airwallex' => ['display_name' => 'Airwallex', 'signature_type' => 'HMAC', 'logo' => 'providers/airwallex.png'],
        'amazon_bwp' => ['display_name' => 'Amazon BWP', 'signature_type' => 'RSA', 'logo' => 'providers/amazon_bwp.png'],
        'dana' => ['display_name' => 'DANA', 'signature_type' => 'RSA', 'logo' => 'providers/dana.png'],
        'doku' => ['display_name' => 'DOKU', 'signature_type' => 'HMAC', 'logo' => 'providers/doku.png'],
        'lemonsqueezy' => ['display_name' => 'Lemon Squeezy', 'signature_type' => 'HMAC', 'logo' => 'providers/lemonsqueezy.png'],
        'midtrans' => ['display_name' => 'Midtrans', 'signature_type' => 'SHA512', 'logo' => 'providers/midtrans.png'],
        'mock' => ['display_name' => 'Mock', 'signature_type' => 'SIM', 'logo' => 'providers/mock.png'],
        'oy' => ['display_name' => 'OY! Indonesia', 'signature_type' => 'HMAC', 'logo' => 'providers/oy.png'],
        'paddle' => ['display_name' => 'Paddle', 'signature_type' => 'HMAC', 'logo' => 'providers/paddle.png'],
        'payoneer' => ['display_name' => 'Payoneer', 'signature_type' => 'Token', 'logo' => 'providers/payoneer.png'],
        'paypal' => ['display_name' => 'PayPal', 'signature_type' => 'API', 'logo' => 'providers/paypal.png'],
        'skrill' => ['display_name' => 'Skrill', 'signature_type' => 'MD5', 'logo' => 'providers/skrill.png'],
        'stripe' => ['display_name' => 'Stripe', 'signature_type' => 'HMAC', 'logo' => 'providers/stripe.png'],
        'tripay' => ['display_name' => 'TriPay', 'signature_type' => 'HMAC', 'logo' => 'providers/tripay.png'],
        'xendit' => ['display_name' => 'Xendit', 'signature_type' => 'Token', 'logo' => 'providers/xendit.png'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Idempotency (dipakai oleh IdempotencyKeyService)
    |--------------------------------------------------------------------------
    */
    // TTL cache untuk menyimpan response idempotent.
    'idempotency_ttl' => (int) env('TENRUSL_IDEMPOTENCY_TTL', 3600),

    'idempotency' => [
        'ttl_seconds' => (int) env(
            'TENRUSL_IDEMPOTENCY_TTL_SECONDS',
            env('IDEMPOTENCY_TTL_SECONDS', 7200)
        ),
        'lock_seconds' => (int) env('TENRUSL_IDEMPOTENCY_LOCK_SECONDS', env('IDEMPOTENCY_LOCK_SECONDS', 30)),
    ],

    /*
    |--------------------------------------------------------------------------
    | Signature global config
    |--------------------------------------------------------------------------
    */
    'signature' => [
        'timestamp_leeway_seconds' => (int) env(
            'TENRUSL_SIG_TS_LEEWAY_SECONDS',
            (int) env('TENRUSL_SIG_TS_LEEWAY', env('SIG_TS_LEEWAY', 300))
        ),
    ],
];
