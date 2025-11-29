<?php

return [

    'hint' => 'Firma de callback específica del proveedor.',

    'summary' => <<<'TEXT'
Los callbacks de OY! forman parte de una postura de seguridad más amplia basada en API keys registradas y allowlisting de IP de origen para solicitudes de partners. OY! también ofrece la función Authorization Callback para que puedas controlar y aprobar callbacks antes de que lleguen a tu sistema, agregando una compuerta explícita para prevenir cambios de estado no deseados. Aun así, en la práctica debes tratar cualquier callback entrante como no confiable hasta verificarlo, imponer frescura (ventana de timestamp/nonce) y hacer el consumidor idempotente para que los reintentos y la entrega fuera de orden sean seguros.

Como los proveedores difieren en cómo firman callbacks, nuestro simulador demuestra una línea base endurecida usando un header HMAC (por ejemplo, `X-Callback-Signature`) calculado sobre el raw request body exacto con un secreto compartido. Esto ilustra los mismos principios que usarás en producción: hashing de bytes crudos (sin re-serializar), comparación constant-time y ventanas cortas contra replay. Combínalo con un pequeño store de deduplicación y acknowledgements 2xx rápidos para mantener saludable la lógica de reintentos del proveedor y evitar efectos colaterales duplicados.

Operativamente, mantén una traza de auditoría (hora de recepción, resultado de verificación, hash del body — no el secreto), rota secretos de forma segura y monitorea la tasa de fallos de verificación. Si dependes de allowlists, recuerda que pueden cambiar; una verificación criptográfica (o la compuerta explícita de autorización de OY!) debe seguir siendo el ancla principal de confianza. Mantén el endpoint acotado, predecible y bien documentado para que otros servicios y el equipo puedan reutilizarlo con confianza.
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'Usa la postura de seguridad de OY!: API key registrada + allowlisting de IP de origen para requests de partners.',
        'Aprovecha Authorization Callback (dashboard) para aprobar callbacks antes de que lleguen a tu sistema.',
        'En este simulador, verifica `X-Callback-Signature = HMAC-SHA256(raw_body, secret)` como modelo de buenas prácticas; aplica comparación constant-time y checks de frescura.',
        'Haz el procesamiento idempotente y responde 2xx rápidamente para mantener sanos los reintentos del proveedor.',
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
