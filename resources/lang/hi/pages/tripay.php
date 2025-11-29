<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay आपके द्वारा कॉन्फ़िगर किए गए URL पर callbacks भेजता है और ऐसे headers शामिल करता है जो event की पहचान करते हैं और sender को authenticate करने में मदद करते हैं। विशेष रूप से, callbacks में `X-Callback-Event` (जैसे `payment_status`) और signature validation के लिए `X-Callback-Signature` (TriPay के दस्तावेज़ के अनुसार) होता है। आपके consumer को इन headers को पढ़ना चाहिए, अनुरोध की authenticity सत्यापित करनी चाहिए, और तभी internal state अपडेट करनी चाहिए।

Endpoint को तेज़ और idempotent बनाएं। यदि timestamps/nonces मौजूद हों तो छोटा freshness window रखें, और reference या event identifiers पर आधारित lightweight deduplication store बनाए रखें। Event रिकॉर्ड होते ही जल्दी 2xx लौटाएँ, फिर side effects को asynchronously हैंडल करें। Transparency और incident handling के लिए audit trail रखें जिसमें receipt time, event metadata, और verification outcomes हों, लेकिन secrets को logs में न लिखें।
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        '`X-Callback-Event` (उदा., `payment_status`) और `X-Callback-Signature` की जाँच करें।',
        'TriPay के दस्तावेज़ के अनुसार signature validate करें; mismatch या header missing होने पर reject करें।',
        'Processing को idempotent रखें (reference/event ID से deduplicate) और जल्दी acknowledgement (2xx) दें।',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status' => 'PAID',
        'amount' => 125000,
        'currency' => 'IDR',
        'provider' => 'tripay',
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
            'path' => '/api/webhooks/tripay',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
