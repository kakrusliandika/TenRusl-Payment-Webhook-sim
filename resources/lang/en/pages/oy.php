<?php

return [

    'hint' => 'Provider-specific callback signature.',

    'summary' => <<<'TEXT'
OY! callbacks are part of a broader security posture built around registered API keys and source IP allowlisting for partner requests. OY! also offers an Authorization Callback feature to let you control and approve callbacks before they reach your system, adding an explicit gate to prevent unintended state changes. In practice, you should still treat every incoming callback as untrusted until verified, enforce freshness (timestamp/nonce window), and make the consumer idempotent so retries and out-of-order delivery remain safe.

Because public providers differ in how they sign callbacks, our simulator demonstrates a hardened baseline using an HMAC header (for example, `X-Callback-Signature`) computed over the exact raw request body with a shared secret. This illustrates the same principles you’ll use in production: raw-byte hashing (no re-serialization), constant-time comparison, and short replay windows. Pair that with a small dedup store and quick 2xx acknowledgements to keep provider retry logic healthy while avoiding duplicate side effects.

Operationally, maintain an audit trail (receipt time, verification outcome, body hash—not the secret), rotate secrets safely, and monitor verification failure rates. If you rely on allowlists, remember they can change; a cryptographic check (or OY’s explicit authorization gate) should remain the primary trust anchor. Keep the endpoint narrow, predictable, and well-documented so other services and teammates can reuse it confidently.
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'Use OY! security posture: registered API key + source IP allowlisting for partner requests.',
        'Leverage Authorization Callback (dashboard) to approve callbacks before they hit your system.',
        'In this simulator, verify `X-Callback-Signature = HMAC-SHA256(raw_body, secret)` as a best-practice model; apply constant-time compare & freshness checks.',
        'Make processing idempotent and return 2xx promptly to keep provider retries healthy.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.completed',
        'provider' => 'oy',
        'data' => [
            'partner_trx_id' => 'PRT-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'COMPLETED',
        ],
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
            'path' => '/api/webhooks/oy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
