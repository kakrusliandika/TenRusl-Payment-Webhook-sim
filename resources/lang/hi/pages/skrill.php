<?php

return [

    'hint' => 'MD5/HMAC शैली की कॉलबैक सिग्नेचर।',

    'summary' => <<<'TEXT'
Skrill आपके `status_url` पर एक स्टेटस कॉलबैक पोस्ट करता है और आपसे `md5sig` का उपयोग करके संदेश को वैलिडेट करने की अपेक्षा करता है—यह एक **UPPERCASE MD5** है जो निश्चित फ़ील्ड-कनकैटनेशन से बनता है (उदाहरण: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`). केवल तभी payload पर भरोसा करें जब आपका computed वैल्यू incoming `md5sig` से पूरी तरह मैच करे। Skrill अनुरोध पर एक वैकल्पिक `sha2sig` (UPPERCASE SHA-2) भी सपोर्ट करता है, जो `md5sig` की तरह ही निर्मित होता है।

व्यवहार में, सिग्नेचर वैलिडेशन हमेशा बैकएंड पर रखें (secret word कभी एक्सपोज़ न करें) और **उसी exact** पैरामीटर वैल्यूज़ को हैश करें जो Skrill ने आपको पोस्ट की हैं। endpoint को idempotent रखें (transaction या event ID से dedup), persistence के बाद जल्दी 2xx लौटाएँ और noncritical काम defer करें। debugging के दौरान verification परिणाम और body hash लॉग करें, लेकिन secrets को logs से बाहर रखें। formatting पर सावधानी रखें—amount और currency फ़ील्ड्स को सिग्नेचर स्ट्रिंग बनाते समय verbatim उपयोग करें—ताकि retries और environments में तुलना स्थिर रहे।
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        '`md5sig` बिल्कुल वैसे ही रीबिल्ड करें: डॉक्यूमेंटेड फ़ील्ड्स (जैसे merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) को concatenate करके **UPPERCASE MD5** निकालें।',
        'प्राप्त `md5sig` से compare करें; Skrill द्वारा enable होने पर `sha2sig` (UPPERCASE SHA-2) वैकल्पिक रूप से उपयोग करें।',
        'वैलिडेशन केवल server-side करें और exact posted values का उपयोग करें; handler idempotent रखें और जल्दी 2xx लौटाएँ।',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount' => '10.00',
        'mb_currency' => 'EUR',
        'status' => '2',
        'md5sig' => '<UPPERCASE_MD5>',
        'provider' => 'skrill',
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
            'path' => '/api/webhooks/skrill',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
