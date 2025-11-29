<?php

return [

    'hint' => 'ترويسة توقيع مع طابع زمني.',

    'summary' => <<<'TEXT'
تقوم Stripe بتوقيع كل طلب webhook وتضع التوقيع المحسوب داخل ترويسة `Stripe-Signature`. يجب على نقطتك الطرفية التحقق من الطلب قبل تنفيذ أي عمل. باستخدام مكتبات Stripe الرسمية، مرّر ثلاث مدخلات إلى روتين التحقق: جسم الطلب الخام تمامًا (raw body)، وترويسة `Stripe-Signature`، و“سرّ” نقطة النهاية (endpoint secret). لا تتابع إلا عند نجاح التحقق؛ وإلا فأعد استجابة غير 2xx وتوقّف عن المعالجة. وعندما لا يمكنك استخدام مكتبة رسمية، نفّذ التحقق اليدوي كما هو موثّق، بما في ذلك فحوصات سماحية الطابع الزمني لتقليل مخاطر إعادة التشغيل (replay).

اعتبر التحقق من التوقيع بوابة صارمة. اجعل المعالج idempotent (بتخزين معرفات الأحداث)، وأعد 2xx بسرعة بعد الحفظ، وادفع العمل الثقيل إلى مهام خلفية. تأكّد أن إطار العمل لديك يوفّر **البايتات الخام**—تجنّب إعادة تسلسل JSON قبل الهاش، لأن أي تغيير في المسافات أو ترتيب الحقول سيكسر فحص التوقيع. أخيرًا، سجّل أقل قدر ممكن من التشخيص (نتيجة التحقق، نوع الحدث، تجزئة للجسم—بدون أسرار) وراقب الإخفاقات أثناء تدوير السر أو تغييرات نقطة النهاية.
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        'اقرأ ترويسة `Stripe-Signature`؛ واحصل على endpoint secret من لوحة تحكم Stripe.',
        'تحقق باستخدام المكتبات الرسمية عبر تمرير: raw body، وترويسة `Stripe-Signature`، وendpoint secret.',
        'عند التحقق اليدوي، طبّق سماحية للطابع الزمني لتقليل replay، وقارن التواقيع باستخدام مقارنة آمنة زمنيًا (timing-safe).',
        'اقبل فقط عند النجاح؛ خزّن معرفات الأحداث للـ idempotency وأعد 2xx بسرعة بعد الحفظ.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider' => 'stripe',
        'created_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/stripe',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
