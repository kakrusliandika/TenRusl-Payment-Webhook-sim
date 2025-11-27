<?php

return [

    'hint' => 'x-amzn-signature bei jeder Anfrage.',

    'summary' => <<<'TEXT'
Buy with Prime (BWP) signiert jeden Webhook, damit du sicherstellen kannst, dass er tatsächlich von Amazon stammt und unterwegs nicht verändert wurde. Jede Anfrage enthält die digitale Signatur im Header `x-amzn-signature`. Dein Handler muss die erwartete Signatur exakt so rekonstruieren, wie es von BWP für den jeweiligen Ereignistyp und die jeweilige Umgebung dokumentiert ist; stimmen die Werte nicht überein, lehne den Aufruf ab. Behandle jeden Timestamp/Jede Nonce, der/die die Anfrage begleitet, als Teil deiner Anti-Replay-Strategie, erzwinge ein enges Gültigkeitsfenster und speichere bereits verarbeitete Kennungen, um Duplikate zu vermeiden.

Operativ sollte der Endpoint schnell und deterministisch sein: zuerst verifizieren, dann nach sicherer Speicherung mit einem `2xx`-Status quittieren und die schwersten Arbeiten asynchron ausführen. Wenn du Allowlists verwendest, denke daran, dass IPs und Netze sich ändern können—kryptografische Verifikation ist der wichtigste Vertrauensanker. Halte eine sichere Audit-Logkette vor (Request-ID, Vorhandensein der Signatur, Verifikationsergebnis und ein Hash des Bodys—nicht des Secrets). Für lokale Tests kannst du den Verifikationsschritt hinter einem Umgebungs-Flag stubben und gleichzeitig sicherstellen, dass Produktionspfade die Signaturen immer prüfen. Beim Rotieren von Schlüsseln oder Anpassen der Kanonisierung geh vorsichtig vor, überwache Fehlerraten und dokumentiere den exakten Headersatz sowie die von dir implementierte Hashing-/Kanonisierungslogik, damit andere Services im Stack im Gleichschritt bleiben.

Aus Integrationssicht solltest du **klare Fehlerursachen** bereitstellen (ungültige Signatur, veralteter Timestamp, fehlerhafte Anfrage) und stabile Fehlercodes zurückgeben, damit sich Retries vorhersagbar verhalten. Kombiniere dies mit Idempotenz auf Anwendungsebene und Replay-Schutz, damit nachgelagerte Zustandsübergänge für Zahlungen auch bei Retries, Lastspitzen oder teilweisen Ausfällen sicher bleiben.
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        'Lies `x-amzn-signature` aus den Request-Headern.',
        'Rekonstruiere die erwartete Signatur exakt gemäß Buy with Prime (Algorithmus/Kanonisierung laut offizieller Doku); lehne bei Abweichung ab.',
        'Wenn ein Timestamp/Nonce bereitgestellt wird, erzwinge ein enges Frischefenster zur Abwehr von Replay-Angriffen und speichere verarbeitete IDs, um Duplikate zu verhindern.',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data'      => [
            'orderId'  => 'BWP-001',
            'status'   => 'COMPLETED',
            'amount'   => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
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
            'path'   => '/api/webhooks/amazon_bwp',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
