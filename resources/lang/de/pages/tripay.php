<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay sendet Callbacks an die von dir konfigurierte URL und liefert Header mit, die das Event identifizieren und dir helfen, den Absender zu authentifizieren. Insbesondere enthalten Callbacks `X-Callback-Event` mit einem Wert wie `payment_status` sowie `X-Callback-Signature` zur Signaturprüfung gemäß der TriPay-Dokumentation. Dein Consumer sollte diese Header auslesen, die Authentizität entsprechend verifizieren und erst danach den internen Status aktualisieren.

Gestalte den Endpoint schnell und idempotent. Verwende ein kurzes Freshness-Fenster, wenn Timestamps/Nonces vorhanden sind, und halte einen schlanken Dedup-Store, der über Reference- oder Event-IDs arbeitet. Gib nach dem Erfassen des Events zügig 2xx zurück und verarbeite Side-Effects asynchron. Für Transparenz und Incident-Handling solltest du außerdem einen Audit-Trail führen (Empfangszeit, Event-Metadaten, Verifizierungsergebnis), ohne Secrets zu loggen.
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        '`X-Callback-Event` (z. B. `payment_status`) und `X-Callback-Signature` prüfen.',
        'Signatur gemäß TriPay-Dokumentation validieren; bei Mismatch oder fehlendem Header ablehnen.',
        'Verarbeitung idempotent halten (Dedup nach reference / event ID) und schnell bestätigen (2xx).',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status' => 'PAID',
        'amount' => 125000,
        'currency' => 'IDR',
        'provider' => 'tripay',
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
            'path' => '/api/webhooks/tripay',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
