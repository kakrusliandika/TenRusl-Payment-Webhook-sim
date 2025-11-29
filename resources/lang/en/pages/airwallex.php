<?php

return [

    'hint' => 'HMAC-SHA256 over x-timestamp + body.',

    'summary' => <<<'TEXT'
Airwallex webhooks are signed so you can verify both authenticity and integrity before touching your database. Each request includes two critical headers: `x-timestamp` and `x-signature`. To validate a message, read the raw HTTP body exactly as received, concatenate the `x-timestamp` value (as a string) with that raw body to form the digest input, then compute an HMAC using SHA-256 with your notification URL’s shared secret as the key. Airwallex expects the result as a **hex digest**; compare it to the `x-signature` header using a constant-time comparison to avoid timing leaks. If the signatures mismatch, or if the timestamp is missing/invalid, fail closed and return a non-2xx response.

Because replays are a real risk for any webhook system, apply a freshness window to `x-timestamp`. Reject messages that are too old or far in the future, and store processed event IDs to deduplicate downstream side effects (idempotency at your application layer). Treat the payload as untrusted until verification passes; do not re-stringify JSON before hashing—use the exact raw bytes as they arrived to prevent subtle whitespace/ordering mismatches. When verification succeeds, return `2xx` quickly; perform heavy work asynchronously to keep retry logic friendly and reduce accidental duplicates.

For local and CI flows, Airwallex provides first-class tooling: configure your notification URL(s) in the dashboard, preview example payloads, and **send test events** against your endpoint. When debugging, log the received `x-timestamp`, a computed signature preview (never log secrets), and any event identifier if present. If you rotate the secret key, roll it out safely and monitor signature error rates. Finally, document the full chain—verification, deduplication, retries, and error responses—so teammates can reproduce results with the same raw-body hashing rules and time window.
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        'Extract `x-timestamp` and `x-signature` from headers.',
        'Build value_to_digest = <x-timestamp> + <raw HTTP body> (exact bytes).',
        'Compute expected = HMAC-SHA256(value_to_digest, <webhook secret>) as HEX; compare with `x-signature` using constant-time comparison.',
        'Reject if signatures mismatch or timestamp is stale; also deduplicate processed event IDs to ensure idempotency.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'payment_intent_id' => 'pi_awx_001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'succeeded',
        ],
        'provider' => 'airwallex',
        'created_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/airwallex',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
