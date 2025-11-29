<?php

return [

    'hint' => 'signature_key validation.',

    'summary' => <<<'TEXT'
Midtrans includes a computed `signature_key` inside every HTTP(S) notification so you can verify origin before acting. The formula is explicit and stable:
    SHA512(order_id + status_code + gross_amount + ServerKey)
Build the input string using the exact values from the notification body (as strings) and your private `ServerKey`, then compute the SHA-512 hex digest and compare to `signature_key` using a constant-time comparison. If verification fails, discard the notification. For genuine messages, use the documented fields (for example, `transaction_status`) to drive your state machine—acknowledge quickly (2xx), enqueue heavy work, and make updates idempotent in case of retries or out-of-order delivery.

Two common pitfalls: formatting and coercion. Keep `gross_amount` exactly as provided (don’t localize, don’t change decimals) when constructing the string, and avoid trimming or newline changes. Store a per-event or per-order deduplication key to shield against race conditions; log the verification outcome and a body hash for audit without leaking secrets. Pair this with rate limiting on the endpoint and clear failure codes so your monitoring can distinguish temporary errors (eligible for retry) from permanent rejections (invalid signature).
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'Take `order_id`, `status_code`, `gross_amount` from the body (as strings) and append your `ServerKey`.',
        'Compute `SHA512(order_id + status_code + gross_amount + ServerKey)` and compare to `signature_key` (constant-time).',
        'Reject on mismatch; otherwise update state from `transaction_status`. Keep processing idempotent & return 2xx promptly.',
        'Beware of formatting changes to `gross_amount` and stray whitespace when concatenating.',
    ],

    'example_payload' => [
        'order_id' => 'ORDER-001',
        'status_code' => '200',
        'gross_amount' => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key' => '<sha512>',
        'provider' => 'midtrans',
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
            'path' => '/api/webhooks/midtrans',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
