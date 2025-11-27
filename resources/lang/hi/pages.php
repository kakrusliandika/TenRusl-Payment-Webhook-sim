<?php

return [

    'back_to_providers' => 'प्रोवाइडर पर वापस जाएँ',
    'view_details'      => 'विवरण देखें',
    'breadcrumb'        => 'ब्रेडक्रंब',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => 'प्रोवाइडर खोजें...',
    'no_results'       => 'आपकी खोज से कोई प्रोवाइडर मेल नहीं खाता।',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => 'प्रोवाइडर',
    'provider_label'        => 'प्रोवाइडर',
    'provider_endpoints'    => 'एंडपॉइंट',
    'signature_notes_title' => 'सिग्नेचर नोट्स',
    'example_payload_title' => 'उदाहरण पेलोड',
    'view_docs'             => 'डॉक्युमेंटेशन देखें',

    'create_payment'  => 'पेमेंट बनाएँ (idempotent)',
    'get_payment'     => 'पेमेंट स्टेटस प्राप्त करें',
    'receive_webhook' => 'Webhook प्राप्त करें',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path'   => '/api/payments',
            'note'   => 'Idempotency-Key आवश्यक',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'note'   => 'पेमेंट स्टेटस प्राप्त करें',
        ],
        'receive_webhook' => [
            'method' => 'POST',
            'path'   => '/api/webhooks/{provider}',
            'note'   => 'mock/xendit/midtrans/…',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Errors / Empty states
    |--------------------------------------------------------------------------
    */
    'not_found'    => 'जो पेज आप ढूंढ रहे हैं वह नहीं मिला।',
    'server_error' => 'सर्वर पर एक अप्रत्याशित त्रुटि हुई। कृपया दोबारा प्रयास करें।',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} कोई परिणाम नहीं|{1} :count परिणाम|[2,*] :count परिणाम',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => 'सिम्युलेटेड पेमेंट प्रोवाइडर जिसमें साइन किए गए webhooks, idempotent फ्लो और लोकल तथा CI परीक्षण के लिए सुरक्षित payload शामिल हैं।',
];
