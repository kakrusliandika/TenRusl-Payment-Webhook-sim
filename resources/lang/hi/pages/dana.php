<?php

return [

    'hint' => 'RSA (DANA की public key से verify करें)।',

    'summary' => <<<'TEXT'
DANA एक **असममित (asymmetric)** सिग्नेचर स्कीम इस्तेमाल करता है: रिक्वेस्ट को private key से साइन किया जाता है, और इंटीग्रेटर आधिकारिक **DANA public key** से उसे verify करते हैं। व्यवहार में, आप webhook header (उदाहरण के लिए `X-SIGNATURE`) से सिग्नेचर निकालते हैं, उसे base64-decode करते हैं, फिर RSA-2048 + SHA-256 का उपयोग करके उसी सिग्नेचर के खिलाफ raw HTTP request body को verify करते हैं। केवल तभी payload को authentic माना जाना चाहिए जब verification का परिणाम सकारात्मक हो। यदि verification विफल हो—या signature/header मौजूद न हो—तो non-2xx कोड के साथ जवाब दें और प्रोसेसिंग रोक दें।

क्योंकि webhooks को retry किया जा सकता है या वे out of order भी आ सकते हैं, अपने handler को idempotent बनाएं: एक unique event identifier को persist करें और duplicate होने पर short-circuit करें; replay को कम करने के लिए किसी भी timestamp/nonce की freshness जाँचें; और signature verification सफल होने तक सभी फ़ील्ड्स को untrusted मानें। verification से पहले JSON को दोबारा serialize न करें; ठीक वही bytes hash/verify करें जो wire पर आए थे। secrets और private keys को logs से बाहर रखें; अगर log करना ज़रूरी हो, तो केवल high-level diagnostics रिकॉर्ड करें (verification result, body का hash, event ID) और उन logs को at rest सुरक्षित रखें।

टीमों के लिए, एक छोटा runbook प्रकाशित करें जिसमें शामिल हो: DANA public key को कैसे load या rotate करना है, आपके इस्तेमाल किए गए हर language/runtime में verify कैसे करना है, आपकी integration के लिए exact string-to-sign नियम क्या हैं, और कौन-सी विफलता permanent बनाम transient मानी जाएगी। इसे एक मजबूत retry/backoff policy, bounded work queues, health checks, और verification failures पर alerts के साथ जोड़ें। नतीजा एक ऐसा webhook consumer होगा जो load के तहत सुरक्षित है, retries के प्रति resilient है, और DANA द्वारा आवश्यक cryptographic verification के अनुरूप है।
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        '`X-SIGNATURE` header के value को base64-decode करें।',
        'आधिकारिक DANA public key का उपयोग करके exact raw HTTP body पर RSA-2048 + SHA-256 verify करें; केवल verification positive होने पर स्वीकार करें।',
        'missing/invalid signature या malformed payload वाले किसी भी webhook को reject करें; सफल verification से पहले डेटा पर कभी भरोसा न करें।',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.paid',
        'provider' => 'dana',
        'data'     => [
            'transaction_id' => 'DANA-001',
            'amount'         => 25000,
            'currency'       => 'IDR',
            'status'         => 'SUCCESS',
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
            'path'   => '/api/webhooks/dana',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
