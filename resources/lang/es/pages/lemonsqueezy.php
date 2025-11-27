<?php

return [

    'hint' => 'Header de firma HMAC.',

    'summary' => <<<'TEXT'
Lemon Squeezy firma cada webhook con un HMAC directo sobre el **cuerpo crudo de la solicitud**. El emisor usa tu “signing secret” del webhook para producir un HMAC SHA-256 como **hex digest**; ese digest se envía en el header `X-Signature`. Tu tarea es leer los bytes del body exactamente como llegaron (sin re-stringify ni cambios de espacios), calcular el mismo HMAC con tu secret, devolverlo como string **hex** y compararlo con `X-Signature` usando una comparación en tiempo constante (constant-time). Si los valores difieren —o falta el header— rechaza la solicitud antes de ejecutar cualquier lógica de negocio.

Como los frameworks suelen parsear el body antes de que puedas hashearlo, asegúrate de que tu ruta te dé acceso a los bytes crudos (por ejemplo, configura “raw body” en Node/Express). Trata la verificación como una compuerta: solo después de que pase debes parsear el JSON y actualizar el estado. Haz tu handler idempotente para que reintentos o duplicados no apliquen efectos dos veces, y registra diagnósticos mínimos (longitud del header, resultado de verificación, id del evento) en lugar de secretos. Para pruebas locales, usa los eventos de prueba de Lemon Squeezy y simula fallos para confirmar el comportamiento de retry/backoff. Documenta el flujo end-to-end —verificación, deduplicación y procesamiento asíncrono— para que el equipo pueda reproducir resultados consistentes entre entornos.
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        'Lee `X-Signature` (HMAC-SHA256 **hex** del body crudo) y obtén los bytes crudos del request.',
        'Calcula el HMAC en hex con tu signing secret y compáralo con una función segura contra timing.',
        'Rechaza si no coincide o falta el header; solo parsea JSON después de que la verificación sea exitosa.',
        'Asegura que el framework exponga el raw body (sin re-serializar); haz el handler idempotente y registra diagnósticos mínimos.',
    ],

    'example_payload' => [
        'meta'     => ['event_name' => 'order_created'],
        'data'     => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path'   => '/api/webhooks/lemonsqueezy',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
