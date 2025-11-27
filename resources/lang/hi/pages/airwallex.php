<?php

return [

    'hint' => 'x-timestamp + body पर HMAC-SHA256।',

    'summary' => <<<'TEXT'
Airwallex वेबहुक पर सिग्नेचर लगाए जाते हैं ताकि आप डेटाबेस को छूने से पहले ही उनकी प्रामाणिकता (authenticity) और अखंडता (integrity) की जाँच कर सकें। हर रिक्वेस्ट में दो महत्वपूर्ण हेडर होते हैं: `x-timestamp` और `x-signature`। किसी संदेश को वैलिडेट करने के लिए, HTTP का रॉ बॉडी बिल्कुल वैसे ही पढ़ें जैसा वह प्राप्त हुआ है, फिर उस रॉ बॉडी के आगे `x-timestamp` का मान (स्ट्रिंग के रूप में) जोड़ें और इसी को डाइजेस्ट इनपुट के रूप में उपयोग करें। अब SHA-256 के साथ HMAC निकालें और कुंजी के रूप में अपनी नोटिफिकेशन URL का shared secret इस्तेमाल करें। Airwallex परिणाम को **hex digest** के रूप में अपेक्षा करता है; इस मान की तुलना `x-signature` हेडर से करें, और यह तुलना constant-time तरीके से करें ताकि टाइ밍 लीक न हों। अगर सिग्नेचर मेल नहीं खाते, या टाइमस्टैम्प गायब/अमान्य हो, तो रिक्वेस्ट को असफल मानें और non-2xx रिस्पॉन्स लौटाएँ।

क्योंकि किसी भी वेबहुक सिस्टम के लिए replay एक वास्तविक खतरा है, `x-timestamp` पर freshness window लागू करें। बहुत पुराने या बहुत भविष्य के टाइमस्टैम्प वाली रिक्वेस्ट को रिजेक्ट कर दें, और प्रोसेस हो चुके event IDs को स्टोर करें ताकि डाउनस्ट्रीम साइड इफेक्ट्स का डेडुप्लीकेशन हो सके (यानी आपके एप्लिकेशन लेयर पर idempotency)। payload को तब तक अनट्रस्टेड मानें जब तक वेरिफिकेशन पास न हो जाए; JSON को दोबारा stringify करके हैश न करें—आने वाले raw bytes को ही इस्तेमाल करें, ताकि whitespace या ordering के सूक्ष्म अंतर से बचा जा सके। जब वेरिफिकेशन सफल हो जाए, तो तुरंत `2xx` रिस्पॉन्स लौटाएँ; भारी काम asynchronous तरीके से करें ताकि retry लॉजिक हेल्दी रहे और accidental डुप्लीकेट कम हों।

लोकल और CI फ्लो के लिए Airwallex बेहतरीन टूलिंग देता है: डैशबोर्ड में अपनी notification URLs कॉन्फ़िगर करें, उदाहरण payload देखें, और अपने endpoint पर **टेस्ट इवेंट भेजें**। डिबग करते समय, प्राप्त `x-timestamp`, आपके द्वारा निकाले गए सिग्नेचर का एक प्रीव्यू (secrets कभी लॉग न करें) और अगर event identifier मौजूद हो तो उसे लॉग करें। अगर आप secret key rotate करते हैं, तो यह काम सुरक्षित ढंग से करें और signature error rate पर नज़र रखें। अंत में, पूरी चेन—verification, deduplication, retries और error responses—को डॉक्यूमेंट करें ताकि टीम के दूसरे सदस्य भी वही raw-body hashing rules और time window उपयोग करके परिणामों को reproduce कर सकें।
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        'हेडर से `x-timestamp` और `x-signature` निकालें।',
        'value_to_digest = <x-timestamp> + <raw HTTP body> (सटीक बाइट्स) बनाएँ।',
        'expected = HMAC-SHA256(value_to_digest, <webhook secret>) को HEX में निकालें; `x-signature` से constant-time comparison के साथ तुलना करें।',
        'अगर सिग्नेचर मेल न खाएँ या टाइमस्टैम्प पुराना हो, तो रिजेक्ट करें; साथ ही idempotency के लिए प्रोसेस हो चुके event IDs को डेडुप्लीकेट करें।',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment_intent.succeeded',
        'data'     => [
            'payment_intent_id' => 'pi_awx_001',
            'amount'            => 25000,
            'currency'          => 'IDR',
            'status'            => 'succeeded',
        ],
        'provider'   => 'airwallex',
        'created_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/airwallex',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
