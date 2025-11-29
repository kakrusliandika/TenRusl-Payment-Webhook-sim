<?php

return [

    'back_to_providers' => 'Voltar para provedores',
    'view_details' => 'Ver detalhes',
    'breadcrumb' => 'Trilha de navegação',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => 'Buscar provedores...',
    'no_results' => 'Nenhum provedor corresponde à sua pesquisa.',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => 'Provedor',
    'provider_label' => 'Provedor',
    'provider_endpoints' => 'Endpoints',
    'signature_notes_title' => 'Notas de assinatura',
    'example_payload_title' => 'Payload de exemplo',
    'view_docs' => 'Ver documentação',

    'create_payment' => 'Criar pagamento (idempotente)',
    'get_payment' => 'Obter status do pagamento',
    'receive_webhook' => 'Receber webhook',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path' => '/api/payments',
            'note' => 'Idempotency-Key obrigatório',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path' => '/api/payments/{id}',
            'note' => 'Buscar status do pagamento',
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
    'not_found' => 'A página que você está procurando não foi encontrada.',
    'server_error' => 'Ocorreu um erro inesperado no servidor. Tente novamente.',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} Nenhum resultado|{1} :count resultado|[2,*] :count resultados',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => 'Provedor de pagamento simulado com webhooks assinados, fluxos idempotentes e payloads seguros para testes locais e em pipelines de CI.',
];
