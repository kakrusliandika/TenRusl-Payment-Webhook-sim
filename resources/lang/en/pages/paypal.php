<?php

return [

    'hint' => 'Verify Webhook Signature API.',

    'summary' => <<<'TEXT'
PayPal requires server-side verification of each webhook via the official Verify Webhook Signature API. Your listener must extract the headers sent with the notification—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL`, and `PAYPAL-TRANSMISSION-SIG`—along with your `webhook_id` and the **raw** request body (`webhook_event`). Post these to the verification endpoint and accept the event only if PayPal returns a success result. This replaces older verification mechanisms and simplifies parity across REST products.

Build the consumer as a fast, idempotent gate: verify first, persist an event record, acknowledge with a 2xx, and push heavy work to a queue. Use a constant-time comparison for any local checks and keep the raw bytes intact when forwarding to PayPal to avoid subtle reserialization bugs. Enforce a tight time tolerance around `PAYPAL-TRANSMISSION-TIME` to reduce replay windows, and log minimal audit data (request ID, verification outcome, body hash—not secrets). With this pattern, duplicate deliveries and partial outages won’t cause double processing, and your audit trail will remain trustworthy during incident response.
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'Collect headers: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; keep the raw body.',
        'Call the Verify Webhook Signature API with those values plus your webhook_id and webhook_event; accept only on success.',
        'Treat verification as a gate; enforce a short time tolerance to mitigate replays and make the consumer idempotent.',
        'Return 2xx promptly, queue heavy work, and log minimal diagnostics (no secrets).',
    ],

    'example_payload' => [
        'id'          => 'WH-' . now()->timestamp,
        'event_type'  => 'PAYMENT.CAPTURE.COMPLETED',
        'resource'    => [
            'id'     => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider'    => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/paypal',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
