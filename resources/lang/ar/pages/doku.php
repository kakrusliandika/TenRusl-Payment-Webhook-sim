<?php

return [

    'hint' => 'توقيع باستخدام ترويسات Client-Id/Request-*.',

    'summary' => <<<'TEXT'
تؤمّن DOKU إشعارات HTTP عبر توقيع قانوني (canonical) يعتمد على الترويسات، ويجب عليك التحقق منه قبل تنفيذ أي إجراء على الـ payload. يصل كل نداء (callback) ومعه ترويسة `Signature` بصيغة `HMACSHA256=<base64>`. لإعادة بناء القيمة المتوقعة، احسب أولاً قيمة `Digest` لجسم الطلب: وهي SHA-256 لبايتات JSON الخام ثم تُحوَّل إلى base64. بعد ذلك، أنشئ سلسلة مفصولة بأسطر (newline-delimited) مكوّنة من خمس مكوّنات بالترتيب والتهجئة التالية تماماً:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (مثال: `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
ثم احسب HMAC باستخدام SHA-256 مع مفتاح DOKU Secret Key كمفتاح التوقيع على تلك السلسلة القانونية، ثم حوّل الناتج إلى base64 وأضف قبله `HMACSHA256=`. وأخيراً قارِن الناتج مع ترويسة `Signature` باستخدام مقارنة بزمن ثابت (constant-time). أي عدم تطابق، أو نقص في أي مكوّن، أو قيمة غير صحيحة يجب اعتباره فشلاً في المصادقة ويجب رفض الطلب فوراً.

للمتانة والأمان، أقرّ الإشعارات الصحيحة سريعاً (2xx) وادفع الأعمال الثقيلة إلى مهام خلفية حتى لا تتسبب في إعادة المحاولة. اجعل المستهلك idempotent عبر تسجيل المعرّفات التي تمّت معالجتها (مثل `Request-Id` أو معرّف حدث داخل الجسم). تحقّق من الحداثة: يجب أن يقع `Request-Timestamp` ضمن نافذة ضيقة لمنع هجمات replay؛ وتأكد أيضاً أن `Request-Target` يطابق مسارك الفعلي لتفادي أخطاء canonicalization. أثناء التحليل، اتبع إرشادات DOKU لتكون غير صارم: تجاهل الحقول غير المعروفة وفضّل تطوّر المخطط (schema evolution) على المحللات الهشّة. أثناء الاستجابة للحوادث، سجّل وجود الترويسات المطلوبة، والقيم المحسوبة للـ digest/signature (من دون تسجيل السر أبداً)، وهاش للجسم للمساعدة في التدقيق دون تسريب بيانات حساسة.
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'اقرأ الترويسات: `Client-Id` و`Request-Id` و`Request-Timestamp` و`Signature`، واستنتج `Request-Target` (مسار الراوت لديك).',
        'احسب `Digest = base64( SHA256(raw JSON body) )`.',
        'ابنِ السلسلة القانونية على أسطر: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (بهذا الترتيب، كل عنصر في سطر مستقل، دون سطر جديد في النهاية).',
        'احسب expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`؛ وقارنه مع `Signature` بمقارنة زمن ثابت.',
        'طبّق حداثة الـ timestamp؛ واجعل المعالجة idempotent؛ وأقرّ بسرعة (2xx) وانقل الأعمال الثقيلة للخلفية.',
    ],

    'example_payload' => [
        'order'       => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider'    => 'doku',
        'sent_at'     => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/doku',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
