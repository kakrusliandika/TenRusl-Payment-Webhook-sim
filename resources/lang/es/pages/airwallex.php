<?php

return [

    'hint' => 'HMAC-SHA256 sobre x-timestamp + body.',

    'summary' => <<<'TEXT'
Los webhooks de Airwallex se firman para que puedas verificar tanto la autenticidad como la integridad antes de tocar tu base de datos. Cada petición incluye dos cabeceras críticas: `x-timestamp` y `x-signature`. Para validar un mensaje, lee el cuerpo HTTP en bruto exactamente como se recibió, concatena el valor de `x-timestamp` (como cadena) con ese cuerpo en bruto para formar la entrada del digest y luego calcula un HMAC usando SHA-256 con el secreto compartido de la URL de notificación como clave. Airwallex espera el resultado como un **hex digest**; compáralo con la cabecera `x-signature` usando una comparación en tiempo constante para evitar fugas de tiempo. Si las firmas no coinciden, o si la marca de tiempo falta o no es válida, falla de forma segura y devuelve una respuesta que no sea 2xx.

Dado que los replays son un riesgo real en cualquier sistema de webhooks, aplica una ventana de frescura a `x-timestamp`. Rechaza los mensajes que sean demasiado antiguos o demasiado adelantados en el tiempo y almacena los IDs de eventos ya procesados para deduplicar efectos secundarios posteriores (idempotencia en la capa de tu aplicación). Trata el payload como no confiable hasta que la verificación se complete correctamente; no vuelvas a serializar el JSON antes de hacer el hash: usa exactamente los mismos bytes en bruto que llegaron para evitar discrepancias sutiles de espacios en blanco u orden. Cuando la verificación tenga éxito, devuelve rápidamente una respuesta `2xx`; realiza el trabajo pesado de forma asíncrona para mantener un comportamiento de reintentos saludable y reducir duplicados accidentales.

Para flujos locales y de CI, Airwallex ofrece herramientas de primera clase: configura tus URL de notificación en el panel, previsualiza payloads de ejemplo y **envía eventos de prueba** contra tu endpoint. Al depurar, registra el `x-timestamp` recibido, una vista previa de la firma calculada (nunca registres secretos) y cualquier identificador de evento si existe. Si rotas la clave secreta, haz el despliegue con cuidado y monitoriza la tasa de errores de firma. Por último, documenta toda la cadena—verificación, deduplicación, reintentos y respuestas de error—para que tus compañeros puedan reproducir los resultados con las mismas reglas de hash del cuerpo en bruto y la misma ventana de tiempo.
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        'Extrae `x-timestamp` y `x-signature` de las cabeceras.',
        'Construye value_to_digest = <x-timestamp> + <cuerpo HTTP en bruto> (bytes exactos).',
        'Calcula expected = HMAC-SHA256(value_to_digest, <webhook secret>) en HEX; compáralo con `x-signature` usando una comparación en tiempo constante.',
        'Rechaza si las firmas no coinciden o la marca de tiempo está caducada; además, deduplica los IDs de eventos ya procesados para garantizar la idempotencia.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment_intent.succeeded',
        'data'     => [
            'payment_intent_id' => 'pi_awx_001',
            'amount'            => 25000,
            'currency'          => 'IDR',
            'status'            => 'succeeded',
        ],
        'provider'   => 'airwallex',
        'created_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/airwallex',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
