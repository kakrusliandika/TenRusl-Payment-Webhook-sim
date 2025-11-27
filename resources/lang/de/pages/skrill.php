<?php

return [

    'hint' => 'Callback-Signatur im MD5/HMAC-Stil.',

    'summary' => <<<'TEXT'
Skrill sendet einen Status-Callback an deine `status_url` und erwartet, dass du die Nachricht über `md5sig` validierst—eine **MD5-Signatur in GROSSBUCHSTABEN** aus einer klar definierten Konkatenation von Feldern (z. B. `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`). Nur wenn dein berechneter Wert mit dem eingehenden `md5sig` übereinstimmt, solltest du dem Payload vertrauen. Skrill unterstützt auf Anfrage außerdem ein alternatives `sha2sig` (SHA-2 in Großbuchstaben), das analog zu `md5sig` aufgebaut wird.

In der Praxis bleibt die Signaturprüfung im Backend (Secret Word nie exponieren) und du hashst die **exakt** zurückgesendeten Parameterwerte. Gestalte den Endpoint idempotent (Dedup nach Transaction- oder Event-ID), gib nach Persistenz schnell 2xx zurück und verschiebe Unkritisches. Beim Debugging logge Verifikationsresultate und einen Body-Hash, aber niemals Secrets. Achte auf Formatierung—Betrag und Währung müssen beim Aufbau der Signaturzeichenkette unverändert übernommen werden—damit Vergleiche über Retries und Umgebungen stabil bleiben.
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        '`md5sig` exakt nachbauen: dokumentierte Felder (z. B. merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) konkatenieren und **UPPERCASE MD5** berechnen.',
        'Mit dem empfangenen `md5sig` vergleichen; optional `sha2sig` (UPPERCASE SHA-2) verwenden, falls von Skrill aktiviert.',
        'Validierung nur serverseitig mit den exakt geposteten Werten; Handler idempotent halten & schnell 2xx zurückgeben.',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount'      => '10.00',
        'mb_currency'    => 'EUR',
        'status'         => '2',
        'md5sig'         => '<UPPERCASE_MD5>',
        'provider'       => 'skrill',
        'sent_at'        => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/skrill',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
