<?php

return [

    'hint' => 'اختبار سريع.',

    'summary' => <<<'TEXT'
هذا المزوّد الوهمي (mock) هو بيئة لعب حتمية وبدون بيانات اعتماد لاختبار دورة حياة الـ webhook بالكامل: إنشاء الطلب، انتقالات الحالة بطريقة idempotent، الإرسال، التحقق، إعادة المحاولة (retries)، ومعالجة الفشل. لأنه يعمل دون اعتماديات خارجية، يمكنك التكرار محليًا أو داخل CI، وتسجيل fixtures، وشرح قرارات المعمارية (مثل أين تضع التحقق مقابل الحفظ) دون تسريب أسرار حقيقية.

استخدمه لمحاكاة أوضاع الفشل الشائعة: تأخير التسليم، الإرسال المكرر، الأحداث خارج الترتيب، واستجابات 5xx المؤقتة التي تُطلق exponential backoff. كما يدعم الـ mock “أوضاع توقيع” مختلفة (بدون / HMAC-SHA256 / RSA-verify stub) حتى يتدرّب الفريق على hashing للجسم الخام، المقارنة بزمن ثابت (constant-time)، ونوافذ الطوابع الزمنية بشكل آمن. هذا يساعدك على التحقق من مفاتيح idempotency وجداول dedup قبل دمج بوابة دفع حقيقية.

ولجودة التوثيق، اجعل الـ mock قريبًا من الإنتاج: نفس أشكال الـ endpoints، والرؤوس، وأكواد الأخطاء؛ والاختلاف الوحيد هو جذر الثقة. قم بالإقرار بالـ webhooks الصحيحة بسرعة (2xx) ومرّر العمل الثقيل إلى مهام خلفية. وتعامل مع payload الخاص بالـ mock على أنه غير موثوق حتى ينجح التحقق—ثم طبّق قواعد العمل. النتيجة هي حلقة تغذية راجعة سريعة وعرض توضيحي قابل للنقل يعكس المعمارية التي ستطلقها.
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'أوضاع المحاكي: بدون / HMAC-SHA256 / RSA-verify stub؛ اختر عبر الإعدادات للتدرّب على مسارات التحقق.',
        'قم بعمل hash للجسم الخام تمامًا؛ وقارن بدالة timing-safe؛ وطبّق نوافذ إعادة تشغيل قصيرة (replay).',
        'سجّل معرفات الأحداث التي تمت معالجتها لضمان idempotency؛ وACK للـ webhooks الصحيحة بسرعة (2xx) وأجّل العمل الثقيل.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.succeeded',
        'provider' => 'mock',
        'data'     => [
            'payment_id' => 'pay_mock_001',
            'amount'     => 25000,
            'currency'   => 'IDR',
            'status'     => 'succeeded',
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
            'path'   => '/api/webhooks/mock',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
