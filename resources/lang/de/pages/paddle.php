<?php

return [

    'hint' => 'Public-Key-Signatur (Classic) / Secret (Billing).',

    'summary' => <<<'TEXT'
Paddle Billing signiert jeden Webhook mit einem `Paddle-Signature`-Header, der einen Unix-Timestamp (`ts`) und eine Signatur (`h1`) enthält. Für eine manuelle Verifizierung konkatenierst du den Timestamp, einen Doppelpunkt und den exakten Raw-Request-Body zum „signed payload“; anschließend berechnest du daraus mit dem Secret deiner Notification-Destination den Hash und vergleichst ihn mit `h1` per constant-time (timing-safe) Vergleich. Paddle erzeugt ein eigenes Secret pro Notification-Destination—behandle es wie ein Passwort und halte es außerhalb des Source Controls.

Nutze bevorzugt die offiziellen SDKs oder eigenes Middleware-Handling, um vor jedem Parsing zu verifizieren. Da Timing und Body-Transformationen häufige Stolpersteine sind, stelle sicher, dass dein Framework die Raw-Bytes bereitstellt (z. B. Express `express.raw({ type: 'application/json' })`) und erzwinge eine kurze Toleranz für `ts`, um Replays zu verhindern. Nach erfolgreicher Verifizierung: schnell ack’n (2xx), Event-ID für Idempotenz speichern und schwere Arbeit in Background-Jobs auslagern. Das hält die Zustellung zuverlässig und verhindert doppelte Side Effects bei Retries.

Beim Umstieg von Paddle Classic gilt: Die Verifizierung ist von Public-Key-Signaturen auf secret-basiertes HMAC für Billing gewechselt. Aktualisiere Runbooks und Secrets-Management entsprechend und überwache Verifizierungsmetriken beim Rollout. Klare Logs (ohne Secrets) und deterministische Fehlerantworten vereinfachen Incident-Handling und Partner-Support erheblich.
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        'Lies den `Paddle-Signature`-Header; parse die Werte `ts` und `h1`.',
        'Baue das signed payload = `ts + ":" + <raw request body>`; hashe mit dem Secret deines Endpoint.',
        'Vergleiche deinen Hash mit `h1` per timing-safe Funktion; erzwinge eine kurze Toleranz für `ts`, um Replay zu verhindern.',
        'Bevorzuge offizielle SDKs oder Verifizierungs-Middleware; JSON erst nach erfolgreicher Verifizierung parsen.',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
