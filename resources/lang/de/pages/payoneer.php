<?php

return [

    'hint' => 'Produktspezifische Benachrichtigungen.',

    'summary' => <<<'TEXT'
Payoneer Checkout liefert asynchrone Benachrichtigungen (Webhooks) an einen von dir kontrollierten Endpoint, damit dein System Zahlungszustände sicher außerhalb des Browsers des Nutzers abgleichen kann. Die Plattform erlaubt dir, eine dedizierte Notification-URL zu definieren und den Zustellstil passend zu deinem Stack zu wählen—POST (empfohlen) oder GET, mit JSON oder formularcodierten Parametern. Da Parameterumfang sowie Signing-/Auth-Muster produktspezifisch sind, betrachte Payoneer-Benachrichtigungen als Integrationsfläche: Dokumentiere die Header/Felder, die das Event identifizieren, nutze Anti-Replay-Metadaten, wo verfügbar, und verifiziere die Authentizität, bevor du Zustände änderst.

Operativ solltest du mit einem schmalen, idempotenten Handler beginnen, der ein unveränderliches Event-Record persistiert und schnell 2xx zurückgibt. Schwere Business-Logik gehört in Background-Worker, um Retry-Stürme zu vermeiden. Verwende Dedup-Keys und erzwinge ein Freshness-Window für Timestamp/Nonce, um Replays oder Out-of-Order-Zustellung abzusichern. Für zusätzliche Sicherheit kannst du einen vom Provider ausgegebenen Token (oder dein eigenes Random-Secret) an die Notification-URL anhängen und serverseitig prüfen. Abschließend: Erstelle ein Runbook für das Team (Endpoints, Formate, Fehlercodes, genaue Verifikationsschritte für deine Payoneer-Produktvariante) und versioniere es zusammen mit dem Code.
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        'Stelle einen dedizierten Notification-Endpoint bereit (POST empfohlen); akzeptiere JSON oder Form-Daten.',
        'Validiere die Authentizität wie für deine Produktvariante dokumentiert (Token oder Signaturfelder); bei Abweichung ablehnen.',
        'Erzwinge Timestamp/Nonce-Freshness, wenn verfügbar, und gestalte die Verarbeitung idempotent (Dedup-Key speichern).',
        'Schnell ACK (2xx) und schwere Arbeit in Background-Jobs auslagern; Audit-Trail ohne Secrets-Logging führen.',
    ],

    'example_payload' => [
        'event' => 'checkout.transaction.completed',
        'provider' => 'payoneer',
        'data' => [
            'orderId' => 'PO-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'COMPLETED',
        ],
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
            'path' => '/api/webhooks/payoneer',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
