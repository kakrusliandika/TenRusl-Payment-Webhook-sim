<?php

return [

    'hint' => 'Firma de callback estilo MD5/HMAC.',

    'summary' => <<<'TEXT'
Skrill envía un callback de estado a tu `status_url` y espera que valides el mensaje usando `md5sig`, un **MD5 en MAYÚSCULAS** de una concatenación bien definida de campos (por ejemplo: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`). Solo si tu valor calculado coincide con el `md5sig` entrante debes confiar en el payload. Skrill también admite un `sha2sig` alternativo (SHA-2 en mayúsculas) bajo solicitud, construido de forma análoga a `md5sig`.

En la práctica, mantén la validación de la firma en tu backend (nunca expongas la secret word) y hashea los **valores exactos** tal como te los postean en el callback. Haz el endpoint idempotente (dedup por transaction o event ID), devuelve 2xx rápidamente tras persistir y difiere el trabajo no crítico. Al depurar, registra el resultado de verificación y un hash del cuerpo, sin incluir secretos en logs. Cuida el formato—los campos de monto y moneda deben usarse tal cual al construir la cadena—para que las comparaciones sean estables entre reintentos y entornos.
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        'Reconstruye `md5sig` exactamente: concatena los campos documentados (p. ej., merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) y calcula **MD5 en MAYÚSCULAS**.',
        'Compara con el `md5sig` recibido; opcionalmente usa `sha2sig` (SHA-2 en MAYÚSCULAS) si Skrill lo habilita.',
        'Valida solo del lado del servidor con los valores exactos posteados; mantén el handler idempotente y devuelve 2xx rápidamente.',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount' => '10.00',
        'mb_currency' => 'EUR',
        'status' => '2',
        'md5sig' => '<UPPERCASE_MD5>',
        'provider' => 'skrill',
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
            'path' => '/api/webhooks/skrill',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
