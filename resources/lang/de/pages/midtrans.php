<?php

return [

    'hint' => 'signature_key-Prüfung.',

    'summary' => <<<'TEXT'
Midtrans liefert in jeder HTTP(S)-Benachrichtigung einen berechneten `signature_key`, damit du die Herkunft verifizieren kannst, bevor du etwas verarbeitest. Die Formel ist explizit und stabil:
    SHA512(order_id + status_code + gross_amount + ServerKey)
Baue den Input-String aus den exakten Werten aus dem Notification-Body (als Strings) und deinem privaten `ServerKey`, berechne dann den SHA-512 Hex-Digest und vergleiche ihn per constant-time Vergleich mit `signature_key`. Wenn die Verifizierung fehlschlägt, verwerfe die Benachrichtigung. Bei echten Nachrichten nutze die dokumentierten Felder (z. B. `transaction_status`) für deine Zustandsmaschine—schnell bestätigen (2xx), schwere Arbeit in eine Queue schieben und Updates idempotent halten, falls Retries oder Out-of-Order-Zustellungen auftreten.

Zwei typische Fallstricke: Formatierung und Typ-Konvertierung. Behalte `gross_amount` exakt so bei, wie es geliefert wird (nicht lokalisieren, Dezimalstellen nicht ändern) und vermeide Trimmen oder Änderungen bei Newlines/Whitespace. Speichere pro Event oder pro Order einen Deduplication-Key, um Race Conditions abzufangen; logge das Verifikationsergebnis und einen Body-Hash zur Auditierbarkeit, ohne Secrets zu leaken. Kombiniere das mit Rate Limiting am Endpoint und klaren Fehlercodes, damit Monitoring zwischen temporären Fehlern (retryfähig) und permanenten Ablehnungen (ungültige Signatur) unterscheiden kann.
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'Nimm `order_id`, `status_code`, `gross_amount` aus dem Body (als Strings) und hänge deinen `ServerKey` an.',
        'Berechne `SHA512(order_id + status_code + gross_amount + ServerKey)` und vergleiche mit `signature_key` (constant-time).',
        'Bei Mismatch ablehnen; sonst State anhand von `transaction_status` updaten. Verarbeitung idempotent halten & zügig 2xx zurückgeben.',
        'Achte auf Formatänderungen bei `gross_amount` und auf versehentlichen Whitespace beim Konkatenieren.',
    ],

    'example_payload' => [
        'order_id' => 'ORDER-001',
        'status_code' => '200',
        'gross_amount' => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key' => '<sha512>',
        'provider' => 'midtrans',
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
            'path' => '/api/webhooks/midtrans',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
