<?php

return [

    'hint' => 'Signatur mit Client-Id/Request-* Headern.',

    'summary' => <<<'TEXT'
DOKU schützt HTTP-Benachrichtigungen mit einer kanonischen, headerbasierten Signatur, die du verifizieren musst, bevor du auf irgendein Payload reagierst. Jeder Callback enthält einen `Signature`-Header, dessen Wert die Form `HMACSHA256=<base64>` hat. Um den erwarteten Wert zu rekonstruieren, berechnest du zuerst einen `Digest` für den Request-Body: SHA-256 über die rohen JSON-Bytes, anschließend base64-kodiert. Danach baust du einen durch Newlines getrennten String aus fünf Komponenten — exakt in dieser Reihenfolge und Schreibweise:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (z. B. `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
Anschließend berechnest du ein HMAC mit SHA-256 über diesen kanonischen String, wobei dein DOKU Secret Key als Schlüssel dient, base64-kodierst das Ergebnis und stellst `HMACSHA256=` voran. Zum Schluss vergleichst du mit dem `Signature`-Header mittels konstantzeitlicher (constant-time) Vergleichsfunktion. Jede Abweichung, fehlende Komponente oder ein fehlerhafter Wert ist als Authentifizierungsfehler zu behandeln und die Anfrage muss sofort abgelehnt werden.

Für Robustheit und Sicherheit bestätige gültige Benachrichtigungen schnell (2xx) und verschiebe aufwändige Arbeit in Background-Jobs, damit keine Retries getriggert werden. Mache den Consumer idempotent, indem du verarbeitete Identifikatoren speicherst (z. B. `Request-Id` oder eine Event-ID im Body). Prüfe die Frische: `Request-Timestamp` sollte innerhalb eines engen Fensters liegen, um Replay-Angriffe zu verhindern; stelle außerdem sicher, dass `Request-Target` deinem tatsächlichen Route-Pfad entspricht, um Canonicalization-Bugs zu vermeiden. Beim Parsen folge DOKUs Empfehlung, nicht zu strikt zu sein: unbekannte Felder ignorieren und Schema-Evolution gegenüber fragilen Parsern bevorzugen. Im Incident-Fall logge das Vorhandensein der Pflicht-Header, den berechneten Digest/Signature (niemals das Secret) sowie einen Hash des Bodys, um Audits zu unterstützen, ohne sensible Daten preiszugeben.
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'Lies die Header: `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature` und leite `Request-Target` ab (dein Route-Pfad).',
        'Berechne `Digest = base64( SHA256(raw JSON body) )`.',
        'Baue den kanonischen String mit den Zeilen: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (in dieser Reihenfolge, jeweils in eigener Zeile, kein abschließender Zeilenumbruch).',
        'Berechne expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`; vergleiche mit `Signature` per constant-time.',
        'Timestamp-Frische erzwingen; idempotent verarbeiten; schnell ACK (2xx) und schwere Arbeit auslagern.',
    ],

    'example_payload' => [
        'order' => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider' => 'doku',
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
            'path' => '/api/webhooks/doku',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
