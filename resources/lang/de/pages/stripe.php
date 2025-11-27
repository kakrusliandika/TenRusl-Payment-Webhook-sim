<?php

return [

    'hint' => 'Signatur-Header mit Zeitstempel.',

    'summary' => <<<'TEXT'
Stripe signiert jede Webhook-Anfrage und legt die berechnete Signatur im Header `Stripe-Signature` ab. Dein Endpoint muss die Anfrage verifizieren, bevor irgendeine Verarbeitung startet. Mit den offiziellen Stripe-Bibliotheken übergibst du drei Eingaben an die Verifizierungsroutine: den exakt unveränderten Raw-Request-Body, den `Stripe-Signature`-Header und dein Endpoint-Secret. Fahre nur fort, wenn die Verifizierung erfolgreich ist; andernfalls antworte mit einem Nicht-2xx und brich ab. Wenn du keine offizielle Bibliothek verwenden kannst, implementiere die manuelle Verifizierung wie dokumentiert, inklusive einer Zeitstempel-Toleranz, um Replay-Risiken zu verringern.

Behandle die Signaturprüfung als striktes Gate. Halte den Handler idempotent (Event-IDs speichern), antworte nach Persistenz schnell mit 2xx und verschiebe schwere Arbeit in Background-Jobs. Stelle sicher, dass dein Framework **die Raw-Bytes** liefert—vermeide JSON vor dem Hash neu zu serialisieren, denn Änderungen an Whitespace oder Reihenfolge brechen die Signaturprüfung. Logge abschließend nur minimale Diagnosen (Verifizierungsresultat, Event-Typ, Body-Hash—keine Secrets) und überwache Fehler bei Secret-Rotation oder Endpoint-Änderungen.
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        'Lies den `Stripe-Signature`-Header; hole das Endpoint-Secret aus dem Stripe-Dashboard.',
        'Verifiziere mit offiziellen Libraries durch Übergabe von: Raw-Body, `Stripe-Signature` und Endpoint-Secret.',
        'Bei manueller Verifizierung: Zeitstempel-Toleranz erzwingen (Replay-Schutz) und Signaturen timing-safe vergleichen.',
        'Nur bei Erfolg akzeptieren; Event-IDs für Idempotenz speichern und nach Persistenz schnell 2xx zurückgeben.',
    ],

    'example_payload' => [
        'id'   => 'evt_' . now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id'     => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider'   => 'stripe',
        'created_at' => now()->toIso8601String(),
    ],

    'endpoints' => [
        [
            'method' => 'POST',
            'path'   => '/api/payments',
            'desc'   => __('pages.create_payment'),
        ],
        [
            'method' => 'GET',
            'path'   => '/api/payments/{id}',
            'desc'   => __('pages.get_payment'),
        ],
        [
            'method' => 'POST',
            'path'   => '/api/webhooks/stripe',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
