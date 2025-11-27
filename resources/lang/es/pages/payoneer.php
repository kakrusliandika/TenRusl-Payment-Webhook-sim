<?php

return [

    'hint' => 'Notificaciones específicas del producto.',

    'summary' => <<<'TEXT'
Payoneer Checkout entrega notificaciones asíncronas (webhooks) a un endpoint que tú controlas, para que tu sistema pueda conciliar el estado del pago de forma segura fuera del navegador del usuario. La plataforma permite definir una URL dedicada de notificación y elegir el estilo de entrega que mejor se adapte a tu stack—POST (recomendado) o GET, con JSON o parámetros codificados como formulario. Como el conjunto de parámetros y los patrones de firma/autenticación dependen del producto, trata las notificaciones de Payoneer como una superficie de integración: documenta los headers/campos que identifican el evento, incluye metadatos anti-replay cuando estén disponibles y verifica la autenticidad antes de mutar el estado.

Operativamente, comienza aislando un handler pequeño e idempotente que persista un registro inmutable del evento y responda 2xx rápidamente. Mantén la lógica de negocio pesada en workers en background para evitar tormentas de reintentos. Aplica claves de deduplicación y una ventana de frescura para cualquier timestamp/nonce, protegiéndote contra replays o entrega fuera de orden. Si necesitas mayor seguridad, agrega un token emitido por el proveedor (o tu propio secreto aleatorio) en la URL de notificación y valídalo del lado del servidor. Por último, publica un runbook para el equipo que documente endpoints, formatos, códigos de fallo y los pasos exactos de verificación que implementas para tu variante de producto Payoneer—y mantenlo versionado junto al código.
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        'Expón un endpoint dedicado de notificación (POST recomendado); acepta JSON o datos de formulario.',
        'Valida la autenticidad según lo documentado para tu variante de producto (token o campos de firma); rechaza si no coincide.',
        'Impón frescura de timestamp/nonce cuando esté disponible y haz el procesamiento idempotente (guarda una clave de deduplicación).',
        'ACK rápido (2xx) y deriva el trabajo pesado a jobs en background; mantén una traza de auditoría sin registrar secretos.',
    ],

    'example_payload' => [
        'event'     => 'checkout.transaction.completed',
        'provider'  => 'payoneer',
        'data'      => [
            'orderId'  => 'PO-001',
            'amount'   => 25000,
            'currency' => 'IDR',
            'status'   => 'COMPLETED',
        ],
        'sent_at'   => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/payoneer',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
