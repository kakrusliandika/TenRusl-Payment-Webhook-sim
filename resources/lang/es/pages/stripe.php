<?php

return [

    'hint' => 'Header de firma con marca de tiempo.',

    'summary' => <<<'TEXT'
Stripe firma cada solicitud de webhook y expone la firma calculada en el header `Stripe-Signature`. Tu endpoint debe verificar la solicitud antes de hacer cualquier trabajo. Con las librerías oficiales de Stripe, pasa tres entradas a la rutina de verificación: el cuerpo crudo exacto (raw body), el header `Stripe-Signature` y el secreto de tu endpoint. Continúa solo cuando la verificación sea exitosa; de lo contrario, devuelve un no-2xx y detén el procesamiento. Si no puedes usar una librería oficial, implementa la verificación manual según lo documentado, incluyendo comprobaciones de tolerancia de timestamp para reducir el riesgo de replay.

Trata la verificación de firma como una compuerta estricta. Mantén el handler idempotente (almacena IDs de eventos), responde con 2xx rápidamente tras persistir y envía el trabajo pesado a jobs en background. Asegúrate de que tu framework te entregue **los bytes crudos**—evita re-serializar JSON antes de hashear, porque cualquier cambio en espacios o en el orden romperá la verificación. Por último, registra diagnósticos mínimos (resultado de verificación, tipo de evento, hash del cuerpo—sin secretos) y monitorea fallos durante rotación de secretos o cambios de endpoint.
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        'Lee el header `Stripe-Signature`; obtén el endpoint secret desde el dashboard de Stripe.',
        'Verifica con librerías oficiales pasando: raw body, `Stripe-Signature` y endpoint secret.',
        'Si verificas manualmente, aplica tolerancia de timestamp para mitigar replay y compara firmas con una función timing-safe.',
        'Acepta solo si es exitoso; guarda IDs de eventos para idempotencia y devuelve 2xx rápido tras persistir.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider' => 'stripe',
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
            'path' => '/api/webhooks/stripe',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
