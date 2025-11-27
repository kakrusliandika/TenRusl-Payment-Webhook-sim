<?php

return [

    'hint' => 'प्रोवाइडर-विशिष्ट कॉलबैक सिग्नेचर।',

    'summary' => <<<'TEXT'
OY! के कॉलबैक एक व्यापक सुरक्षा दृष्टिकोण का हिस्सा हैं, जो रजिस्टर्ड API keys और पार्टनर रिक्वेस्ट के लिए source IP allowlisting पर आधारित है। OY! एक Authorization Callback फीचर भी देता है जिससे आप कॉलबैक आपके सिस्टम तक पहुँचने से पहले उन्हें नियंत्रित और अनुमोदित कर सकते हैं—यह एक स्पष्ट gate जोड़ता है ताकि अनचाहे state changes रोके जा सकें। व्यवहार में, आपको हर इनकमिंग कॉलबैक को तब तक अविश्वसनीय (untrusted) मानना चाहिए जब तक वह सत्यापित न हो जाए, freshness लागू करनी चाहिए (timestamp/nonce window), और consumer को idempotent बनाना चाहिए ताकि retries और out-of-order delivery सुरक्षित रहें।

क्योंकि अलग-अलग प्रोवाइडर कॉलबैक को अलग तरीके से साइन करते हैं, हमारा सिम्युलेटर HMAC header (जैसे `X-Callback-Signature`) के साथ एक hardened baseline दिखाता है, जो shared secret के जरिए exact raw request body पर compute किया जाता है। यह production में उपयोग होने वाले वही सिद्धांत दर्शाता है: raw-byte hashing (कोई re-serialization नहीं), constant-time comparison, और छोटे replay windows। इसे छोटे dedup store और तेज 2xx acknowledgements के साथ जोड़ें ताकि प्रोवाइडर की retry logic स्वस्थ रहे और duplicate side effects से बचा जा सके।

Operational तौर पर, एक audit trail रखें (receipt time, verification outcome, body hash—secret नहीं), secrets को सुरक्षित रूप से rotate करें, और verification failure rates मॉनिटर करें। अगर आप allowlists पर निर्भर हैं, याद रखें वे बदल सकती हैं; cryptographic check (या OY का explicit authorization gate) प्राथमिक trust anchor बना रहना चाहिए। Endpoint को संकीर्ण, अनुमानित और अच्छी तरह documented रखें ताकि अन्य सेवाएँ और टीम के सदस्य इसे भरोसे के साथ reuse कर सकें।
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'OY! की सुरक्षा posture अपनाएँ: registered API key + partner requests के लिए source IP allowlisting।',
        'Authorization Callback (dashboard) का उपयोग करें ताकि कॉलबैक सिस्टम तक पहुँचने से पहले approve हो सकें।',
        'इस सिम्युलेटर में, best-practice मॉडल के रूप में `X-Callback-Signature = HMAC-SHA256(raw_body, secret)` verify करें; constant-time compare और freshness checks लागू करें।',
        'Processing को idempotent रखें और provider retries को healthy रखने के लिए जल्दी 2xx लौटाएँ।',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.completed',
        'provider' => 'oy',
        'data'     => [
            'partner_trx_id' => 'PRT-001',
            'amount'         => 25000,
            'currency'       => 'IDR',
            'status'         => 'COMPLETED',
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
            'path'   => '/api/webhooks/oy',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
