<?php

return [

    'hint' => 'Verify Webhook Signature API।',

    'summary' => <<<'TEXT'
PayPal हर webhook के लिए आधिकारिक Verify Webhook Signature API के माध्यम से server-side verification की मांग करता है। आपके listener को notification के साथ भेजे गए headers—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL`, और `PAYPAL-TRANSMISSION-SIG`—के साथ आपकी `webhook_id` और **raw** request body (`webhook_event`) निकालनी होती है। इन्हें verification endpoint पर POST करें और event को तभी स्वीकार करें जब PayPal success result लौटाए। यह पुराने verification तरीकों को replace करता है और REST products में consistency आसान बनाता है।

Consumer को एक तेज़, idempotent gate की तरह बनाएं: पहले verify करें, event record persist करें, 2xx के साथ acknowledge करें, और heavy work को queue में भेज दें। किसी भी local check के लिए constant-time compare का उपयोग करें और PayPal को forward करते समय raw bytes को ज्यों का त्यों रखें ताकि re-serialization से होने वाले subtle bugs से बचा जा सके। Replay window घटाने के लिए `PAYPAL-TRANSMISSION-TIME` के आसपास tight time tolerance enforce करें और minimal audit data log करें (request ID, verification outcome, body hash—secrets नहीं)। इस pattern से duplicate deliveries और partial outages double processing नहीं कर पाएंगे, और incident response में audit trail भरोसेमंद रहेगा।
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'Headers इकट्ठा करें: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; raw body को सुरक्षित रखें।',
        'इन values के साथ webhook_id और webhook_event जोड़कर Verify Webhook Signature API कॉल करें; केवल success पर ही स्वीकार करें।',
        'Verification को gate की तरह ट्रीट करें; replays कम करने के लिए छोटा time tolerance लागू करें और consumer को idempotent बनाएं।',
        'तेज़ी से 2xx लौटाएं, heavy work को queue करें, और minimal diagnostics log करें (secrets नहीं)।',
    ],

    'example_payload' => [
        'id' => 'WH-'.now()->timestamp,
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider' => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paypal',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
