<?php

return [

    'hint' => 'HMAC सिग्नेचर हेडर।',

    'summary' => <<<'TEXT'
Lemon Squeezy हर webhook को **raw request body** पर एक सरल HMAC के साथ साइन करता है। Sender आपके webhook के “signing secret” का उपयोग करके SHA-256 का HMAC **hex digest** बनाता है; वही digest `X-Signature` हेडर में भेजा जाता है। आपका काम है body bytes को बिल्कुल वैसे ही पढ़ना जैसे वे आए हैं (कोई re-stringify नहीं, whitespace बदलाव नहीं), अपने secret से वही HMAC निकालना, उसे **hex** string के रूप में निकालना, और `X-Signature` से constant-time (timing-safe) तुलना करना। अगर वैल्यू अलग हो — या हेडर गायब हो — तो किसी भी business logic को छुए बिना request reject करें।

क्योंकि कई frameworks डिफ़ॉल्ट रूप से body को parse कर देते हैं, सुनिश्चित करें कि आपकी route आपको raw bytes तक पहुँच दे (जैसे Node/Express में “raw body” handling कॉन्फ़िगर करें)। Verification को gate की तरह ट्रीट करें: पास होने के बाद ही JSON parse करें और state अपडेट करें। Handler को idempotent बनाएं ताकि retries/duplicates side effects दोबारा लागू न करें, और secrets की बजाय न्यूनतम diagnostics (received header length, verification result, event id) रिकॉर्ड करें। Local testing के लिए Lemon Squeezy के test events इस्तेमाल करें और failures simulate करके retry/backoff behavior जाँचें। End-to-end flow—verification, deduplication, और asynchronous processing—को document करें ताकि टीम अलग-अलग environments में consistent results reproduce कर सके।
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        '`X-Signature` पढ़ें (raw body का HMAC-SHA256 **hex**) और raw request bytes प्राप्त करें।',
        'अपने signing secret से hex HMAC compute करें और timing-safe function से compare करें।',
        'Mismatch/हेडर missing होने पर reject करें; verification सफल होने के बाद ही JSON parse करें।',
        'Framework से raw body उपलब्ध कराएँ (re-serialization नहीं); handler को idempotent रखें और minimal diagnostics log करें।',
    ],

    'example_payload' => [
        'meta'     => ['event_name' => 'order_created'],
        'data'     => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path'   => '/api/webhooks/lemonsqueezy',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
