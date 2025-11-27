<?php

return [

    'hint' => 'توقيع بالمفتاح العام (Classic) / سرّ (Billing).',

    'summary' => <<<'TEXT'
تقوم Paddle Billing بتوقيع كل webhook عبر ترويسة `Paddle-Signature` التي تتضمن طابعًا زمنيًا بنظام Unix (`ts`) وتوقيعًا (`h1`). للتحقق يدويًا، قم بضمّ قيمة الطابع الزمني ثم نقطتين ثم جسم الطلب الخام تمامًا لبناء الرسالة الموقَّعة؛ بعد ذلك احسبها باستخدام المفتاح السري لوجهة الإشعارات لديك وقارن النتيجة مع `h1` بدالة مقارنة بزمن ثابت (constant-time). تنشئ Paddle سرًا منفصلًا لكل وجهة إشعارات—تعامل معه ككلمة مرور وأبعده عن مستودع الشيفرة.

استخدم الـ SDK الرسمي أو middleware خاصًا بك للتحقق قبل أي عملية parsing. وبسبب أن التوقيت وتحويلات الجسم من أكثر مصادر الأخطاء شيوعًا، تأكد أن إطار العمل يتيح لك الوصول إلى البايتات الخام (مثل Express: `express.raw({ type: 'application/json' })`) وطبّق نافذة تسامح قصيرة لقيمة `ts` لمنع إعادة التشغيل (replay). بعد نجاح التحقق، اعترف بسرعة (2xx)، خزّن معرّف الحدث لضمان idempotency، وانقل العمل الثقيل إلى مهام خلفية. هذا يجعل التسليم موثوقًا ويمنع الآثار الجانبية المكررة عند وجود retries.

وعند الترحيل من Paddle Classic، لاحظ أن التحقق انتقل من توقيعات بالمفتاح العام إلى HMAC قائم على سرّ في Billing. حدّث إجراءات التشغيل (runbooks) وإدارة الأسرار وفقًا لذلك، وراقب مؤشرات التحقق عند نشر التغييرات. سجلات واضحة (بدون أسرار) واستجابات خطأ حتمية تسهّل كثيرًا التعامل مع الحوادث ودعم الشركاء.
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        'اقرأ ترويسة `Paddle-Signature`؛ واستخرج قيم `ts` و `h1`.',
        'ابنِ payload الموقَّع = `ts + ":" + <raw request body>`؛ ثم احسبه باستخدام مفتاح سرّ الـ endpoint.',
        'قارن ناتجك مع `h1` بدالة timing-safe؛ وطبّق نافذة تسامح قصيرة على `ts` لمنع replay.',
        'فضّل الـ SDK الرسمي أو middleware للتحقق؛ ولا تقم بقراءة/تحليل JSON إلا بعد نجاح التحقق.',
    ],

    'example_payload' => [
        'event_id'   => 'evt_' . now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider'   => 'paddle',
        'data'       => [
            'transaction_id' => 'txn_001',
            'amount'         => 25000,
            'currency_code'  => 'IDR',
            'status'         => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/paddle',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
