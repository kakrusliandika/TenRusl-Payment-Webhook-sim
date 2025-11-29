<?php

return [

    'hint' => 'Firma con headers Client-Id/Request-*.',

    'summary' => <<<'TEXT'
DOKU protege las Notificaciones HTTP con una firma canónica basada en headers que debes verificar antes de actuar sobre cualquier payload. Cada callback llega con un header `Signature` cuyo valor tiene la forma `HMACSHA256=<base64>`. Para reconstruir el valor esperado, primero calcula un `Digest` del cuerpo: SHA-256 de los bytes JSON en bruto, codificado en base64. Luego, construye una cadena delimitada por saltos de línea compuesta por cinco componentes en este orden y escritura exactos:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (p. ej. `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
Después calcula un HMAC con SHA-256 usando tu DOKU Secret Key como clave sobre esa cadena canónica, codifica el resultado en base64 y antepone `HMACSHA256=`. Por último, compara contra el header `Signature` usando una comparación en tiempo constante (constant-time). Cualquier discrepancia, componente faltante o valor malformado debe tratarse como un fallo de autenticación y la solicitud debe rechazarse de inmediato.

Para resiliencia y seguridad, confirma rápidamente las notificaciones válidas (2xx) y pasa el trabajo pesado a jobs en segundo plano para no disparar reintentos. Haz el consumer idempotente registrando identificadores procesados (p. ej., `Request-Id` o un ID de evento en el cuerpo). Valida frescura: `Request-Timestamp` debe estar dentro de una ventana estricta para impedir ataques de replay; y asegúrate de que `Request-Target` coincida con tu ruta real para evitar errores de canonicalización. Al parsear, sigue la guía de DOKU para no ser estricto: ignora campos desconocidos y prefiere evolución de esquema frente a parsers frágiles. Durante respuesta a incidentes, registra la presencia de headers requeridos, el digest/firma calculados (nunca el secreto) y un hash del cuerpo para facilitar auditorías sin filtrar datos sensibles.
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'Lee los headers: `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature` e infiere `Request-Target` (tu path de ruta).',
        'Calcula `Digest = base64( SHA256(raw JSON body) )`.',
        'Construye la cadena canónica con líneas: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (en ese orden, cada una en su propia línea, sin salto de línea final).',
        'Calcula expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )`; compáralo con `Signature` usando constant-time.',
        'Exige frescura del timestamp; procesa de forma idempotente; ACK rápido (2xx) y descarga trabajo pesado.',
    ],

    'example_payload' => [
        'order' => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider' => 'doku',
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
            'path' => '/api/webhooks/doku',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
