<?php

return [

    'hint' => 'प्रोडक्ट-विशिष्ट नोटिफिकेशन।',

    'summary' => <<<'TEXT'
Payoneer Checkout असिंक्रोनस नोटिफिकेशन (webhooks) आपके नियंत्रित endpoint पर डिलीवर करता है, ताकि आपका सिस्टम यूज़र के ब्राउज़र के बाहर सुरक्षित रूप से पेमेंट स्टेट को reconcile कर सके। प्लेटफ़ॉर्म आपको एक dedicated notification URL परिभाषित करने और अपने stack के अनुसार delivery style चुनने देता है—POST (अनुशंसित) या GET, JSON या form-encoded parameters के साथ। क्योंकि exact parameter set और signing/auth patterns प्रोडक्ट-विशिष्ट होते हैं, Payoneer notifications को एक integration surface की तरह ट्रीट करें: event पहचानने वाले headers/fields को डॉक्यूमेंट करें, जहाँ उपलब्ध हो वहाँ anti-replay metadata शामिल करें, और state बदलने से पहले authenticity verify करें।

Operational रूप से, एक संकीर्ण (narrow) idempotent handler अलग करें जो एक immutable event record persist करे और जल्दी 2xx लौटाए। retry storms से बचने के लिए heavy business logic को background workers में रखें। deduplication keys लागू करें और किसी भी timestamp/nonce पर freshness window enforce करें ताकि replays या out-of-order delivery से सुरक्षा रहे। अतिरिक्त assurance चाहिए तो notification URL में provider-issued token (या आपका खुद का random secret) जोड़ें और server-side validate करें। अंत में, टीम के लिए एक runbook प्रकाशित करें जिसमें endpoints, formats, failure codes, और आपकी Payoneer product variant के लिए लागू किए गए exact verification steps दस्तावेज़ित हों—और उसे code के साथ versioned रखें।
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        'एक dedicated notification endpoint expose करें (POST अनुशंसित); JSON या form data स्वीकार करें।',
        'अपनी product variant के डॉक्यूमेंटेशन के अनुसार authenticity validate करें (token या signature fields); mismatch पर reject करें।',
        'जहाँ उपलब्ध हो वहाँ timestamp/nonce freshness enforce करें और processing को idempotent बनाएं (dedup key store करें)।',
        'तेज़ ACK (2xx) दें और heavy काम background jobs को offload करें; secrets लॉग किए बिना audit trail रखें।',
    ],

    'example_payload' => [
        'event'     => 'checkout.transaction.completed',
        'provider'  => 'payoneer',
        'data'      => [
            'orderId'  => 'PO-001',
            'amount'   => 25000,
            'currency' => 'IDR',
            'status'   => 'COMPLETED',
        ],
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
            'path'   => '/api/webhooks/payoneer',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
