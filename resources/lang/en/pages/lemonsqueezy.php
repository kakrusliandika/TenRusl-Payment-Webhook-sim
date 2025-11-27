<?php

return [

    'hint' => 'HMAC signature header.',

    'summary' => <<<'TEXT'
Lemon Squeezy signs every webhook with a straightforward HMAC over the **raw request body**. The sender uses your webhook “signing secret” to produce a SHA-256 HMAC **hex digest**; that digest is sent in the `X-Signature` header. Your task is to read the body bytes exactly as received (no re-stringify, no whitespace changes), compute the same HMAC with your secret, output it as a **hex** string, and compare to `X-Signature` using a constant-time function. If the values differ—or if the header is missing—reject the request before touching any business logic.

Because framework defaults often parse the body before you can hash it, ensure your route gives you access to the raw bytes (for example, configure “raw body” handling in Node/Express). Treat verification as a gate: only after it passes should you parse JSON and update state. Make your handler idempotent so retries or duplicates don’t double-apply side effects, and capture minimal diagnostics (received header length, verification result, event id) rather than secrets. For local testing, use Lemon Squeezy’s test events and simulate failures to confirm retry/backoff behavior. Document the end-to-end path—verification, deduplication, and asynchronous processing—so teammates can reproduce consistent results across environments.
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        'Read `X-Signature` (HMAC-SHA256 **hex** of the raw body) and get the raw request bytes.',
        'Compute hex HMAC using your signing secret and compare with a timing-safe function.',
        'Reject on mismatch/missing header; only parse JSON after verification succeeds.',
        'Ensure framework provides raw body (no re-serialization); make the handler idempotent and log minimal diagnostics.',
    ],

    'example_payload' => [
        'meta'     => ['event_name' => 'order_created'],
        'data'     => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
        'sent_at'  => now()->toIso8601String(),
    ],

    'endpoints' => [
        [
            'method' => 'POST',
            'path'   => '/api/payments',
            'desc'   => __('pages.create_payment'),
        ],
        [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'desc'   => __('pages.get_payment'),
        ],
        [
            'method' => 'POST',
            'path'   => '/api/webhooks/lemonsqueezy',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
