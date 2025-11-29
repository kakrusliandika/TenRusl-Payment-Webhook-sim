<?php

return [

    'hint' => 'Callback token signature.',

    'summary' => <<<'TEXT'
Xendit signs webhook events using a per-account token exposed in the `x-callback-token` header. Your integration must compare this header against the token you obtained from the Xendit dashboard and reject any request with a missing or mismatched token. Certain webhook products also include a `webhook-id` that you can store to prevent duplicate processing in case of retries.

Operationally, keep verification as the first step, persist an immutable event record, acknowledge with 2xx promptly, and move heavy work to queues. Enforce idempotency using `webhook-id` (or your own key) and apply a tight time window if timestamp metadata is provided. Document the full path (verification, deduplication, retries, and error codes) so teammates and services can integrate consistently across environments.
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        'Compare `x-callback-token` with your unique token from the Xendit dashboard; reject on mismatch.',
        'Use `webhook-id` (when present) to deduplicate; treat verification as a hard gate before parsing JSON.',
        'Respond 2xx quickly and defer heavy work; log minimal diagnostics without exposing secrets.',
    ],

    'example_payload' => [
        'id' => 'evt_xnd_'.now()->timestamp,
        'event' => 'invoice.paid',
        'data' => [
            'id' => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
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
            'path' => '/api/webhooks/xendit',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
