<?php

return [

    'hint' => 'Signature with Client-Id/Request-* headers.',

    'summary' => <<<'TEXT'
DOKU secures HTTP Notifications with a canonical, header-driven signature that you must verify before acting on any payload. Each callback arrives with a `Signature` header whose value takes the form `HMACSHA256=<base64>`. To reconstruct the expected value, you first compute a `Digest` for the request body: base64-encoded SHA-256 of the raw JSON bytes. Next, build a newline-delimited string composed of five components in this exact order and spelling:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (e.g. `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
Then compute an HMAC using SHA-256 with your DOKU Secret Key as the key over that canonical string, base64-encode the result, and prepend `HMACSHA256=`. Finally, compare against the `Signature` header using a constant-time comparison. Any mismatch, missing component, or malformed value must be treated as an authentication failure and the request should be rejected immediately.

For resilience and safety, acknowledge valid notifications quickly (2xx) and push heavy work to background jobs so you don’t trip retries. Make the consumer idempotent by recording processed identifiers (e.g., `Request-Id` or an event ID in the body). Validate freshness: `Request-Timestamp` should sit within a tight window to prevent replay attacks; also make sure `Request-Target` matches your actual route to avoid canonicalization bugs. When parsing, follow DOKU’s guidance to be non-strict: ignore unknown fields and prefer schema evolution over brittle parsers. During incident response, log the presence of required headers, the computed digest/signature (never the secret), and a hash of the body to aid auditing without leaking sensitive data.
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'Read headers: `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature`, and infer `Request-Target` (your route path).',
        'Compute `Digest = base64( SHA256(raw JSON body) )`.',
        'Build canonical string with lines: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (in that order, each on its own line, no trailing newline).',
        'Compute expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`; compare to `Signature` using constant-time.',
        'Enforce timestamp freshness; make processing idempotent; ACK fast (2xx) and offload heavy work.',
    ],

    'example_payload' => [
        'order'       => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider'    => 'doku',
        'sent_at'     => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/doku',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
