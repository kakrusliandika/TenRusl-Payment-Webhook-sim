<?php

return [

    'hint' => 'API Verify Webhook Signature.',

    'summary' => <<<'TEXT'
PayPal exige la verificación del lado del servidor de cada webhook mediante la API oficial Verify Webhook Signature. Tu listener debe extraer los headers enviados con la notificación—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL` y `PAYPAL-TRANSMISSION-SIG`—junto con tu `webhook_id` y el cuerpo **crudo** de la petición (`webhook_event`). Envía todo esto al endpoint de verificación y acepta el evento solo si PayPal devuelve un resultado exitoso. Esto reemplaza mecanismos antiguos y simplifica la paridad entre productos REST.

Construye el consumidor como una compuerta rápida e idempotente: verifica primero, persiste un registro del evento, responde con 2xx y envía el trabajo pesado a una cola. Usa comparación constant-time para cualquier chequeo local y conserva los bytes crudos al reenviar a PayPal para evitar errores sutiles por re-serialización. Impón una tolerancia de tiempo estrecha alrededor de `PAYPAL-TRANSMISSION-TIME` para reducir ventanas de replay y registra solo datos mínimos de auditoría (request ID, resultado de verificación, hash del body—sin secretos). Con este patrón, las entregas duplicadas y las caídas parciales no provocarán doble procesamiento, y tu auditoría seguirá siendo confiable durante incidentes.
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'Reúne headers: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; conserva el body crudo.',
        'Llama a Verify Webhook Signature API con esos valores más webhook_id y webhook_event; acepta solo si es exitoso.',
        'Trata la verificación como una compuerta; aplica una tolerancia corta para mitigar replays y haz el consumidor idempotente.',
        'Devuelve 2xx rápidamente, encola trabajo pesado y registra diagnósticos mínimos (sin secretos).',
    ],

    'example_payload' => [
        'id'          => 'WH-' . now()->timestamp,
        'event_type'  => 'PAYMENT.CAPTURE.COMPLETED',
        'resource'    => [
            'id'     => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider'    => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/paypal',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
