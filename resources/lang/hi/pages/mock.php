<?php

return [

    'hint' => 'तेज़ परीक्षण।',

    'summary' => <<<'TEXT'
यह mock provider एक deterministic, credential-free playground है जो पूरे webhook lifecycle को अभ्यास करने देता है: request creation, idempotent state transitions, delivery, verification, retries, और failure handling। क्योंकि यह बिना external dependencies के चलता है, आप local या CI में जल्दी iterate कर सकते हैं, fixtures रिकॉर्ड कर सकते हैं, और architecture decisions (जैसे verification बनाम persistence कहाँ रखना है) बिना real secrets लीक किए दिखा सकते हैं।

इसे common failure modes simulate करने के लिए उपयोग करें: delayed deliveries, duplicate sends, out-of-order events, और transient 5xx responses जो exponential backoff trigger करती हैं। mock अलग-अलग “signature modes” (none / HMAC-SHA256 / RSA-verify stub) भी सपोर्ट करता है ताकि टीम raw-body hashing, constant-time comparison, और timestamp windows को सुरक्षित वातावरण में practice कर सके। इससे आप real gateway integrate करने से पहले idempotency keys और dedup tables validate कर पाते हैं।

Documentation quality के लिए mock को production के करीब रखें: same endpoint shapes, headers, और error codes; फर्क सिर्फ trust root का हो। Valid webhooks को जल्दी acknowledge (2xx) करें और heavy work background jobs में offload करें। Verification पास होने तक mock payload को untrusted मानें—फिर अपने business rules लागू करें। नतीजा: fast feedback loop और एक portable demo जो वही architecture mirror करता है जिसे आप ship करेंगे।
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'Simulator modes: none / HMAC-SHA256 / RSA-verify stub; config से चुनकर verification paths practice करें।',
        'Exact raw request body hash करें; timing-safe function से compare करें; short replay windows enforce करें।',
        'Idempotency के लिए processed event IDs रिकॉर्ड करें; valid webhooks को fast ACK (2xx) करें और heavy work defer/offload करें।',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.succeeded',
        'provider' => 'mock',
        'data'     => [
            'payment_id' => 'pay_mock_001',
            'amount'     => 25000,
            'currency'   => 'IDR',
            'status'     => 'succeeded',
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
            'path'   => '/api/webhooks/mock',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
