<?php

return [

    'hint' => 'RSA (Verifizierung mit dem öffentlichen DANA-Schlüssel).',

    'summary' => <<<'TEXT'
DANA verwendet ein **asymmetrisches** Signaturverfahren: Anfragen werden mit einem privaten Schlüssel signiert, und Integratoren verifizieren sie mit dem offiziellen **öffentlichen DANA-Schlüssel**. In der Praxis liest du die Signatur aus dem Webhook-Header (z. B. `X-SIGNATURE`), decodierst sie aus Base64 und verifizierst anschließend den rohen HTTP-Request-Body gegen diese Signatur mit RSA-2048 und SHA-256. Erst wenn die Verifizierung positiv ausfällt, sollte das Payload als authentisch gelten. Schlägt die Verifizierung fehl – oder fehlt die Signatur/der Header – antworte mit einem Non-2xx-Statuscode und brich die Verarbeitung ab.

Da Webhooks erneut zugestellt werden oder in anderer Reihenfolge eintreffen können, sollte dein Handler idempotent sein: Speichere eine eindeutige Event-ID und überspringe Duplikate; prüfe Timestamp/Nonce auf Frische, um Replays zu verhindern; und behandle alle Felder als nicht vertrauenswürdig, bis die Signaturprüfung erfolgreich war. Vermeide eine erneute Serialisierung von JSON vor der Verifizierung; hashe exakt die Bytes, die über die Leitung angekommen sind. Halte Secrets und private Schlüssel aus Logs heraus; wenn du loggen musst, erfasse nur grobe Diagnosen (Verifizierungsergebnis, Hash des Bodys, Event-ID) und schütze diese Logs im Ruhezustand.

Für Teams: Veröffentliche ein kurzes Runbook, das abdeckt, wie der öffentliche DANA-Schlüssel geladen oder rotiert wird, wie die Verifizierung in jeder Sprache/Laufzeitumgebung umgesetzt wird, welche genauen String-to-Sign-Regeln für eure Integration gelten und was ein permanenter vs. transienter Fehler ist. Ergänze das mit einer robusten Retry-/Backoff-Policy, begrenzten Work-Queues, Health Checks und Alerts bei Verifizierungsausfällen. Das Ergebnis ist ein Webhook-Consumer, der unter Last sicher ist, Retries gut verkraftet und die von DANA geforderte kryptografische Verifikation konsequent umsetzt.
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        'Base64-decode den Wert aus dem Header `X-SIGNATURE`.',
        'Verifiziere RSA-2048 + SHA-256 über dem exakten rohen HTTP-Body mit dem offiziellen öffentlichen DANA-Schlüssel; akzeptiere nur bei positiver Verifizierung.',
        'Lehne jeden Webhook mit fehlender/ungültiger Signatur oder fehlerhaftem Payload ab; vertraue keinen Daten vor erfolgreicher Verifizierung.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.paid',
        'provider' => 'dana',
        'data'     => [
            'transaction_id' => 'DANA-001',
            'amount'         => 25000,
            'currency'       => 'IDR',
            'status'         => 'SUCCESS',
        ],
        'sent_at'  => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/dana',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
