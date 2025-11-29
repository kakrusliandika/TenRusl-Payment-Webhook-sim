<?php

return [

    'hint' => 'Firma con token de callback.',

    'summary' => <<<'TEXT'
Xendit firma los eventos de webhook usando un token por cuenta que se expone en el header `x-callback-token`. Tu integración debe comparar este header con el token que obtuviste desde el dashboard de Xendit y rechazar cualquier solicitud sin token o con token distinto. Algunos productos de webhook también incluyen un `webhook-id` que puedes almacenar para evitar procesamiento duplicado en caso de reintentos.

Operativamente, mantén la verificación como primer paso, persiste un registro inmutable del evento, responde con 2xx de inmediato y mueve el trabajo pesado a colas. Aplica idempotencia usando `webhook-id` (o tu propia clave) y usa una ventana de tiempo estricta si se proporciona metadata de timestamp. Documenta el flujo completo (verificación, deduplicación, reintentos y códigos de error) para que el equipo integre de forma consistente en todos los entornos.
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        'Compara `x-callback-token` con tu token único del dashboard de Xendit; rechaza si no coincide.',
        'Usa `webhook-id` (cuando exista) para deduplicar; trata la verificación como un gate duro antes de parsear JSON.',
        'Responde 2xx rápido y difiere el trabajo pesado; registra diagnósticos mínimos sin exponer secretos.',
    ],

    'example_payload' => [
        'id' => 'evt_xnd_'.now()->timestamp,
        'event' => 'invoice.paid',
        'data' => [
            'id' => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
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
            'path' => '/api/webhooks/xendit',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
