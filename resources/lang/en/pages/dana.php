<?php

return [

    'hint' => 'RSA (verify with DANA public key).',

    'summary' => <<<'TEXT'
DANA employs an **asymmetric** signature scheme: requests are signed with a private key, and integrators verify them using the official **DANA public key**. In practice, you retrieve the signature from the webhook header (for example, `X-SIGNATURE`), base64-decode it, then verify the raw HTTP request body against that signature using RSA-2048 with SHA-256. Only if verification returns a positive result should the payload be considered authentic. If verification fails—or the signature/header is missing—respond with a non-2xx code and stop processing.

Because webhooks can be retried or delivered out of order, design your handler to be idempotent: persist a unique event identifier and short-circuit duplicates; validate any timestamp/nonce for freshness to mitigate replays; and treat all fields as untrusted until after signature verification. Avoid re-serializing the JSON before verification; hash exactly the bytes that arrived over the wire. Keep secrets and private keys out of logs; if you must log, record only high-level diagnostics (verification result, a hash of the body, event ID) and secure those logs at rest.

For teams, publish a short runbook that covers: how to load or rotate the DANA public key, how to verify in each language/runtime you use, the exact string-to-sign rules for your integration, and what constitutes a permanent versus transient failure. Pair this with a robust retry/backoff policy, bounded work queues, health checks, and alerts on verification failures. The outcome is a webhook consumer that is safe under load, resilient to retries, and compliant with the cryptographic verification that DANA requires by design.
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        'Base64-decode the value from the `X-SIGNATURE` header.',
        'Verify RSA-2048 + SHA-256 over the exact raw HTTP body using the official DANA public key; accept only if verification returns positive.',
        'Reject any webhook with missing/invalid signature or malformed payload; never trust data prior to successful verification.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.paid',
        'provider' => 'dana',
        'data'     => [
            'transaction_id' => 'DANA-001',
            'amount'         => 25000,
            'currency'       => 'IDR',
            'status'         => 'SUCCESS',
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
            'path'   => '/api/webhooks/dana',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
