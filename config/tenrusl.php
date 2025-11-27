<?php

return [
    // Demo secrets (read from .env)
    'mock_secret'             => env('MOCK_SECRET', 'changeme'),
    'xendit_callback_token'   => env('XENDIT_CALLBACK_TOKEN', 'changeme'),
    'midtrans_server_key'     => env('MIDTRANS_SERVER_KEY', 'changeme'),

    // Optional provider secrets
    'stripe_webhook_secret'   => env('STRIPE_WEBHOOK_SECRET'),
    'paypal_env'              => env('PAYPAL_ENV', 'sandbox'),
    'paypal_webhook_id'       => env('PAYPAL_WEBHOOK_ID'),
    'paypal_client_id'        => env('PAYPAL_CLIENT_ID'),
    'paypal_client_secret'    => env('PAYPAL_CLIENT_SECRET'),
    'paddle_signing_secret'   => env('PADDLE_SIGNING_SECRET'),
    'paddle_public_key'       => env('PADDLE_PUBLIC_KEY'),
    'ls_webhook_secret'       => env('LS_WEBHOOK_SECRET'),
    'airwallex_webhook_secret'=> env('AIRWALLEX_WEBHOOK_SECRET'),
    'tripay_private_key'      => env('TRIPAY_PRIVATE_KEY'),
    'doku_client_id'          => env('DOKU_CLIENT_ID'),
    'doku_secret_key'         => env('DOKU_SECRET_KEY'),
    'doku_request_target'     => env('DOKU_REQUEST_TARGET', '/'),
    'dana_public_key'         => env('DANA_PUBLIC_KEY'),
    'oy_callback_secret'      => env('OY_CALLBACK_SECRET'),
    'oy_ip_whitelist'         => env('OY_IP_WHITELIST'),
    'payoneer_shared_secret'  => env('PAYONEER_SHARED_SECRET'),
    'payoneer_merchant_id'    => env('PAYONEER_MERCHANT_ID'),
    'skrill_merchant_id'      => env('SKRILL_MERCHANT_ID'),
    'skrill_email'            => env('SKRILL_EMAIL'),
    'skrill_md5_secret'       => env('SKRILL_MD5_SECRET'),
    'amzn_bwp_public_key'     => env('AMZN_BWP_PUBLIC_KEY'),

    // Allowlist for providers
    'providers_allowlist' => [
        'mock','xendit','midtrans',
        'stripe','paypal','paddle','lemonsqueezy','airwallex','tripay',
        'doku','dana','oy','payoneer','skrill','amazon_bwp',
    ],

    // Retry & idempotency demo knobs
    'max_retry_attempts' => env('TENRUSL_MAX_RETRY_ATTEMPTS', 5),
    'idempotency_ttl'    => env('TENRUSL_IDEMPOTENCY_TTL', 3600),

    // --- IMPORTANT: store plain relative asset paths only (no asset()/url()/route()) ---
    'providers_meta' => [
        'airwallex'    => ['display_name' => 'Airwallex',     'signature_type' => 'HMAC',   'logo' => 'providers/airwallex.png'],
        'amazon_bwp'   => ['display_name' => 'Amazon BWP',    'signature_type' => 'RSA',    'logo' => 'providers/amazon_bwp.png'],
        'dana'         => ['display_name' => 'DANA',          'signature_type' => 'RSA',    'logo' => 'providers/dana.png'],
        'doku'         => ['display_name' => 'DOKU',          'signature_type' => 'HMAC',   'logo' => 'providers/doku.png'],
        'lemonsqueezy' => ['display_name' => 'Lemon Squeezy', 'signature_type' => 'HMAC',   'logo' => 'providers/lemonsqueezy.png'],
        'midtrans'     => ['display_name' => 'Midtrans',      'signature_type' => 'SHA512', 'logo' => 'providers/midtrans.png'],
        'mock'         => ['display_name' => 'Mock',          'signature_type' => 'SIM',    'logo' => 'providers/mock.png'],
        'oy'           => ['display_name' => 'OY! Indonesia', 'signature_type' => 'HMAC',   'logo' => 'providers/oy.png'],
        'paddle'       => ['display_name' => 'Paddle',        'signature_type' => 'HMAC',   'logo' => 'providers/paddle.png'],
        'payoneer'     => ['display_name' => 'Payoneer',      'signature_type' => 'Token',  'logo' => 'providers/payoneer.png'],
        'paypal'       => ['display_name' => 'PayPal',        'signature_type' => 'API',    'logo' => 'providers/paypal.png'],
        'skrill'       => ['display_name' => 'Skrill',        'signature_type' => 'MD5',    'logo' => 'providers/skrill.png'],
        'stripe'       => ['display_name' => 'Stripe',        'signature_type' => 'HMAC',   'logo' => 'providers/stripe.png'],
        'tripay'       => ['display_name' => 'TriPay',        'signature_type' => 'HMAC',   'logo' => 'providers/tripay.png'],
        'xendit'       => ['display_name' => 'Xendit',        'signature_type' => 'Token',  'logo' => 'providers/xendit.png'],
    ],
];
