<?php

return [

    'hint' => 'توقيع استدعاء رجوعي بأسلوب MD5/HMAC.',

    'summary' => <<<'TEXT'
ترسل Skrill إشعار الحالة (status callback) إلى `status_url` الخاص بك وتتوقع منك التحقق من الرسالة باستخدام `md5sig`، وهو **MD5 بحروف كبيرة** ناتج عن دمج حقول محددة بترتيب معروف (مثلًا: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`). لا تثق بالبيانات إلا إذا كانت القيمة التي تحسبها مطابقة تمامًا لـ `md5sig` الوارد. كما تدعم Skrill خيارًا بديلًا باسم `sha2sig` (SHA-2 بحروف كبيرة) عند الطلب، ويتم بناؤه بطريقة مماثلة لـ `md5sig`.

عمليًا، اجعل التحقق من التوقيع داخل الخادم فقط (لا تكشف secret word مطلقًا)، واحسب الهاش على **القيم نفسها تمامًا** كما تم إرسالها إليك في callback. اجعل نقطة النهاية idempotent (إزالة التكرار عبر transaction أو event ID)، وأعد 2xx بسرعة بعد الحفظ، وأجّل الأعمال غير الحرجة. أثناء التصحيح، سجّل نتيجة التحقق وهاشًا للجسم مع إبقاء الأسرار خارج السجلات. انتبه للتنسيق—قيم المبلغ والعملة يجب استخدامها كما هي عند بناء سلسلة التوقيع—لكي تبقى المقارنات ثابتة عبر الإعادات والبيئات المختلفة.
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        'أعد بناء `md5sig` بدقة: ادمج الحقول الموثّقة (مثل merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) ثم احسب **MD5 بحروف كبيرة**.',
        'قارن بالقيمة المستلمة `md5sig`؛ ويمكن استخدام `sha2sig` (SHA-2 بحروف كبيرة) إذا كانت مفعّلة من Skrill.',
        'نفّذ التحقق على الخادم فقط وبنفس القيم المرسلة؛ واجعل المعالج idempotent وأعد 2xx بسرعة.',
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
