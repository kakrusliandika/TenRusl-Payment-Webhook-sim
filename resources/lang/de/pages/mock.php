<?php

return [

    'hint' => 'Schnelles Testen.',

    'summary' => <<<'TEXT'
Dieser Mock-Provider ist ein deterministischer, credential-freier Playground, um den gesamten Webhook-Lebenszyklus zu üben: Request-Erstellung, idempotente Statusübergänge, Zustellung, Verifizierung, Retries und Fehlerbehandlung. Da er ohne externe Abhängigkeiten läuft, kannst du lokal oder in CI iterieren, Fixtures aufzeichnen und Architekturentscheidungen (z. B. wo Verifizierung vs. Persistenz stattfindet) demonstrieren, ohne echte Secrets zu leaken.

Nutze ihn, um typische Fehlerbilder zu simulieren: verzögerte Zustellungen, doppelte Sendungen, Out-of-Order-Events und temporäre 5xx-Antworten, die exponentielles Backoff auslösen. Der Mock unterstützt außerdem verschiedene „Signatur-Modi“ (none / HMAC-SHA256 / RSA-verify stub), damit Teammitglieder Raw-Body-Hashing, constant-time Vergleich und Timestamp-Windows sicher üben können. So validierst du Idempotency-Keys und Dedup-Tabellen, bevor du einen echten Gateway integrierst.

Für hochwertige Doku halte den Mock nah an Production: gleiche Endpoint-Formen, Header und Error Codes; der einzige Unterschied ist die Trust-Root. Bestätige gültige Webhooks schnell (2xx) und verlagere schwere Arbeit in Background-Jobs. Behandle den Mock-Payload als untrusted, bis die Verifizierung bestanden ist—danach wende deine Business-Regeln an. Ergebnis: schneller Feedback-Loop und eine portable Demo, die die Architektur widerspiegelt, die du später auslieferst.
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'Simulator-Modi: none / HMAC-SHA256 / RSA-verify stub; per Config wählen, um Verifizierungs-Pfade zu üben.',
        'Exakten raw Request Body hashen; timing-safe vergleichen; kurze Replay-Windows erzwingen.',
        'Verarbeitete Event-IDs für Idempotenz protokollieren; gültige Webhooks schnell ACKen (2xx) und schwere Arbeit verschieben.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.succeeded',
        'provider' => 'mock',
        'data' => [
            'payment_id' => 'pay_mock_001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'succeeded',
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
            'path' => '/api/webhooks/mock',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
