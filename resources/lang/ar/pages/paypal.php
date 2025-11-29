<?php

return [

    'hint' => 'واجهة برمجة تطبيقات التحقق من توقيع Webhook.',

    'summary' => <<<'TEXT'
يتطلب PayPal التحقق من كل webhook على جهة الخادم عبر واجهة Verify Webhook Signature الرسمية. يجب على مستمعك استخراج الرؤوس المرسلة مع الإشعار—`PAYPAL-TRANSMISSION-ID` و `PAYPAL-TRANSMISSION-TIME` و `PAYPAL-CERT-URL` و `PAYPAL-TRANSMISSION-SIG`—بالإضافة إلى `webhook_id` لديك وجسم الطلب **الخام** (`webhook_event`). أرسل هذه القيم إلى نقطة تحقق التوقيع ولا تقبل الحدث إلا إذا أعاد PayPal نتيجة نجاح. هذا يستبدل آليات التحقق الأقدم ويبسّط التوافق عبر منتجات REST.

ابنِ المستهلك كـ “بوابة” سريعة ويدعم idempotency: تحقق أولاً، خزّن سجل حدث، اعترف بـ 2xx، وادفع العمل الثقيل إلى طابور. استخدم مقارنة بزمن ثابت لأي فحوصات محلية واحتفظ بالبايتات الخام عند تمريرها إلى PayPal لتجنب أخطاء إعادة التسلسل الدقيقة. طبّق سماحية زمنية ضيقة حول `PAYPAL-TRANSMISSION-TIME` لتقليل نافذة إعادة التشغيل، وسجّل أقل قدر ممكن من بيانات التدقيق (معرّف الطلب، نتيجة التحقق، تجزئة الجسم—دون أسرار). بهذه البنية، لن تسبب التسليمات المكررة أو الأعطال الجزئية معالجة مزدوجة، وسيبقى سجل التدقيق موثوقًا أثناء الاستجابة للحوادث.
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'اجمع الرؤوس: PAYPAL-TRANSMISSION-ID و PAYPAL-TRANSMISSION-TIME و PAYPAL-CERT-URL و PAYPAL-TRANSMISSION-SIG؛ واحتفظ بالجسم الخام.',
        'استدعِ Verify Webhook Signature API بهذه القيم إضافة إلى webhook_id و webhook_event؛ ولا تقبل إلا عند النجاح.',
        'عامل التحقق كـ بوابة؛ وطبّق سماحية زمنية قصيرة لتخفيف replays واجعل المستهلك idempotent.',
        'أعد 2xx بسرعة، وضع العمل الثقيل في طابور، وسجّل تشخيصًا محدودًا (بدون أسرار).',
    ],

    'example_payload' => [
        'id' => 'WH-'.now()->timestamp,
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider' => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paypal',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
