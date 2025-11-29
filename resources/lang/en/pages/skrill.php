<?php

return [

    'hint' => 'MD5/HMAC style callback signature.',

    'summary' => <<<'TEXT'
Skrill posts a status callback to your `status_url` and expects you to validate the message using `md5sig`, an **uppercase MD5** of a well-defined field concatenation (for example: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`). Only if your computed value matches the incoming `md5sig` should you trust the payload. Skrill also supports an alternative `sha2sig` (uppercase SHA-2) upon request, which is constructed analogously to `md5sig`.

In practice, keep signature validation in your back end (never expose the secret word), and hash the **exact** parameter values as posted back to you. Make the endpoint idempotent (dedup by transaction or event ID), return 2xx quickly after persistence, and defer noncritical work. During debugging, log verification results and a body hash while keeping secrets out of logs. Treat formatting carefully—amount and currency fields must be used verbatim when building the signature string—so comparisons are stable across retries and environments.
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        'Rebuild `md5sig` exactly: concatenate documented fields (e.g., merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) and compute **UPPERCASE MD5**.',
        'Compare with the received `md5sig`; optionally use `sha2sig` (uppercase SHA-2) if enabled by Skrill.',
        'Perform validation on the server only, using the exact posted values; keep the handler idempotent & return 2xx quickly.',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount' => '10.00',
        'mb_currency' => 'EUR',
        'status' => '2',
        'md5sig' => '<UPPERCASE_MD5>',
        'provider' => 'skrill',
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
            'path' => '/api/webhooks/skrill',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
