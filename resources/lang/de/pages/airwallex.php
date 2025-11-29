<?php

return [

    'hint' => 'HMAC-SHA256 über x-timestamp + Body.',

    'summary' => <<<'TEXT'
Airwallex-Webhooks werden signiert, damit du sowohl die Authentizität als auch die Integrität prüfen kannst, bevor du deine Datenbank berührst. Jede Anfrage enthält zwei wichtige Header: `x-timestamp` und `x-signature`. Um eine Nachricht zu validieren, lies den rohen HTTP-Body exakt so ein, wie er empfangen wurde, konkateniere den `x-timestamp`-Wert (als String) mit diesem rohen Body, um die Digest-Eingabe zu bilden, und berechne dann ein HMAC mit SHA-256, wobei der gemeinsam genutzte Secret der Benachrichtigungs-URL als Schlüssel dient. Airwallex erwartet das Ergebnis als **Hex-Digest**; vergleiche ihn mit dem Header `x-signature` mithilfe eines zeitkonstanten Vergleichs, um Timing-Leaks zu vermeiden. Falls die Signaturen nicht übereinstimmen oder der Zeitstempel fehlt bzw. ungültig ist, brich hart ab und gib eine Nicht-2xx-Antwort zurück.

Da Replay-Angriffe in jedem Webhook-System ein reales Risiko darstellen, solltest du ein Frischefenster auf `x-timestamp` anwenden. Lehne Nachrichten ab, die zu alt oder zu weit in der Zukunft liegen, und speichere bereits verarbeitete Event-IDs, um nachgelagerte Seiteneffekte zu deduplizieren (Idempotenz auf Anwendungsebene). Behandle das Payload als nicht vertrauenswürdig, bis die Verifizierung bestanden ist; serialisiere JSON nicht erneut vor dem Hashen—verwende exakt die rohen Bytes, wie sie eingegangen sind, um subtile Unterschiede bei Leerzeichen/Reihenfolge zu vermeiden. Wenn die Verifizierung erfolgreich ist, antworte schnell mit einem `2xx`-Status; führe aufwändige Arbeit asynchron aus, um die Retry-Logik freundlich zu halten und versehentliche Duplikate zu reduzieren.

Für lokale und CI-Workflows stellt Airwallex erstklassige Tools zur Verfügung: Konfiguriere deine Benachrichtigungs-URL(s) im Dashboard, sieh dir Beispiel-Payloads an und **sende Testereignisse** an deinen Endpoint. Beim Debugging solltest du den empfangenen `x-timestamp`, eine Vorschau der berechneten Signatur (niemals Secrets loggen) und ggf. vorhandene Event-IDs protokollieren. Wenn du den Secret-Schlüssel rotierst, führe die Umstellung sorgfältig durch und überwache die Fehlerrate bei Signaturen. Dokumentiere schließlich die gesamte Kette—Verifizierung, Deduplikation, Retries und Fehlerantworten—damit Teammitglieder die Ergebnisse mit denselben Roh-Body-Hashregeln und demselben Zeitfenster reproduzieren können.
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        'Extrahiere `x-timestamp` und `x-signature` aus den Headern.',
        'Baue value_to_digest = <x-timestamp> + <roher HTTP-Body> (exakte Bytes).',
        'Berechne expected = HMAC-SHA256(value_to_digest, <webhook secret>) als HEX und vergleiche es mit `x-signature` mittels zeitkonstantem Vergleich.',
        'Lehne ab, wenn die Signaturen nicht übereinstimmen oder der Zeitstempel veraltet ist; dedupliziere außerdem bereits verarbeitete Event-IDs, um Idempotenz sicherzustellen.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'payment_intent_id' => 'pi_awx_001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'succeeded',
        ],
        'provider' => 'airwallex',
        'created_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/airwallex',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
