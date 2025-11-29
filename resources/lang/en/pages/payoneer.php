<?php

return [

    'hint' => 'Product-specific notifications.',

    'summary' => <<<'TEXT'
Payoneer Checkout delivers asynchronous notifications (webhooks) to an endpoint you control, so your system can safely reconcile payment state outside the user’s browser. The platform lets you define a dedicated notification URL and choose the delivery style that best fits your stack—POST (recommended) or GET, with JSON or form-encoded parameters. Because the exact parameter set and signing/auth patterns are product-specific, treat Payoneer notifications as an integration surface: document the headers/fields that identify the event, include anti-replay metadata where available, and verify authenticity before mutating state.

Operationally, start by isolating a narrow, idempotent handler that persists an immutable event record and returns 2xx quickly. Keep heavy business logic in background workers to avoid retry storms. Apply deduplication keys and enforce a freshness window on any timestamp/nonce to protect against replays or out-of-order delivery. When you need extra assurance, append a provider-issued token (or your own random secret) in the notification URL and validate it server-side. Finally, publish a runbook for teammates that documents endpoints, formats, failure codes, and the exact verification steps you implement for your Payoneer product variant—and keep it versioned alongside code.
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        'Expose a dedicated notification endpoint (POST recommended); accept JSON or form data.',
        'Validate authenticity as documented for your product variant (token or signature fields); reject on mismatch.',
        'Enforce timestamp/nonce freshness when available and make processing idempotent (store a dedup key).',
        'ACK fast (2xx) and offload heavy work to background jobs; keep an audit trail without logging secrets.',
    ],

    'example_payload' => [
        'event' => 'checkout.transaction.completed',
        'provider' => 'payoneer',
        'data' => [
            'orderId' => 'PO-001',
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
            'path' => '/api/webhooks/payoneer',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
