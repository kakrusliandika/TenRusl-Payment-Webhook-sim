<?php

return [

    'hint' => 'Callback-Token-Signatur.',

    'summary' => <<<'TEXT'
Xendit signiert Webhook-Events mit einem konto-spezifischen Token, das im Header `x-callback-token` enthalten ist. Deine Integration muss diesen Header mit dem Token vergleichen, den du im Xendit-Dashboard abrufst, und jede Anfrage mit fehlendem oder abweichendem Token ablehnen. Einige Webhook-Produkte liefern zusätzlich eine `webhook-id`, die du speichern kannst, um doppelte Verarbeitung bei Retries zu verhindern.

Betrieblich sollte die Verifizierung immer der erste Schritt sein: ein unveränderliches Event-Record persistieren, zügig mit 2xx bestätigen und schwere Arbeit in Queues auslagern. Erzwinge Idempotenz mit `webhook-id` (oder einem eigenen Schlüssel) und setze ein enges Zeitfenster, falls Timestamp-Metadaten vorhanden sind. Dokumentiere den gesamten Pfad (Verifizierung, Dedup, Retries und Fehlercodes), damit Team und Services konsistent über Umgebungen hinweg integrieren.
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        '`x-callback-token` mit deinem eindeutigen Token aus dem Xendit-Dashboard vergleichen; bei Mismatch ablehnen.',
        '`webhook-id` (falls vorhanden) zur Deduplication verwenden; Verifizierung als hartes Gate vor dem JSON-Parsing behandeln.',
        'Schnell 2xx zurückgeben und schwere Arbeit verzögern/auslagern; nur minimale Diagnosen loggen (keine Secrets).',
    ],

    'example_payload' => [
        'id' => 'evt_xnd_'.now()->timestamp,
        'event' => 'invoice.paid',
        'data' => [
            'id' => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
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
            'path' => '/api/webhooks/xendit',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
