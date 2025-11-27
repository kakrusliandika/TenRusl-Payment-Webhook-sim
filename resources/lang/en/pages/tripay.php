<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay delivers callbacks to the URL you configure and includes headers that identify the event and help you authenticate the sender. In particular, callbacks carry `X-Callback-Event` with a value such as `payment_status`, and `X-Callback-Signature` for signature validation as specified by TriPay’s documentation. Your consumer should read these headers, verify the request authenticity accordingly, and only then update internal state.

Design the endpoint to be fast and idempotent. Use a short freshness window if timestamps/nonces are present, and maintain a lightweight deduplication store keyed by reference or event identifiers. Return a 2xx quickly once the event is recorded, then handle side effects asynchronously. For transparency and incident handling, keep an audit trail that records receipt time, event metadata, and verification outcomes without logging secrets.
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        'Inspect `X-Callback-Event` (e.g., `payment_status`) and `X-Callback-Signature`.',
        'Validate the signature as documented by TriPay; reject on mismatch or missing header.',
        'Keep processing idempotent (deduplicate by reference/event ID) and acknowledge quickly (2xx).',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status'    => 'PAID',
        'amount'    => 125000,
        'currency'  => 'IDR',
        'provider'  => 'tripay',
        'sent_at'   => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/tripay',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
