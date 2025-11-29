<?php

return [

    'hint' => 'x-amzn-signature on each request.',

    'summary' => <<<'TEXT'
Buy with Prime (BWP) signs every webhook so you can confirm it truly originated from Amazon and wasn’t altered in transit. Each request includes the digital signature in the `x-amzn-signature` header. Your handler must reconstruct the expected signature exactly as documented by BWP for the given event type and environment; if the values don’t match, reject the call. Treat any timestamp/nonce that accompanies the request as part of your anti-replay posture, enforcing a tight validity window and storing processed identifiers to avoid duplicates.

Operationally, design the endpoint to be fast and deterministic: verify first, ack with a `2xx` once safely recorded, and perform the heaviest work asynchronously. If you depend on allowlists, remember that IPs and networks can change—cryptographic verification is the primary trust anchor. Keep a secure audit trail (request ID, signature presence, verification result, and a hash of the body—not the secret). For local testing, stub the verification step behind an environment flag while ensuring production paths always check signatures. When rotating keys or updating canonicalization rules, roll forward cautiously, monitor error rates, and document the precise header set and hashing/canonicalization you implement so other services in your stack remain in lock-step.

From an integration ergonomics perspective, expose **clear failure reasons** (invalid signature, stale timestamp, malformed request) and return stable error codes so retries behave predictably. Combine this with application-level idempotency and replay protection to make downstream payment state transitions safe even under retries, bursts, or partial outages.
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        'Read `x-amzn-signature` from the request headers.',
        'Recreate the expected signature exactly as defined by Buy with Prime (algorithm/canonicalization in official docs); reject on mismatch.',
        'If a timestamp/nonce is provided, enforce a tight freshness window to mitigate replay attacks; store processed IDs to avoid duplicates.',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data' => [
            'orderId' => 'BWP-001',
            'status' => 'COMPLETED',
            'amount' => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
        'sent_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/amazon_bwp',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
