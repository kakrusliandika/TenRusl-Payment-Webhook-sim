<?php

return [

    'hint' => 'टाइमस्टैम्प वाला सिग्नेचर हेडर।',

    'summary' => <<<'TEXT'
Stripe हर webhook अनुरोध पर हस्ताक्षर करता है और गणना किया हुआ सिग्नेचर `Stripe-Signature` हेडर में देता है। आपका endpoint कोई भी काम करने से पहले अनुरोध को verify करे। Stripe की आधिकारिक लाइब्रेरियों के साथ, verification रूटीन में तीन इनपुट दें: बिल्कुल वही raw request body, `Stripe-Signature` हेडर, और आपका endpoint secret। verification सफल होने पर ही आगे बढ़ें; अन्यथा non-2xx लौटाएँ और प्रोसेसिंग रोक दें। यदि आप आधिकारिक लाइब्रेरी का उपयोग नहीं कर सकते, तो दस्तावेज़ के अनुसार manual verification लागू करें, जिसमें replay जोखिम घटाने के लिए timestamp tolerance checks भी शामिल हों।

सिग्नेचर verification को सख्त gate की तरह ट्रीट करें। handler को idempotent रखें (event IDs स्टोर करें), persistence के बाद जल्दी 2xx लौटाएँ, और heavy work को background jobs में भेजें। सुनिश्चित करें कि आपका framework **raw bytes** उपलब्ध कराता है—hashing से पहले JSON को दोबारा serialize न करें, क्योंकि whitespace या ordering में बदलाव सिग्नेचर verification तोड़ देगा। अंत में, minimal diagnostics लॉग करें (verification outcome, event type, body hash—secrets नहीं) और secret rotation या endpoint changes के दौरान failures मॉनिटर करें।
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        '`Stripe-Signature` हेडर पढ़ें; Stripe dashboard से endpoint secret लें।',
        'आधिकारिक लाइब्रेरियों से verify करें: raw request body, `Stripe-Signature`, और endpoint secret पास करें।',
        'manual verification में timestamp tolerance लागू करें (replay कम करने हेतु) और signatures को timing-safe तरीके से compare करें।',
        'केवल success पर accept करें; idempotency के लिए event IDs स्टोर करें और persistence के बाद जल्दी 2xx लौटाएँ।',
    ],

    'example_payload' => [
        'id'   => 'evt_' . now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id'     => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider'   => 'stripe',
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
            'path'   => '/api/webhooks/stripe',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
