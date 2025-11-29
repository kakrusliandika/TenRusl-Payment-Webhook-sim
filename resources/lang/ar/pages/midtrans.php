<?php

return [

    'hint' => 'التحقق من signature_key.',

    'summary' => <<<'TEXT'
تضمّن Midtrans قيمة `signature_key` محسوبة داخل كل إشعار HTTP(S) حتى تتمكن من التحقق من المصدر قبل تنفيذ أي إجراء. المعادلة واضحة وثابتة:
    SHA512(order_id + status_code + gross_amount + ServerKey)
قم ببناء سلسلة الإدخال باستخدام القيم الدقيقة من جسم الإشعار (كسلاسل نصية) مع `ServerKey` الخاص بك (السري)، ثم احسب ناتج SHA-512 بصيغة hex وقارنه مع `signature_key` باستخدام مقارنة بزمن ثابت (constant-time). إذا فشل التحقق، تجاهل الإشعار. أمّا للرسائل الصحيحة، فاستخدم الحقول الموثقة (مثل `transaction_status`) لتشغيل آلة الحالات لديك—قم بالإقرار سريعًا (2xx)، وضع الأعمال الثقيلة في قائمة انتظار، واجعل التحديثات idempotent تحسبًا لإعادة المحاولة أو وصول الإشعارات بترتيب غير متوقع.

هناك فخّان شائعان: التنسيق والتحويل. أبقِ `gross_amount` تمامًا كما ورد (لا تُحوّله لصيغة محلية ولا تغيّر الكسور العشرية) عند تكوين السلسلة، وتجنب القصّ أو تغييرات الأسطر/المسافات. خزّن مفتاح deduplication لكل حدث أو لكل طلب لحماية النظام من حالات السباق؛ وسجّل نتيجة التحقق وهاش للجسم لأغراض التدقيق دون تسريب الأسرار. وادمج ذلك مع تحديد المعدّل (rate limiting) لنقطة النهاية وأكواد فشل واضحة حتى تتمكن المراقبة من التمييز بين الأخطاء المؤقتة (صالحة لإعادة المحاولة) والرفض الدائم (توقيع غير صالح).
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'خذ `order_id` و`status_code` و`gross_amount` من الجسم (كسلاسل نصية) ثم أضف `ServerKey` الخاص بك.',
        'احسب `SHA512(order_id + status_code + gross_amount + ServerKey)` وقارنه مع `signature_key` (مقارنة constant-time).',
        'ارفض عند عدم التطابق؛ وإلا حدّث الحالة اعتمادًا على `transaction_status`. اجعل المعالجة idempotent وأعد 2xx بسرعة.',
        'احذر تغييرات تنسيق `gross_amount` وأي مسافات/فراغات غير مقصودة أثناء الدمج.',
    ],

    'example_payload' => [
        'order_id' => 'ORDER-001',
        'status_code' => '200',
        'gross_amount' => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key' => '<sha512>',
        'provider' => 'midtrans',
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
            'path' => '/api/webhooks/midtrans',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
