<?php

return [

    'back_to_providers' => 'Back to providers',
    'view_details' => 'View details',
    'breadcrumb' => 'Breadcrumb',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => 'Search providers...',
    'no_results' => 'No providers match your search.',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => 'Provider',
    'provider_label' => 'Provider',
    'provider_endpoints' => 'Endpoints',
    'signature_notes_title' => 'Signature Notes',
    'example_payload_title' => 'Example Payload',
    'view_docs' => 'View docs',

    'create_payment' => 'Create payment (idempotent)',
    'get_payment' => 'Get payment status',
    'receive_webhook' => 'Receive webhook',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path' => '/api/payments',
            'note' => 'Idempotency-Key required',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path' => '/api/payments/{id}',
            'note' => 'Fetch payment status',
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
    'not_found' => 'The page you are looking for could not be found.',
    'server_error' => 'An unexpected server error occurred. Please try again.',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} No results|{1} :count result|[2,*] :count results',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => 'Simulated payment provider with signed webhooks, idempotent flows, and safe payloads for local and CI testing.',
];
