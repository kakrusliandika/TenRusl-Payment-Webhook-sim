<?php

return [

    'back_to_providers' => 'Zurück zu den Providern',
    'view_details'      => 'Details anzeigen',
    'breadcrumb'        => 'Breadcrumb',

    /*
    |--------------------------------------------------------------------------
    | Providers Page / Search
    |--------------------------------------------------------------------------
    */
    'search_providers' => 'Provider suchen...',
    'no_results'       => 'Keine Provider entsprechen deiner Suche.',

    /*
    |--------------------------------------------------------------------------
    | Payments (cards & partials)
    |--------------------------------------------------------------------------
    */
    'provider_default_name' => 'Provider',
    'provider_label'        => 'Provider',
    'provider_endpoints'    => 'Endpoints',
    'signature_notes_title' => 'Hinweise zur Signatur',
    'example_payload_title' => 'Beispiel-Payload',
    'view_docs'             => 'Dokumentation ansehen',

    'create_payment'  => 'Zahlung erstellen (idempotent)',
    'get_payment'     => 'Zahlungsstatus abrufen',
    'receive_webhook' => 'Webhook empfangen',

    'endpoint_list' => [
        'create_payment' => [
            'method' => 'POST',
            'path'   => '/api/payments',
            'note'   => 'Idempotency-Key erforderlich',
        ],
        'get_payment' => [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'note'   => 'Zahlungsstatus abrufen',
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
    'not_found'    => 'Die gesuchte Seite konnte nicht gefunden werden.',
    'server_error' => 'Es ist ein unerwarteter Serverfehler aufgetreten. Bitte versuche es erneut.',

    /*
    |--------------------------------------------------------------------------
    | Pluralization examples used on pages
    |--------------------------------------------------------------------------
    | Use via: trans_choice('pages.counts.results', $count, ['count' => $count])
    */
    'counts' => [
        'results' => '{0} Keine Ergebnisse|{1} :count Ergebnis|[2,*] :count Ergebnisse',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback summary untuk provider yang belum punya file sendiri
    |--------------------------------------------------------------------------
    */
    'default_summary' => 'Simulierter Zahlungsprovider mit signierten Webhooks, idempotenten Abläufen und sicheren Payloads für lokale Tests und CI-Umgebungen.',
];
