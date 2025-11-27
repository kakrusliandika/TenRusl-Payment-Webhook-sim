<?php

return [

    'hint' => 'प्रत्येक अनुरोध पर x-amzn-signature हेडर।',

    'summary' => <<<'TEXT'
Buy with Prime (BWP) हर webhook पर सिग्नेचर लगाता है ताकि आप सुनिश्चित कर सकें कि वह वास्तव में Amazon से आया है और ट्रांज़िट के दौरान उसमें कोई बदलाव नहीं हुआ। हर अनुरोध में डिजिटल सिग्नेचर `x-amzn-signature` हेडर में शामिल होता है। आपके हैंडलर को दिए गए event type और environment के लिए BWP दस्तावेज़ में परिभाषित तरीके से अपेक्षित सिग्नेचर को बिल्कुल वैसा ही दोबारा बनाना होगा; यदि मान मेल नहीं खाते, तो कॉल को अस्वीकार कर दें। अनुरोध के साथ आने वाले किसी भी timestamp/nonce को anti-replay रणनीति का हिस्सा मानें—सख्त वैधता विंडो लागू करें और पहले से प्रोसेस किए गए identifiers को स्टोर करें ताकि डुप्लीकेट से बचा जा सके।

ऑपरेशनल दृष्टि से, endpoint को तेज़ और निर्धारक (deterministic) बनाएं: पहले वेरिफिकेशन करें, डेटा सुरक्षित रूप से रिकॉर्ड हो जाने पर `2xx` से ack करें, और भारी काम asynchronous तरीके से चलाएँ। यदि आप allowlists पर निर्भर हैं, तो याद रखें कि IP और नेटवर्क बदल सकते हैं—क्रिप्टोग्राफिक वेरिफिकेशन ही प्राथमिक trust anchor है। एक सुरक्षित ऑडिट ट्रेल रखें (request ID, सिग्नेचर की मौजूदगी, वेरिफिकेशन परिणाम, और body का hash—secret नहीं)। लोकल टेस्टिंग के लिए, आप environment flag के पीछे वेरिफिकेशन स्टेप को stub कर सकते हैं, लेकिन यह सुनिश्चित करें कि production पाथ हमेशा सिग्नेचर की जाँच करें। जब आप keys rotate करें या canonicalization नियम अपडेट करें, तो सावधानी से रोल-आउट करें, error rate मॉनिटर करें, और आपने जो भी header सेट और hashing/canonicalization लागू की है उसे अच्छी तरह डॉक्यूमेंट करें ताकि आपके stack की बाकी सेवाएँ भी उसी कदम से चल सकें।

इंटीग्रेशन की सुविधा के नज़रिए से, **स्पष्ट विफलता कारण** (अमान्य सिग्नेचर, पुराना timestamp, खराब अनुरोध) उजागर करें और स्थिर error codes वापस करें ताकि retry व्यवहार पूर्वानुमेय रहे। इसे application-लेवल idempotency और replay सुरक्षा के साथ मिलाकर उपयोग करें ताकि डाउनस्ट्रीम payment state transitions retries, ट्रैफ़िक बर्स्ट या आंशिक आउटेज के दौरान भी सुरक्षित बने रहें।
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        'अनुरोध के हेडर से `x-amzn-signature` पढ़ें।',
        'Buy with Prime द्वारा परिभाषित नियमों (एल्गोरिथ्म/कैनोनिकलाइज़ेशन, आधिकारिक डॉक्युमेंटेशन के अनुसार) के आधार पर अपेक्षित सिग्नेचर को बिल्कुल वैसा ही दोबारा बनाएँ; mismatch होने पर अनुरोध अस्वीकार करें।',
        'अगर timestamp/nonce उपलब्ध हो, तो replay हमलों को कम करने के लिए सख्त freshness window लागू करें और प्रोसेस हुई IDs को स्टोर करके डुप्लीकेट से बचें।',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data'      => [
            'orderId'  => 'BWP-001',
            'status'   => 'COMPLETED',
            'amount'   => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
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
            'path'   => '/api/webhooks/amazon_bwp',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
