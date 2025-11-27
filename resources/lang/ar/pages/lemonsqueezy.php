<?php

return [

    'hint' => 'ترويسة توقيع HMAC.',

    'summary' => <<<'TEXT'
يقوم Lemon Squeezy بتوقيع كل webhook باستخدام HMAC بسيط على **جسم الطلب الخام (raw request body)**. يستخدم المُرسل “signing secret” الخاص بالويبهوك لإنتاج HMAC بـ SHA-256 على شكل **hex digest**؛ ثم يُرسل ذلك الـ digest داخل ترويسة `X-Signature`. مهمتك هي قراءة بايتات الجسم كما وصلت تماماً (من دون إعادة stringify أو تغيير مسافات/تنسيق)، ثم حساب HMAC نفسه باستخدام السر، وإخراجه كسلسلة **hex**، ومقارنته مع `X-Signature` باستخدام دالة مقارنة بزمن ثابت (constant-time). إذا اختلفت القيم — أو كانت الترويسة مفقودة — ارفض الطلب قبل تنفيذ أي منطق أعمال.

وبما أن إعدادات الأطر (frameworks) غالباً ما تقوم بقراءة/تحليل الجسم قبل أن تتمكن من هَشّه، تأكد أن المسار يمنحك الوصول إلى البايتات الخام (مثلاً تفعيل “raw body” في Node/Express). اعتبر التحقق بوابة: بعد نجاحه فقط قم بتحليل JSON وتحديث الحالة. اجعل المعالج idempotent حتى لا تؤدي عمليات retry أو التكرار إلى تطبيق الآثار الجانبية مرتين، واحتفظ بتشخيصات بسيطة (طول الترويسة المستلمة، نتيجة التحقق، معرّف الحدث) بدلاً من تسجيل الأسرار. للاختبار محلياً، استخدم أحداث الاختبار في Lemon Squeezy وحاكي الإخفاقات للتأكد من سلوك retry/backoff. وثّق المسار كاملاً — التحقق، إزالة التكرار، والمعالجة غير المتزامنة — حتى يتمكن الفريق من إعادة إنتاج نتائج متسقة عبر البيئات.
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        'اقرأ `X-Signature` (‏HMAC-SHA256 بصيغة **hex** على الجسم الخام) واحصل على بايتات الطلب الخام.',
        'احسب HMAC بصيغة hex باستخدام signing secret وقارنه بدالة آمنة زمنياً (timing-safe).',
        'ارفض عند عدم التطابق/غياب الترويسة؛ ولا تقم بتحليل JSON إلا بعد نجاح التحقق.',
        'تأكد أن الإطار يوفّر raw body (من دون إعادة تسلسل)؛ واجعل المعالج idempotent وسجّل أقل قدر من التشخيصات.',
    ],

    'example_payload' => [
        'meta'     => ['event_name' => 'order_created'],
        'data'     => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path'   => '/api/webhooks/lemonsqueezy',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
