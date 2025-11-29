<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
تقوم TriPay بإرسال callbacks إلى الرابط الذي تقوم بإعداده، وتضمّن ترويسات (headers) تحدد الحدث وتساعدك على التحقق من هوية المرسل. وبشكل خاص، تحمل callbacks الترويسة `X-Callback-Event` بقيمة مثل `payment_status`، وترويسة `X-Callback-Signature` للتحقق من التوقيع وفقًا لوثائق TriPay. يجب على المستهلك (consumer) قراءة هذه الترويسات، والتحقق من صحة الطلب كما هو موضح، وبعدها فقط يتم تحديث الحالة داخل النظام.

صمّم نقطة النهاية لتكون سريعة ويديمبوتنت (idempotent). استخدم نافذة صلاحية قصيرة إذا كانت هناك طوابع زمنية/nonce، واحتفظ بمخزن خفيف لإزالة التكرار (dedup) اعتمادًا على المرجع أو مُعرّفات الحدث. أعد 2xx بسرعة بمجرد تسجيل الحدث، ثم عالج الآثار الجانبية بشكل غير متزامن. وللشفافية والتعامل مع الحوادث، احتفظ بسجل تدقيق (audit trail) يسجل وقت الاستلام وبيانات الحدث ونتائج التحقق دون تسجيل الأسرار.
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        'افحص `X-Callback-Event` (مثل `payment_status`) و `X-Callback-Signature`.',
        'تحقق من التوقيع كما هو موثّق لدى TriPay؛ ارفض الطلب عند عدم التطابق أو عند غياب الترويسة.',
        'اجعل المعالجة idempotent (إزالة التكرار حسب reference / event ID) وأرسل إقرارًا سريعًا (2xx).',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status' => 'PAID',
        'amount' => 125000,
        'currency' => 'IDR',
        'provider' => 'tripay',
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
            'path' => '/api/webhooks/tripay',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
