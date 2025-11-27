<?php

return [

    'hint' => 'कॉलबैक टोकन सिग्नेचर।',

    'summary' => <<<'TEXT'
Xendit webhook इवेंट्स को प्रति-अकाउंट टोकन से “साइन” करता है, जो `x-callback-token` हेडर में दिया जाता है। आपकी इंटीग्रेशन को इस हेडर की तुलना Xendit डैशबोर्ड से प्राप्त टोकन से करनी चाहिए और जिस अनुरोध में टोकन गायब हो या मैच न करे, उसे reject करना चाहिए। कुछ webhook प्रोडक्ट्स `webhook-id` भी देते हैं, जिसे आप retries के दौरान डुप्लिकेट प्रोसेसिंग रोकने के लिए स्टोर कर सकते हैं।

ऑपरेशनल रूप से, verification को पहला कदम रखें, immutable event record persist करें, जल्दी 2xx acknowledge करें, और heavy work को queues में भेजें। idempotency के लिए `webhook-id` (या अपना key) उपयोग करें और यदि timestamp metadata हो तो कड़ा time window लागू करें। पूरा फ्लो (verification, deduplication, retries, और error codes) डॉक्युमेंट करें ताकि टीम/सेवाएँ सभी environments में एक जैसा इंटीग्रेट कर सकें।
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        '`x-callback-token` को Xendit डैशबोर्ड के आपके यूनिक टोकन से compare करें; mismatch पर reject करें।',
        '`webhook-id` (यदि मौजूद हो) से deduplicate करें; JSON parse करने से पहले verification को hard gate मानें।',
        'तेज़ 2xx लौटाएँ और heavy work defer करें; secrets एक्सपोज़ किए बिना minimal diagnostics log करें।',
    ],

    'example_payload' => [
        'id'       => 'evt_xnd_' . now()->timestamp,
        'event'    => 'invoice.paid',
        'data'     => [
            'id'     => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
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
            'path'   => '/api/webhooks/xendit',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
