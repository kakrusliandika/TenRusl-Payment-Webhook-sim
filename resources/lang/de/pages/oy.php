<?php

return [

    'hint' => 'Provider-spezifische Callback-Signatur.',

    'summary' => <<<'TEXT'
OY!-Callbacks sind Teil einer umfassenderen Sicherheitsstrategie, die auf registrierten API-Keys und Source-IP-Allowlisting für Partner-Requests basiert. Zusätzlich bietet OY! die Funktion Authorization Callback, mit der du Callbacks steuern und vorab freigeben kannst, bevor sie dein System erreichen — ein explizites Gate, um unbeabsichtigte Statusänderungen zu verhindern. In der Praxis solltest du dennoch jeden eingehenden Callback als untrusted behandeln, bis er verifiziert ist, Freshness (Timestamp/Nonce-Fenster) erzwingen und den Consumer idempotent gestalten, damit Retries und Out-of-Order-Zustellung sicher bleiben.

Da öffentliche Provider sich in der Signierung von Callbacks unterscheiden, demonstriert unser Simulator eine gehärtete Baseline mit einem HMAC-Header (z. B. `X-Callback-Signature`), berechnet über den exakten Raw-Request-Body mit einem Shared Secret. Das zeigt dieselben Prinzipien wie in Production: Raw-Byte-Hashing (keine Re-Serialisierung), constant-time Vergleich und kurze Replay-Windows. Kombiniere das mit einem kleinen Dedup-Store und schnellen 2xx-Acknowledgements, damit Provider-Retry-Logik gesund bleibt und du doppelte Side Effects vermeidest.

Operational solltest du einen Audit-Trail führen (Empfangszeit, Verifikationsstatus, Body-Hash — nicht das Secret), Secrets sicher rotieren und Verifikations-Fail-Raten überwachen. Wenn du Allowlists nutzt, denke daran, dass sie sich ändern können; eine kryptografische Prüfung (oder OY’s explizites Authorization-Gate) sollte der primäre Trust Anchor bleiben. Halte den Endpoint schlank, vorhersehbar und gut dokumentiert, damit andere Services und Teammitglieder ihn zuverlässig wiederverwenden können.
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'Nutze OY!-Security-Posture: registrierter API-Key + Source-IP-Allowlisting für Partner-Requests.',
        'Nutze Authorization Callback (Dashboard), um Callbacks zu genehmigen, bevor sie dein System erreichen.',
        'Im Simulator verifizieren wir `X-Callback-Signature = HMAC-SHA256(raw_body, secret)` als Best-Practice-Modell; constant-time Compare & Freshness-Checks anwenden.',
        'Verarbeitung idempotent machen und 2xx zügig zurückgeben, damit Provider-Retries stabil bleiben.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.completed',
        'provider' => 'oy',
        'data' => [
            'partner_trx_id' => 'PRT-001',
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
            'path' => '/api/webhooks/oy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
