<?php

return [

    'hint' => 'Verify Webhook Signature API.',

    'summary' => <<<'TEXT'
PayPal verlangt die serverseitige Verifizierung jedes Webhooks über die offizielle Verify Webhook Signature API. Dein Listener muss die mitgesendeten Header auslesen—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL` und `PAYPAL-TRANSMISSION-SIG`—zusammen mit deiner `webhook_id` sowie dem **rohen** Request-Body (`webhook_event`). Sende diese Werte an den Verifizierungs-Endpunkt und akzeptiere das Event nur, wenn PayPal einen Erfolgsstatus zurückgibt. Das ersetzt ältere Verifikationsmechanismen und vereinfacht die Konsistenz über REST-Produkte hinweg.

Baue den Consumer als schnelles, idempotentes Gate: zuerst verifizieren, dann einen Event-Record persistieren, mit 2xx bestätigen und schwere Arbeit in eine Queue auslagern. Nutze constant-time Vergleiche für lokale Checks und gib die Raw-Bytes beim Weiterleiten an PayPal unverändert weiter, um subtile Re-Serialization-Bugs zu vermeiden. Erzwinge eine enge Zeit-Toleranz um `PAYPAL-TRANSMISSION-TIME`, um Replay-Fenster zu reduzieren, und logge nur minimale Audit-Daten (Request-ID, Verifizierungsresultat, Body-Hash—keine Secrets). So führen doppelte Zustellungen und Teilausfälle nicht zu doppelter Verarbeitung, und dein Audit-Trail bleibt bei Incidents vertrauenswürdig.
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'Sammle Header: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; Raw-Body unverändert behalten.',
        'Rufe die Verify Webhook Signature API mit diesen Werten plus webhook_id und webhook_event auf; nur bei Erfolg akzeptieren.',
        'Verifizierung als Gate behandeln; kurze Zeit-Toleranz erzwingen, um Replays zu mindern, und den Consumer idempotent machen.',
        'Schnell 2xx zurückgeben, schwere Arbeit queuen und nur minimale Diagnosen loggen (keine Secrets).',
    ],

    'example_payload' => [
        'id' => 'WH-'.now()->timestamp,
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'resource' => [
            'id' => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider' => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paypal',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
