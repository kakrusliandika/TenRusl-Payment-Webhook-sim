<?php

return [

    'back_to_providers' => 'Volver a proveedores',
    'view_details' => 'Ver detalles',
    'breadcrumb' => 'Miga de pan',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => 'Buscar proveedores...',
    'no_results' => 'Ningún proveedor coincide con tu búsqueda.',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => 'Proveedor',
    'provider_label' => 'Proveedor',
    'provider_endpoints' => 'Endpoints',
    'signature_notes_title' => 'Notas de firma',
    'example_payload_title' => 'Payload de ejemplo',
    'view_docs' => 'Ver documentación',

    'create_payment' => 'Crear pago (idempotente)',
    'get_payment' => 'Obtener estado del pago',
    'receive_webhook' => 'Recibir webhook',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path' => '/api/payments',
            'note' => 'Requiere Idempotency-Key',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path' => '/api/payments/{id}',
            'note' => 'Obtener estado del pago',
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
    'not_found' => 'No se pudo encontrar la página que estás buscando.',
    'server_error' => 'Ocurrió un error inesperado en el servidor. Inténtalo de nuevo.',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} Sin resultados|{1} :count resultado|[2,*] :count resultados',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => 'Proveedor de pago simulado con webhooks firmados, flujos idempotentes y payloads seguros para pruebas locales y en CI.',
];
