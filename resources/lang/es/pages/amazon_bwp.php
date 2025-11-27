<?php

return [

    'hint' => 'Cabecera x-amzn-signature en cada solicitud.',

    'summary' => <<<'TEXT'
Buy with Prime (BWP) firma cada webhook para que puedas confirmar que realmente se originó en Amazon y que no fue modificado durante el tránsito. Cada solicitud incluye la firma digital en la cabecera `x-amzn-signature`. Tu handler debe reconstruir la firma esperada exactamente como lo documenta BWP para el tipo de evento y entorno correspondientes; si los valores no coinciden, rechaza la llamada. Trata cualquier timestamp/nonce que acompañe la solicitud como parte de tu postura contra ataques de replay, aplicando una ventana de validez estricta y almacenando los identificadores ya procesados para evitar duplicados.

Operativamente, diseña el endpoint para que sea rápido y determinista: verifica primero, confirma con un `2xx` una vez que el registro sea seguro y ejecuta el trabajo más pesado de forma asíncrona. Si dependes de listas de permitidos (allowlists), recuerda que las IP y redes pueden cambiar; la verificación criptográfica es el ancla principal de confianza. Mantén una traza de auditoría segura (ID de la solicitud, presencia de la firma, resultado de la verificación y un hash del cuerpo—no del secreto). Para pruebas locales, puedes simular el paso de verificación detrás de una variable de entorno, asegurando que las rutas de producción siempre validen las firmas. Al rotar claves o actualizar reglas de canonicalización, avanza con cautela, monitoriza las tasas de error y documenta el conjunto exacto de cabeceras y la lógica de hashing/canonicalización que implementas para que el resto de servicios de tu stack se mantengan sincronizados.

Desde la perspectiva de ergonomía de integración, expón **razones de fallo claras** (firma inválida, timestamp caducado, solicitud mal formada) y devuelve códigos de error estables para que los reintentos se comporten de forma predecible. Combina esto con idempotencia a nivel de aplicación y protección contra replay para que las transiciones de estado de pago en los sistemas posteriores sean seguras incluso ante reintentos, picos de tráfico o caídas parciales.
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        'Lee `x-amzn-signature` de las cabeceras de la solicitud.',
        'Recrea la firma esperada exactamente como la define Buy with Prime (algoritmo/canonicalización según la documentación oficial); rechaza si no coinciden.',
        'Si se proporciona un timestamp/nonce, aplica una ventana de frescura estricta para mitigar ataques de replay y almacena los IDs procesados para evitar duplicados.',
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
