<?php

return [

    'hint' => 'Pruebas rápidas.',

    'summary' => <<<'TEXT'
Este proveedor mock es un entorno determinista y sin credenciales para ejercitar todo el ciclo de vida del webhook: creación de requests, transiciones de estado idempotentes, entrega, verificación, reintentos y manejo de fallos. Como funciona sin dependencias externas, puedes iterar en local o en CI, registrar fixtures y demostrar decisiones de arquitectura (por ejemplo, dónde ubicas la verificación vs. la persistencia) sin exponer secretos reales.

Úsalo para simular fallos comunes: entregas demoradas, envíos duplicados, eventos fuera de orden y respuestas 5xx transitorias que disparan backoff exponencial. El mock también soporta diferentes “modos de firma” (none / HMAC-SHA256 / RSA-verify stub) para que el equipo practique hashing del raw body, comparación constant-time y ventanas de timestamp en un entorno seguro. Así validas tus llaves de idempotencia y tablas de deduplicación antes de integrar un gateway real.

Para documentación de calidad, mantén el mock cercano a producción: mismas formas de endpoints, headers y códigos de error; la única diferencia es la raíz de confianza. Reconoce webhooks válidos rápido (2xx) y deriva el trabajo pesado a jobs en background. Trata el payload del mock como no confiable hasta que pase la verificación—y recién entonces aplica tus reglas de negocio. El resultado es un feedback loop rápido y una demo portable que refleja la arquitectura que vas a desplegar.
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'Modos del simulador: none / HMAC-SHA256 / RSA-verify stub; elige por config para practicar rutas de verificación.',
        'Hashea el raw request body exacto; compara con función timing-safe; impone ventanas cortas contra replay.',
        'Registra event IDs procesados para idempotencia; ACK rápido (2xx) para webhooks válidos y difiere trabajo pesado.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.succeeded',
        'provider' => 'mock',
        'data'     => [
            'payment_id' => 'pay_mock_001',
            'amount'     => 25000,
            'currency'   => 'IDR',
            'status'     => 'succeeded',
        ],
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
            'path'   => '/api/webhooks/mock',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
