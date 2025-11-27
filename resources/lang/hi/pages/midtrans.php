<?php

return [

    'hint' => 'signature_key सत्यापन।',

    'summary' => <<<'TEXT'
Midtrans हर HTTP(S) notification के अंदर एक computed `signature_key` शामिल करता है ताकि आप कार्रवाई करने से पहले origin verify कर सकें। फ़ॉर्मूला स्पष्ट और स्थिर है:
    SHA512(order_id + status_code + gross_amount + ServerKey)
Notification body से exact values (strings के रूप में) और अपने private `ServerKey` का उपयोग करके input string बनाइए, फिर SHA-512 का hex digest निकालकर `signature_key` से constant-time comparison के जरिए मिलान कीजिए। अगर verification fail हो जाए, तो notification discard कर दीजिए। Genuine messages के लिए documented fields (जैसे `transaction_status`) से अपनी state machine चलाइए—जल्दी ACK (2xx) करें, heavy work enqueue करें, और retries या out-of-order delivery की स्थिति में updates को idempotent रखें।

दो आम pitfalls: formatting और coercion। String बनाते समय `gross_amount` को बिल्कुल वैसे ही रखें जैसा मिला है (localize न करें, decimals न बदलें) और trimming या newline/whitespace बदलाव से बचें। Race conditions से बचने के लिए per-event या per-order deduplication key store करें; audit के लिए verification outcome और body hash log करें, लेकिन secrets leak न करें। Endpoint पर rate limiting और clear failure codes भी रखें ताकि monitoring temporary errors (retry योग्य) और permanent rejections (invalid signature) में फर्क कर सके।
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'Body से `order_id`, `status_code`, `gross_amount` (strings के रूप में) लें और अपना `ServerKey` जोड़ें।',
        '`SHA512(order_id + status_code + gross_amount + ServerKey)` compute करें और `signature_key` से constant-time compare करें।',
        'Mismatch पर reject करें; वरना `transaction_status` से state update करें। Processing idempotent रखें और जल्दी 2xx लौटाएँ।',
        'Concatenate करते समय `gross_amount` की formatting बदलने और stray whitespace से सावधान रहें।',
    ],

    'example_payload' => [
        'order_id'           => 'ORDER-001',
        'status_code'        => '200',
        'gross_amount'       => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key'      => '<sha512>',
        'provider'           => 'midtrans',
        'sent_at'            => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/midtrans',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
