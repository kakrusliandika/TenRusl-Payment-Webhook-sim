<?php

return [

    'hint' => 'validación de signature_key.',

    'summary' => <<<'TEXT'
Midtrans incluye un `signature_key` calculado dentro de cada notificación HTTP(S) para que puedas verificar el origen antes de actuar. La fórmula es explícita y estable:
    SHA512(order_id + status_code + gross_amount + ServerKey)
Construye la cadena de entrada usando los valores exactos del cuerpo de la notificación (como strings) y tu `ServerKey` privado, luego calcula el digest SHA-512 en formato hex y compáralo con `signature_key` usando una comparación en tiempo constante (constant-time). Si la verificación falla, descarta la notificación. Para mensajes genuinos, usa los campos documentados (por ejemplo, `transaction_status`) para manejar tu máquina de estados—confirma rápido (2xx), encola el trabajo pesado y haz las actualizaciones idempotentes por si hay reintentos o entregas fuera de orden.

Dos errores comunes: formato y coerción. Mantén `gross_amount` exactamente como se proporciona (no lo localices ni cambies decimales) al construir la cadena, y evita recortes o cambios de saltos de línea/espacios. Guarda una clave de deduplicación por evento o por orden para protegerte de condiciones de carrera; registra el resultado de la verificación y un hash del cuerpo para auditoría sin filtrar secretos. Combina esto con rate limiting en el endpoint y códigos de fallo claros para que tu monitoreo distinga errores temporales (con retry) de rechazos permanentes (firma inválida).
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'Toma `order_id`, `status_code`, `gross_amount` del body (como strings) y añade tu `ServerKey`.',
        'Calcula `SHA512(order_id + status_code + gross_amount + ServerKey)` y compáralo con `signature_key` (constant-time).',
        'Rechaza si no coincide; si no, actualiza el estado desde `transaction_status`. Mantén idempotencia y responde 2xx rápido.',
        'Cuidado con cambios de formato en `gross_amount` y con whitespace accidental al concatenar.',
    ],

    'example_payload' => [
        'order_id'           => 'ORDER-001',
        'status_code'        => '200',
        'gross_amount'       => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key'      => '<sha512>',
        'provider'           => 'midtrans',
        'sent_at'            => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/midtrans',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
