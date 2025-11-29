<?php

return [

    'hint' => 'Client-Id/Request-* हेडर्स के साथ सिग्नेचर।',

    'summary' => <<<'TEXT'
DOKU HTTP Notifications को एक canonical, header-driven सिग्नेचर से सुरक्षित करता है, जिसे किसी भी payload पर कार्रवाई करने से पहले verify करना ज़रूरी है। हर callback के साथ `Signature` हेडर आता है, जिसका मान `HMACSHA256=<base64>` के रूप में होता है। अपेक्षित मान को पुनर्निर्मित करने के लिए, पहले request body के लिए `Digest` निकालें: raw JSON bytes पर SHA-256, फिर base64-encode करें। इसके बाद, ठीक इसी क्रम और स्पेलिंग के साथ पाँच घटकों से एक newline-delimited स्ट्रिंग बनाएं:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (उदा. `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
फिर उस canonical स्ट्रिंग पर SHA-256 के साथ HMAC निकालें (key के रूप में आपका DOKU Secret Key), परिणाम को base64-encode करें और आगे `HMACSHA256=` जोड़ दें। अंत में, `Signature` हेडर के साथ constant-time comparison के जरिए तुलना करें। किसी भी mismatch, missing component, या malformed value को authentication failure माना जाना चाहिए और request को तुरंत reject करना चाहिए।

Resilience और safety के लिए, valid notifications को जल्दी (2xx) ACK करें और भारी काम को background jobs में भेजें ताकि retries ट्रिगर न हों। Consumer को idempotent बनाएं: processed identifiers (जैसे `Request-Id` या body में event ID) रिकॉर्ड करके duplicates को short-circuit करें। Freshness validate करें: `Request-Timestamp` एक tight window के भीतर होना चाहिए ताकि replay attacks रोके जा सकें; और `Request-Target` आपके असली route path से match हो—ताकि canonicalization bugs न हों। Parsing में DOKU के non-strict guidance का पालन करें: unknown fields को ignore करें और brittle parsers की बजाय schema evolution को प्राथमिकता दें। Incident response के दौरान, required headers की मौजूदगी, computed digest/signature (कभी secret नहीं), और body का hash लॉग करें ताकि sensitive data लीक किए बिना auditing में मदद मिले।
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'हेडर्स पढ़ें: `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature`, और `Request-Target` (आपका route path) infer करें।',
        '`Digest = base64( SHA256(raw JSON body) )` compute करें।',
        'Canonical string को इन लाइनों के साथ बनाएं: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (इसी क्रम में, हर लाइन अलग, अंत में trailing newline नहीं)।',
        'expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )` compute करें; `Signature` से constant-time comparison करें।',
        'Timestamp freshness enforce करें; processing को idempotent बनाएं; जल्दी ACK (2xx) करें और heavy work offload करें।',
    ],

    'example_payload' => [
        'order' => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider' => 'doku',
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
            'path' => '/api/webhooks/doku',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
