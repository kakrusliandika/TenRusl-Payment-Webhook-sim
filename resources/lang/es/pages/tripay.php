<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
TriPay envía callbacks a la URL que configures e incluye headers que identifican el evento y ayudan a autenticar al emisor. En particular, los callbacks llevan `X-Callback-Event` con un valor como `payment_status` y `X-Callback-Signature` para la validación de firma según la documentación de TriPay. Tu consumer debe leer estos headers, verificar la autenticidad de la solicitud y solo entonces actualizar el estado interno.

Diseña el endpoint para que sea rápido e idempotente. Usa una ventana corta de vigencia si hay timestamps/nonces y mantén un almacenamiento ligero de deduplicación basado en referencias o identificadores de evento. Devuelve un 2xx rápidamente una vez registrado el evento y gestiona los efectos secundarios de forma asíncrona. Para transparencia y manejo de incidentes, conserva un rastro de auditoría que registre la hora de recepción, metadatos del evento y resultados de verificación sin registrar secretos.
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        'Inspecciona `X-Callback-Event` (p. ej., `payment_status`) y `X-Callback-Signature`.',
        'Valida la firma como lo documenta TriPay; rechaza si no coincide o falta el header.',
        'Mantén el procesamiento idempotente (dedup por reference/event ID) y confirma rápido (2xx).',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status'    => 'PAID',
        'amount'    => 125000,
        'currency'  => 'IDR',
        'provider'  => 'tripay',
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
            'path'   => '/api/webhooks/tripay',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
