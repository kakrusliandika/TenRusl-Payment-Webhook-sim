<?php

return [

    'hint' => 'HMAC-Signatur-Header.',

    'summary' => <<<'TEXT'
Lemon Squeezy signiert jedes Webhook mit einem einfachen HMAC über den **rohen Request-Body**. Der Sender verwendet dein Webhook-“signing secret”, um einen SHA-256 HMAC als **Hex-Digest** zu erzeugen; dieser Digest wird im Header `X-Signature` übertragen. Deine Aufgabe: die Body-Bytes exakt so lesen, wie sie ankommen (kein Re-Stringify, keine Whitespace-Änderungen), denselben HMAC mit deinem Secret berechnen, als **Hex**-String ausgeben und per constant-time Vergleich mit `X-Signature` abgleichen. Wenn die Werte abweichen – oder der Header fehlt – musst du die Anfrage ablehnen, bevor irgendeine Business-Logik läuft.

Da Framework-Defaults den Body oft parsen, bevor du ihn hashen kannst, stelle sicher, dass deine Route Zugriff auf die rohen Bytes hat (z. B. “raw body” Handling in Node/Express konfigurieren). Betrachte die Verifizierung als Gate: erst nach Erfolg JSON parsen und State aktualisieren. Mache deinen Handler idempotent, damit Retries/Duplikate keine Side-Effects doppelt anwenden, und erfasse nur minimale Diagnostik (Header-Länge, Verifikationsergebnis, Event-ID) statt Secrets. Für lokale Tests nutze Lemon Squeezys Test-Events und simuliere Fehler, um Retry/Backoff zu prüfen. Dokumentiere den End-to-End Ablauf – Verifizierung, Deduplizierung und asynchrone Verarbeitung – damit Teammitglieder konsistente Ergebnisse in allen Umgebungen reproduzieren können.
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        'Lies `X-Signature` (HMAC-SHA256 **Hex** über den rohen Body) und greife auf die rohen Request-Bytes zu.',
        'Berechne den Hex-HMAC mit deinem signing secret und vergleiche per timing-sicherer Funktion.',
        'Bei Mismatch/fehlendem Header: ablehnen; JSON erst nach erfolgreicher Verifizierung parsen.',
        'Stelle sicher, dass das Framework den raw body liefert (keine Re-Serialisierung); Handler idempotent machen und nur minimale Diagnostik loggen.',
    ],

    'example_payload' => [
        'meta' => ['event_name' => 'order_created'],
        'data' => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path' => '/api/webhooks/lemonsqueezy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
