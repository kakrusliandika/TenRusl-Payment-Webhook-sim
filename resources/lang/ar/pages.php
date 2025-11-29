<?php

return [

    'back_to_providers' => 'الرجوع إلى المزودين',
    'view_details' => 'عرض التفاصيل',
    'breadcrumb' => 'مسار التنقل',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => 'ابحث عن المزودين...',
    'no_results' => 'لا يوجد أي مزود يطابق عملية البحث.',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => 'المزود',
    'provider_label' => 'المزود',
    'provider_endpoints' => 'نقاط النهاية',
    'signature_notes_title' => 'ملاحظات التوقيع',
    'example_payload_title' => 'مثال على الحمولة',
    'view_docs' => 'عرض المستندات',

    'create_payment' => 'إنشاء دفعة (idempotent)',
    'get_payment' => 'جلب حالة الدفعة',
    'receive_webhook' => 'استقبال Webhook',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path' => '/api/payments',
            'note' => 'يتطلب Idempotency-Key',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path' => '/api/payments/{id}',
            'note' => 'جلب حالة الدفعة',
        ],
        'receive_webhook' => [
            'method' => 'POST',
            'path' => '/api/webhooks/{provider}',
            'note' => 'mock/xendit/midtrans/…',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Errors / Empty states
    |--------------------------------------------------------------------------
    */
    'not_found' => 'الصفحة التي تبحث عنها غير موجودة.',
    'server_error' => 'حدث خطأ غير متوقع في الخادم. يرجى المحاولة مرة أخرى.',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} لا توجد نتائج|{1} نتيجة واحدة|[2,*] :count نتائج',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => 'مزود دفع مُحاكى مع Webhook موقّعة وتدفقات idempotent وpayload آمنة لاختبار البيئة المحلية وبيئات CI.',
];
