<?php

return [

    'hint' => 'Public-key signature (Classic) / secret (Billing).',

    'summary' => <<<'TEXT'
Paddle Billing signs every webhook with a `Paddle-Signature` header that includes a Unix timestamp (`ts`) and a signature (`h1`). To verify manually, concatenate the timestamp, a colon, and the exact raw request body to build the signed payload; then hash it with your notification destination’s secret key and compare against `h1` using a constant-time function. Paddle generates a separate secret per notification destination—treat it like a password and keep it out of source control.

Use the official SDKs or your own middleware to verify before any parsing. Because timing and body transforms are common pitfalls, ensure your framework exposes raw bytes (for example, Express `express.raw({ type: 'application/json' })`) and enforce a short tolerance for `ts` to deter replays. After verification, acknowledge quickly (2xx), store the event ID for idempotency, and move heavy work to background jobs. This keeps delivery reliable and prevents duplicate side effects under retries.

When migrating from Paddle Classic, note that verification has moved from public-key signatures to secret-based HMAC for Billing. Update your runbooks and secrets management accordingly, and monitor verification metrics when rolling out changes. Clear logs (without secrets) and deterministic error responses greatly simplify incident handling and partner support.
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        'Read `Paddle-Signature` header; parse `ts` and `h1` values.',
        'Build signed payload = `ts + ":" + <raw request body>`; hash with your endpoint secret key.',
        'Compare your hash with `h1` using a timing-safe function; enforce a short tolerance on `ts` to prevent replay.',
        'Prefer official SDKs or a verification middleware; only parse JSON after verification succeeds.',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
    ],

    'endpoints' => [
        [
            'method' => 'POST',
            'path' => '/api/payments',
            'desc' => __('pages.create_payment'),
        ],
        [
            'method' => 'GET',
            'path' => '/api/payments/{id}',
            'desc' => __('pages.get_payment'),
        ],
        [
            'method' => 'POST',
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
