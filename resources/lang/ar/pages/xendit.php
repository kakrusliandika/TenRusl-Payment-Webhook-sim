<?php

return [

    'hint' => 'توقيع توكن الاستدعاء (Callback).',

    'summary' => <<<'TEXT'
تقوم Xendit بتأمين أحداث الـ webhook باستخدام توكن خاص بكل حساب يتم إرساله في ترويسة `x-callback-token`. يجب أن تقارن تكاملك قيمة هذه الترويسة بالتوكن الذي حصلت عليه من لوحة تحكم Xendit، وترفض أي طلب يفتقد الترويسة أو يحتوي توكنًا غير مطابق. بعض منتجات الـ webhook تتضمن أيضًا `webhook-id` يمكنك تخزينه لمنع المعالجة المكررة عند حدوث إعادة إرسال (retries).

تشغيليًا، اجعل التحقق هو الخطوة الأولى دائمًا، ثم قم بحفظ سجل حدث غير قابل للتغيير، وردّ بـ 2xx بسرعة، وانقل العمل الثقيل إلى طوابير/مهام خلفية. طبّق idempotency باستخدام `webhook-id` (أو مفتاحك الخاص) وفرض نافذة زمنية ضيقة إذا توفرت بيانات timestamp. وثّق المسار كاملًا (التحقق، إزالة التكرار، إعادة المحاولة، وأكواد الأخطاء) حتى يتكامل الفريق والخدمات بشكل متّسق عبر البيئات.
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        'قارن `x-callback-token` مع توكنك الفريد من لوحة تحكم Xendit؛ ارفض عند عدم التطابق.',
        'استخدم `webhook-id` (إن وُجد) لإزالة التكرار؛ واعتبر التحقق بوابة صارمة قبل تحليل JSON.',
        'أعد 2xx بسرعة وأجّل العمل الثقيل؛ وسجّل تشخيصًا بسيطًا دون كشف الأسرار.',
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
