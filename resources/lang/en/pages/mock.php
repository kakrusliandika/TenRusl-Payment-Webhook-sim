<?php

return [

    'hint' => 'Quick testing.',

    'summary' => <<<'TEXT'
This mock provider is a deterministic, credential-free playground to exercise the entire webhook lifecycle: request creation, idempotent state transitions, delivery, verification, retries, and failure handling. Because it runs without external dependencies, you can iterate locally or in CI, record fixtures, and demonstrate architecture decisions (like where you place verification vs. persistence) without leaking real secrets.

Use it to simulate common failure modes: delayed deliveries, duplicate sends, out-of-order events, and transient 5xx responses that trigger exponential backoff. The mock also supports different “signature modes” (none / HMAC-SHA256 / RSA-verify stub) so teammates can practice raw-body hashing, constant-time comparison, and timestamp windows in a safe environment. That lets you validate your idempotency keys and dedup tables before you integrate a real gateway.

For documentation quality, keep the mock close to production: same endpoint shapes, headers, and error codes; the only difference is the trust root. Acknowledge valid webhooks quickly (2xx) and offload heavy work to background jobs. Treat the mock’s payload as untrusted until verification passes—then apply your business rules. The result is a fast feedback loop and a portable demo that mirrors the architecture you’ll ship.
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'Simulator modes: none / HMAC-SHA256 / RSA-verify stub; choose via config to practice verification paths.',
        'Hash the exact raw request body; compare with a timing-safe function; enforce short replay windows.',
        'Record processed event IDs for idempotency; ACK valid webhooks fast (2xx) and defer heavy work.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.succeeded',
        'provider' => 'mock',
        'data'     => [
            'payment_id' => 'pay_mock_001',
            'amount'     => 25000,
            'currency'   => 'IDR',
            'status'     => 'succeeded',
        ],
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
            'path'   => '/api/webhooks/mock',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
