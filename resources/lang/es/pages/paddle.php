<?php

return [

    'hint' => 'Firma con clave pública (Classic) / secreto (Billing).',

    'summary' => <<<'TEXT'
Paddle Billing firma cada webhook con un header `Paddle-Signature` que incluye un timestamp Unix (`ts`) y una firma (`h1`). Para verificar manualmente, concatena el timestamp, dos puntos y el raw request body exacto para construir el “signed payload”; luego hashea ese valor con la clave secreta de la notification destination y compara el resultado con `h1` usando una función constant-time (timing-safe). Paddle genera un secreto distinto por cada notification destination—trátalo como una contraseña y mantenlo fuera del control de versiones.

Usa los SDK oficiales o tu propio middleware para verificar antes de cualquier parsing. Como el timing y las transformaciones del body son trampas comunes, asegúrate de que tu framework exponga los bytes crudos (por ejemplo, Express `express.raw({ type: 'application/json' })`) y aplica una tolerancia corta para `ts` para disuadir replays. Tras verificar, reconoce rápido (2xx), guarda el event ID para idempotencia y mueve el trabajo pesado a jobs en background. Esto mantiene la entrega confiable y evita efectos colaterales duplicados bajo reintentos.

Al migrar desde Paddle Classic, ten en cuenta que la verificación pasó de firmas con clave pública a HMAC basado en secreto para Billing. Actualiza tus runbooks y la gestión de secretos, y monitorea las métricas de verificación cuando despliegues cambios. Logs claros (sin secretos) y respuestas de error deterministas simplifican mucho el manejo de incidentes y el soporte a partners.
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        'Lee el header `Paddle-Signature`; parsea los valores `ts` y `h1`.',
        'Construye el signed payload = `ts + ":" + <raw request body>`; hashea con el secreto del endpoint.',
        'Compara tu hash con `h1` usando una función timing-safe; impone una tolerancia corta para `ts` para prevenir replay.',
        'Prefiere SDKs oficiales o middleware de verificación; solo parsea JSON después de verificar correctamente.',
    ],

    'example_payload' => [
        'event_id'   => 'evt_' . now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider'   => 'paddle',
        'data'       => [
            'transaction_id' => 'txn_001',
            'amount'         => 25000,
            'currency_code'  => 'IDR',
            'status'         => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/paddle',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
