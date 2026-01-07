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


$normalizeTrustedProxies = static function (mixed $value) use ($csv): array|string|null {
    if (! is_string($value)) {
        return null;
    }

    $value = trim($value);

    if ($value === '') {
        return null;
    }

    if ($value === '*') {
        return '*';
    }

    $parts = $csv($value);

    return $parts !== [] ? $parts : null;
};

$trustedProxies = $normalizeTrustedProxies(env('TRUSTED_PROXIES'));

$webhookRateLimitProviderOverrides = (static function (): array {
    $raw = env('TENRUSL_WEBHOOK_RATE_LIMIT_PROVIDERS_JSON', '');

    if (! is_string($raw)) {
        return [];
    }

    $raw = trim($raw);
    if ($raw === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (! is_array($decoded)) {
        return [];
    }

    $out = [];

    foreach ($decoded as $provider => $limits) {
        if (! is_string($provider)) {
            continue;
        }

        $provider = strtolower(trim($provider));
        if ($provider === '') {
            continue;
        }

        if (! is_array($limits)) {
            continue;
        }

        $entry = [];

        if (array_key_exists('per_second', $limits)) {
            $entry['per_second'] = (int) $limits['per_second'];
        }
        if (array_key_exists('per_minute', $limits)) {
            $entry['per_minute'] = (int) $limits['per_minute'];
        }

        if ($entry !== []) {
            $out[$provider] = $entry;
        }
    }

    return $out;
})();

return [
    /*
    |--------------------------------------------------------------------------
    | Runtime platform knobs (proxy, queue, etc.)
    |--------------------------------------------------------------------------
    */
    'trusted_proxies' => $trustedProxies,

    // Queue name untuk memproses webhook secara async (dipakai WebhooksController).
    'webhook_queue' => env('TENRUSL_WEBHOOK_QUEUE', 'default'),

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
    'stripe_webhook_secret' => env('TENRUSL_STRIPE_WEBHOOK_SECRET', env('STRIPE_WEBHOOK_SECRET')),
    'paypal_env' => env('TENRUSL_PAYPAL_ENV', env('PAYPAL_ENV', $isProduction ? 'live' : 'sandbox')),
    'paypal_webhook_id' => env('TENRUSL_PAYPAL_WEBHOOK_ID', env('PAYPAL_WEBHOOK_ID')),
    'paypal_client_id' => env('TENRUSL_PAYPAL_CLIENT_ID', env('PAYPAL_CLIENT_ID')),
    'paypal_client_secret' => env('TENRUSL_PAYPAL_CLIENT_SECRET', env('PAYPAL_CLIENT_SECRET')),
    'paddle_signing_secret' => env('TENRUSL_PADDLE_SIGNING_SECRET', env('PADDLE_SIGNING_SECRET')),
    'paddle_public_key' => env('TENRUSL_PADDLE_PUBLIC_KEY', env('PADDLE_PUBLIC_KEY')),
    'ls_webhook_secret' => env('TENRUSL_LS_WEBHOOK_SECRET', env('LS_WEBHOOK_SECRET')),
    'airwallex_webhook_secret' => env('TENRUSL_AIRWALLEX_WEBHOOK_SECRET', env('AIRWALLEX_WEBHOOK_SECRET')),
    'tripay_private_key' => env('TENRUSL_TRIPAY_PRIVATE_KEY', env('TRIPAY_PRIVATE_KEY')),
    'doku_client_id' => env('TENRUSL_DOKU_CLIENT_ID', env('DOKU_CLIENT_ID')),
    'doku_secret_key' => env('TENRUSL_DOKU_SECRET_KEY', env('DOKU_SECRET_KEY')),
    'doku_request_target' => env('TENRUSL_DOKU_REQUEST_TARGET', env('DOKU_REQUEST_TARGET', '/')),
    'dana_public_key' => env('TENRUSL_DANA_PUBLIC_KEY', env('DANA_PUBLIC_KEY')),
    'oy_callback_secret' => env('TENRUSL_OY_CALLBACK_SECRET', env('OY_CALLBACK_SECRET')),
    'oy_ip_whitelist' => env('TENRUSL_OY_IP_WHITELIST', env('OY_IP_WHITELIST')),
    'payoneer_shared_secret' => env('TENRUSL_PAYONEER_SHARED_SECRET', env('PAYONEER_SHARED_SECRET')),
    'payoneer_merchant_id' => env('TENRUSL_PAYONEER_MERCHANT_ID', env('PAYONEER_MERCHANT_ID')),
    'skrill_merchant_id' => env('TENRUSL_SKRILL_MERCHANT_ID', env('SKRILL_MERCHANT_ID')),
    'skrill_email' => env('TENRUSL_SKRILL_EMAIL', env('SKRILL_EMAIL')),
    'skrill_md5_secret' => env('TENRUSL_SKRILL_MD5_SECRET', env('SKRILL_MD5_SECRET')),
    'amzn_bwp_public_key' => env('TENRUSL_AMZN_BWP_PUBLIC_KEY', env('AMZN_BWP_PUBLIC_KEY')),

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
    // Header name bisa diubah lewat env (default: X-Admin-Key)
    'admin_header' => env('TENRUSL_ADMIN_HEADER', env('ADMIN_HEADER', 'X-Admin-Key')),

    // Backward-compatible keys (tetap dipertahankan)
    'admin_demo_key' => env('TENRUSL_ADMIN_DEMO_KEY', env('ADMIN_DEMO_KEY', '')),
    'admin_key' => env('TENRUSL_ADMIN_KEY', ''),

    // Skema yang lebih tegas & jelas (dipakai controller):
    // - production: wajib admin.key (fail-closed), demo_key ditolak default
    // - non-production: admin.key dan/atau admin.demo_key bisa diterima
    'admin' => [
        'header' => env('TENRUSL_ADMIN_HEADER', env('ADMIN_HEADER', 'X-Admin-Key')),
        'key' => env('TENRUSL_ADMIN_KEY', ''),
        'demo_key' => env('TENRUSL_ADMIN_DEMO_KEY', env('ADMIN_DEMO_KEY', '')),

        // Safety valve (default false): kalau true, demo key boleh dipakai di production.
        // Disarankan tetap false.
        'accept_demo_in_production' => filter_var(
            env('TENRUSL_ADMIN_ACCEPT_DEMO_IN_PROD', false),
            FILTER_VALIDATE_BOOL
        ),
    ],

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
    'scheduler_queue' => filter_var(
        env('TENRUSL_SCHEDULER_QUEUE', false),
        FILTER_VALIDATE_BOOL
    ),

    // Scheduler singleton + lock (dipakai di routes/console.php)
    'scheduler_singleton' => filter_var(
        env('TENRUSL_SCHEDULER_SINGLETON', $isProduction ? 'true' : 'false'),
        FILTER_VALIDATE_BOOL
    ),
    'scheduler_lock_minutes' => (int) env('TENRUSL_SCHEDULER_LOCK_MINUTES', 10),

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

    // Storage hint (future-proof): redis / database / cache, dll (tergantung implementasi service).
    'idempotency_storage' => env('TENRUSL_IDEMPOTENCY_STORAGE', $isProduction ? 'redis' : 'cache'),

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

    /*
    |--------------------------------------------------------------------------
    | Webhook receiver safety knobs
    |--------------------------------------------------------------------------
    | (A) Key config yang dipakai kode tapi belum didefinisikan jelas:
    | - VerifyWebhookSignature middleware mengakses: tenrusl.webhook_max_payload_bytes
    |
    | (B) Knob production yang sebaiknya jadi “config resmi”:
    | - payload max bytes
    | - rate limit tuning (angka limit di RouteServiceProvider sebaiknya baca config ini)
    | - correlation id behavior (header, generate, include response)
    */
    'webhook_max_payload_bytes' => (int) env('TENRUSL_WEBHOOK_MAX_PAYLOAD_BYTES', 5 * 1024 * 1024),

    'rate_limit' => [
        'web' => [
            'per_minute' => (int) env('TENRUSL_RATE_WEB_PER_MINUTE', 240),
        ],

        'api' => [
            'per_second' => (int) env('TENRUSL_RATE_API_PER_SECOND', 5),
            'per_minute' => (int) env('TENRUSL_RATE_API_PER_MINUTE', 120),
        ],

        'webhooks' => [
            // Default limits untuk webhook receiver.
            // - Disediakan supaya gampang ditarik oleh RateLimiter di RouteServiceProvider
            // - Tuning cukup lewat env tanpa redeploy (via config cache saat deploy).
            'per_second' => (int) env('TENRUSL_RATE_WEBHOOKS_PER_SECOND', 10),

            // Backward-compatible: tetap dukung TENRUSL_WEBHOOK_RATE_LIMIT_PER_MINUTE.
            'per_minute' => (int) env(
                'TENRUSL_RATE_WEBHOOKS_PER_MINUTE',
                (int) env('TENRUSL_WEBHOOK_RATE_LIMIT_PER_MINUTE', 600)
            ),

            // Keying strategy:
            // - ip: berdasarkan IP (butuh TrustProxies benar)
            // - provider: berdasarkan provider path param
            // - ip_provider: gabungan
            'key_by' => env('TENRUSL_WEBHOOK_RATE_LIMIT_KEY_BY', 'ip_provider'),

            /**
             * Optional provider overrides (JSON via env).
             * Example:
             * TENRUSL_WEBHOOK_RATE_LIMIT_PROVIDERS_JSON={"stripe":{"per_second":25,"per_minute":1500},"midtrans":{"per_second":10}}
             *
             * @var array<string, array{per_second?: int, per_minute?: int}>
             */
            'providers' => $webhookRateLimitProviderOverrides,
        ],
    ],

    'correlation_id' => [
        // Header yang dipakai untuk tracing. Default: X-Request-ID
        'header' => env('TENRUSL_CORRELATION_ID_HEADER', 'X-Request-ID'),

        // Generate ID kalau tidak ada di request
        'generate_if_missing' => filter_var(
            env('TENRUSL_CORRELATION_ID_GENERATE', true),
            FILTER_VALIDATE_BOOL
        ),

        // Sertakan header ke response (biar client bisa log korelasi)
        'include_in_response' => filter_var(
            env('TENRUSL_CORRELATION_ID_INCLUDE_IN_RESPONSE', true),
            FILTER_VALIDATE_BOOL
        ),
    ],
];
