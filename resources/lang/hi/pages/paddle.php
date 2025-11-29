<?php

return [

    'hint' => 'पब्लिक-की सिग्नेचर (Classic) / सीक्रेट (Billing)।',

    'summary' => <<<'TEXT'
Paddle Billing हर webhook को `Paddle-Signature` हेडर के साथ साइन करता है, जिसमें Unix timestamp (`ts`) और signature (`h1`) शामिल होते हैं। मैन्युअल वेरिफिकेशन के लिए, timestamp, एक colon, और बिल्कुल वही raw request body जोड़कर signed payload बनाएं; फिर उस payload को अपनी notification destination के secret key से hash करें और `h1` के साथ constant-time (timing-safe) फ़ंक्शन से तुलना करें। Paddle हर notification destination के लिए अलग secret बनाता है—इसे पासवर्ड की तरह ट्रीट करें और source control से बाहर रखें।

किसी भी parsing से पहले वेरिफाई करने के लिए ऑफिशियल SDKs या अपना middleware इस्तेमाल करें। क्योंकि timing और body transforms आम pitfalls हैं, सुनिश्चित करें कि आपका framework raw bytes एक्सपोज़ करे (जैसे Express `express.raw({ type: 'application/json' })`) और replay रोकने के लिए `ts` पर छोटा tolerance लागू करें। वेरिफिकेशन के बाद जल्दी acknowledge (2xx) करें, idempotency के लिए event ID स्टोर करें, और heavy work को background jobs में भेज दें। इससे delivery reliable रहती है और retries के दौरान duplicate side effects नहीं होते।

Paddle Classic से माइग्रेट करते समय ध्यान दें कि वेरिफिकेशन पब्लिक-की सिग्नेचर से Billing के लिए secret-based HMAC पर शिफ्ट हो गया है। उसी अनुसार runbooks और secrets management अपडेट करें और बदलाव rollout करते समय verification metrics मॉनिटर करें। (बिना secrets के) साफ़ लॉग्स और deterministic error responses incident handling और partner support को काफी आसान बनाते हैं।
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        '`Paddle-Signature` हेडर पढ़ें; `ts` और `h1` वैल्यूज़ पार्स करें।',
        'Signed payload बनाएं = `ts + ":" + <raw request body>`; endpoint secret key से hash करें।',
        'अपने hash की `h1` से timing-safe फ़ंक्शन द्वारा तुलना करें; replay रोकने के लिए `ts` पर छोटा tolerance लागू करें।',
        'ऑफिशियल SDKs या verification middleware को प्राथमिकता दें; वेरिफिकेशन सफल होने के बाद ही JSON parse करें।',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
