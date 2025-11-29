<?php

return [

    'hint' => 'RSA (verificar con la clave pública de DANA).',

    'summary' => <<<'TEXT'
DANA emplea un esquema de firma **asimétrico**: las solicitudes se firman con una clave privada y los integradores las verifican usando la **clave pública oficial de DANA**. En la práctica, obtienes la firma desde el header del webhook (por ejemplo, `X-SIGNATURE`), la decodificas desde base64 y luego verificas el cuerpo HTTP en bruto contra esa firma usando RSA-2048 con SHA-256. Solo si la verificación devuelve un resultado positivo debe considerarse que el payload es auténtico. Si la verificación falla —o falta la firma/el header— responde con un código no 2xx y detén el procesamiento.

Como los webhooks pueden reintentarse o entregarse fuera de orden, diseña tu handler para que sea idempotente: persiste un identificador único del evento y corta el procesamiento de duplicados; valida cualquier timestamp/nonce para garantizar frescura y mitigar replays; y trata todos los campos como no confiables hasta después de la verificación de la firma. Evita volver a serializar el JSON antes de verificar; hashea exactamente los bytes que llegaron por la red. Mantén los secretos y las claves privadas fuera de los logs; si necesitas registrar, guarda solo diagnósticos de alto nivel (resultado de verificación, hash del cuerpo, ID del evento) y protege esos logs en reposo.

Para equipos, publica un runbook corto que cubra: cómo cargar o rotar la clave pública de DANA, cómo verificar en cada lenguaje/runtime que utilices, las reglas exactas de string-to-sign para tu integración y qué constituye un fallo permanente versus uno transitorio. Complementa esto con una política sólida de reintentos/backoff, colas de trabajo acotadas, health checks y alertas ante fallos de verificación. El resultado es un consumidor de webhooks seguro bajo carga, resiliente ante reintentos y conforme con la verificación criptográfica que DANA exige por diseño.
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        'Decodifica desde base64 el valor del header `X-SIGNATURE`.',
        'Verifica RSA-2048 + SHA-256 sobre el cuerpo HTTP en bruto exacto usando la clave pública oficial de DANA; acepta solo si la verificación es positiva.',
        'Rechaza cualquier webhook con firma faltante/no válida o payload malformado; nunca confíes en los datos antes de una verificación exitosa.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.paid',
        'provider' => 'dana',
        'data' => [
            'transaction_id' => 'DANA-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'SUCCESS',
        ],
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
            'path' => '/api/webhooks/dana',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
