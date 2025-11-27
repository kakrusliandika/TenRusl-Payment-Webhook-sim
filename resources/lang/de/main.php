<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site / Brand
    |--------------------------------------------------------------------------
    */
    'site_name' => 'TenRusl',

    /*
    |--------------------------------------------------------------------------
    | Global Navigation
    |--------------------------------------------------------------------------
    */
    'home_title'      => 'Startseite',
    'providers_title' => 'Provider',
    'features'        => 'Funktionen',
    'endpoints'       => 'Endpoints',
    'signature'       => 'Signatur',
    'tooling'         => 'Tools',
    'openapi'         => 'OpenAPI',
    'github'          => 'GitHub',

    /*
    |--------------------------------------------------------------------------
    | Accessibility / ARIA
    |--------------------------------------------------------------------------
    */
    'aria' => [
        'primary_nav'  => 'Primäre Navigation',
        'utility_nav'  => 'Utility Navigation',
        'toggle_menu'  => 'Hauptmenü ein- oder ausblenden',
        'toggle_theme' => 'Helles und dunkles Theme umschalten',
        'skip_to_main' => 'Direkt zum Hauptinhalt springen',
        'language'     => 'Anzeigesprache ändern',
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer / Legal
    |--------------------------------------------------------------------------
    */
    'terms'       => 'Nutzungsbedingungen',
    'privacy'     => 'Datenschutz',
    'cookies'     => 'Cookies',
    'footer_demo' => 'Demo-Umgebung für Zahlungsarchitektur zum Lernen, Testen und Erklären moderner Webhook-first-Flows.',
    'build'       => 'Build',

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults (override per page via layout/seo.blade.php)
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'default_title'       => 'Payment Webhook Simulator',
        'default_description' => 'Simuliere reale Zahlungsflüsse mit idempotenten Operationen, verifizierten Webhook-Signaturen, Event-Deduplikation und realistischem Retry- und Backoff-Verhalten – alles ohne echte Zugangsdaten zu einem Payment Gateway preiszugeben oder zu benötigen.',
        'default_image_alt'   => 'Diagramm der Architektur des Payment Webhook Simulators mit Zahlungsprovidern, Webhook-Callbacks, Retries und Status-Updates.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Home (Landing)
    |--------------------------------------------------------------------------
    */
    'home' => [
        'title'       => 'Payment Webhook Simulator',
        'description' => 'Stabilisiere deine Zahlungsintegration in einer sicheren, produktionsähnlichen Sandbox. Übe den Umgang mit Idempotency-Key, Signaturprüfung, Event-Deduplikation und Retries mit Backoff, bevor du Anfragen an ein echtes Payment Gateway sendest.',
        'hero' => [
            'heading_prefix' => 'PWS',
            'heading_emph'   => 'Idempotent',
            'lede'           => 'Simuliere eingehende Zahlungsereignisse von mehreren Providern, überprüfe Signaturen des Roh-Request-Bodys, dedupliziere laute Retries und beobachte realistische Backoff-Strategien End-to-End – ohne echte Zugangsdaten oder Produktions-Webhooks zu verwenden.',
            'cta_docs'       => 'OpenAPI-Referenz',
            'cta_features'   => 'Funktionen entdecken',
            'cta_github'     => 'Quellcode auf GitHub ansehen',
            'stats' => [
                'providers' => ['label' => 'Simulierte Provider'],
                'tests'     => ['label' => 'Automatisierte Tests'],
                'openapi'   => ['label' => 'Dokumentierte Endpoints'],
            ],
            'chip'           => 'POST : /api/webhooks/mock mit signierter JSON-Payload',
            'simulate'       => 'Simuliere echte Zahlungs-Webhooks mit Idempotenz, Signaturprüfungen und Retry-Logik, bevor du live gehst.',
        ],
        'sections' => [
            'features' => [
                'title' => 'Funktionen',
                'lede'  => 'Härke und stabilisiere deine Zahlungsintegration mit realistischen, wiederholbaren Simulationen, die widerspiegeln, wie moderne Gateways Webhook-Events in Produktion senden, signieren und erneut zustellen.',
                'ship' => 'Sichere Zahlungsflüsse bereitstellen, ohne echte Gateway-Zugangsdaten zu verwenden.',
                'items' => [
                    [
                        'title' => 'Idempotenz',
                        'desc'  => 'Teste korrekten Umgang mit dem Idempotency-Key, sodass doppelte Requests, Replays und Client-Retries in einem einzigen konsistenten Zahlungsdatensatz landen und den Zustand nicht beschädigen.',
                    ],
                    [
                        'title' => 'Signaturprüfung',
                        'desc'  => 'Validiere den Rohinhalt der Anfrage und Provider-Header mit HMAC, Zeitstempeln und Secrets und nutze eine sichere Umgebung, um deine Signaturprüfungslogik vor dem Go-Live zu perfektionieren.',
                    ],
                    [
                        'title' => 'Deduplizierung & Retry',
                        'desc'  => 'Simuliere doppelte Webhook-Zustellungen und exponentielles Backoff, damit du Handler entwerfen kannst, die idempotent, robust und mehrfach ohne Seiteneffekte ausführbar sind.',
                    ],
                    [
                        'title' => 'OpenAPI',
                        'desc'  => 'Durchsuche interaktive OpenAPI-Dokumentation, die jeden Zahlungs- und Webhook-Endpoint beschreibt – inklusive Schemas, Beispielen und sofort nutzbaren curl-Snippets.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc'  => 'Importiere die kuratierte Postman-Collection, um Endpoints aufzurufen, Payloads anzupassen und Fehlerszenarien aus deinem bevorzugten API-Client zu proben.',
                    ],
                    [
                        'title' => 'CI-Integration',
                        'desc'  => 'Binde den Simulator in deine CI-Pipelines ein, sodass Tests, Linter und Vertragsprüfungen bei jedem Push laufen und Integrationsregressionen frühzeitig entdeckt werden.',
                    ],
                ],
            ],
            'endpoints' => [
                'title' => 'Endpoints',
                'lede'  => 'Definiere eine kleine, realistische Oberfläche: Zahlungen erstellen, Status abfragen und Provider-ähnliche Webhooks mit idempotentem Verhalten empfangen.',
                'cards' => [
                    [
                        'title' => 'POST /api/payments',
                        'desc'  => 'Erstellt einen neuen simulierten Zahlungsdatensatz. Dieser Endpoint erfordert einen Idempotency-Key-Header, damit du prüfen kannst, wie deine Anwendung wiederholte Zahlungsversuche dedupliziert.',
                    ],
                    [
                        'title' => 'GET /api/payments/{id}',
                        'desc'  => 'Liest den aktuellen Status für eine bestimmte Zahlung aus, inklusive Statusübergängen und zugehörigen Webhook-Ereignissen, die der Simulator bereits verarbeitet hat.',
                    ],
                    [
                        'title' => 'POST /api/webhooks/{provider}',
                        'desc'  => 'Empfängt Webhook-Callbacks für einen bestimmten Provider (mock, xendit, midtrans und mehr) mit realistischen Payloads, Headern und Signaturschemata, die an jede Integration angepasst sind.',
                    ],
                ],
            ],
            'providers' => [
                'title' => 'Provider',
                'lede'  => 'Teste realistische Webhook-Flows mehrerer Zahlungs-Gateways in einer einzigen Sandbox, ohne echte Zugangsdaten zu verwenden.',
                'cta_all' => 'Alle Provider anzeigen',
                'map'   => [
                    'mock'         => 'Mock',
                    'xendit'       => 'Xendit',
                    'midtrans'     => 'Midtrans',
                    'stripe'       => 'Stripe',
                    'paypal'       => 'PayPal',
                    'paddle'       => 'Paddle',
                    'lemonsqueezy' => 'Lemon Squeezy',
                    'airwallex'    => 'Airwallex',
                    'tripay'       => 'Tripay',
                    'doku'         => 'DOKU',
                    'dana'         => 'DANA',
                    'oy'           => 'OY!',
                    'payoneer'     => 'Payoneer',
                    'skrill'       => 'Skrill',
                    'amazon_bwp'   => 'Amazon BWP',
                ],
            ],
            'signature' => [
                'title' => 'Signaturprüfung',
                'lede'  => 'Überprüfe rohe Webhook-Payloads mit HMAC-Signaturen, Zeitstempeln und strikten Header-Checks.',
                'compare'  => 'Vergleiche die Signatur des Providers mit der Signatur, die du aus Rohdaten, gemeinsamem Secret und Zeitstempel berechnest – in konstanter Zeit.',
                'reject'    => 'Lehne nicht übereinstimmende Signaturen und veraltete Zeitstempel automatisch ab.',
                'cards' => [
                    [
                        'title' => 'HMAC / Zeitstempel',
                        'desc'  => 'Experimentiere mit HMAC-Signaturen mit Zeitstempel, die vor Replay-Angriffen schützen. Sieh dir an, wie Roh-Body-Hashes, geteilte Secrets und signierte Header eine überprüfbare Auditspur für jedes Webhook-Event bilden.',
                    ],
                    [
                        'title' => 'Header-basiert',
                        'desc'  => 'Arbeite mit providerspezifischen Headern und Tokens, von einfachen Bearer-Secrets bis hin zu strukturierten Signatur-Containern, um gefälschte Payloads zu blockieren und legitime Events durchzulassen.',
                    ],
                ],
            ],
            'tooling' => [
                'title' => 'Tools',
                'work'  => 'Funktioniert mit deinem lokalen Dev-Stack',
                'cards' => [
                    [
                        'title' => 'OpenAPI',
                        'desc'  => 'Nutze den integrierten OpenAPI-Explorer, um Schemas zu inspizieren, Beispielanfragen zu generieren und Endpoints direkt im Browser zu testen – ideal zum Teilen und Dokumentieren deiner Zahlungsflüsse im Team.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc'  => 'Klon die Postman-Collection, um Skript-Flows auszuführen, Umgebungen zu parametrieren und das Verhalten verschiedener Provider unter der gleichen Request-Abfolge zu vergleichen.',
                    ],
                    [
                        'title' => 'CI',
                        'desc'  => 'Integriere den Simulator in deine Continuous-Integration-Jobs, damit jeder Branch und Pull Request gegen dieselben Zahlungs- und Webhook-Szenarien geprüft wird wie in Produktion.',
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers (Catalog + Search)
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'title'       => 'Provider',
        'description' => 'Durchsuche unterstützte Zahlungsprovider, prüfe ihre Webhook-Payload-Formate und vergleiche Signaturschemata nebeneinander, um eine konsistente, providerunabhängige Integrationsschicht zu entwerfen.',
        'map' => [
            'mock'         => 'Mock',
            'xendit'       => 'Xendit',
            'midtrans'     => 'Midtrans',
            'stripe'       => 'Stripe',
            'paypal'       => 'PayPal',
            'paddle'       => 'Paddle',
            'lemonsqueezy' => 'Lemon Squeezy',
            'airwallex'    => 'Airwallex',
            'tripay'       => 'Tripay',
            'doku'         => 'DOKU',
            'dana'         => 'DANA',
            'oy'           => 'OY!',
            'payoneer'     => 'Payoneer',
            'skrill'       => 'Skrill',
            'amazon_bwp'   => 'Amazon BWP',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pluralization / Examples
    |--------------------------------------------------------------------------
    */
    'plurals' => [
        'tests' => '{0} Noch keine Tests definiert|{1} :count automatisierter Test|[2,*] :count Tests',
        'items' => '{0} Keine Einträge verfügbar|{1} :count Eintrag|[2,*] :count Einträge',
    ],
];
