<?php

return [

    'hint' => 'Timestamped signature header.',

    'summary' => <<<'TEXT'
Stripe signs every webhook request and exposes the computed signature in the `Stripe-Signature` header. Your endpoint must verify the request before doing any work. With Stripe’s official libraries, pass three inputs into the verification routine: the exact raw request body, the `Stripe-Signature` header, and your endpoint secret. Only continue when verification succeeds; otherwise return a non-2xx and stop processing. When you can’t use an official library, implement manual verification as documented, including timestamp tolerance checks to reduce replay risk.

Treat signature verification as a strict gate. Keep the handler idempotent (store event IDs), respond with 2xx quickly after persistence, and push heavy work to background jobs. Make sure your framework provides the **raw bytes**—avoid re-serializing JSON before hashing, because any change in whitespace or ordering will break signature checks. Finally, log minimal diagnostics (verification outcome, event type, a body hash—not secrets) and monitor failures during secret rotation or endpoint changes.
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        'Read the `Stripe-Signature` header; obtain the endpoint secret from your Stripe dashboard.',
        'Verify with official libraries by passing: raw request body, `Stripe-Signature`, and endpoint secret.',
        'If verifying manually, enforce a timestamp tolerance to reduce replay, and compare signatures using a timing-safe function.',
        'Accept only on success; store event IDs for idempotency and return 2xx quickly after persistence.',
    ],

    'example_payload' => [
        'id'   => 'evt_' . now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id'     => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider'   => 'stripe',
        'created_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/stripe',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
