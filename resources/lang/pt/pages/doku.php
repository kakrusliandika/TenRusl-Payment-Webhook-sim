<?php

return [

    'hint' => 'Assinatura usando cabeçalhos Client-Id/Request-*.',

    'summary' => <<<'TEXT'
A DOKU protege as Notificações HTTP com uma assinatura canônica baseada em cabeçalhos, que você deve validar antes de agir sobre qualquer payload. Cada callback chega com um cabeçalho `Signature` cujo valor tem o formato `HMACSHA256=<base64>`. Para reconstruir o valor esperado, primeiro calcule o `Digest` do corpo da requisição: SHA-256 dos bytes JSON brutos, codificado em base64. Em seguida, monte uma string delimitada por quebras de linha composta por cinco componentes, exatamente nesta ordem e grafia:
  • `Client-Id:<value>`
  • `Request-Id:<value>`
  • `Request-Timestamp:<value>`
  • `Request-Target:<path-of-your-notification-url>` (ex.: `/payments/notifications`)
  • `Digest:<base64-of-SHA256(body)>`
Depois, calcule um HMAC com SHA-256 usando sua DOKU Secret Key como chave sobre essa string canônica, codifique o resultado em base64 e prefixe com `HMACSHA256=`. Por fim, compare com o cabeçalho `Signature` usando comparação em tempo constante (constant-time). Qualquer divergência, componente ausente ou valor malformado deve ser tratado como falha de autenticação e a requisição deve ser rejeitada imediatamente.

Para resiliência e segurança, reconheça notificações válidas rapidamente (2xx) e empurre o trabalho pesado para jobs em background para não disparar retries. Torne o consumer idempotente registrando identificadores já processados (por exemplo, `Request-Id` ou um event ID no corpo). Valide frescor: `Request-Timestamp` deve ficar dentro de uma janela curta para evitar replay attacks; e garanta que `Request-Target` corresponda à sua rota real para evitar bugs de canonicalização. Ao fazer parsing, siga a orientação da DOKU para não ser estrito: ignore campos desconhecidos e prefira evolução de schema a parsers frágeis. Durante incidentes, registre a presença dos cabeçalhos obrigatórios, o digest/assinatura calculados (nunca o segredo) e um hash do corpo para ajudar na auditoria sem vazar dados sensíveis.
TEXT
    ,

    // dari view DOKU
    'docs' => 'https://developers.doku.com/get-started-with-doku-api/signature-component/non-snap/signature-component-from-request-header',

    'signature_notes' => [
        'Leia os cabeçalhos: `Client-Id`, `Request-Id`, `Request-Timestamp`, `Signature` e infira `Request-Target` (o path da sua rota).',
        'Calcule `Digest = base64( SHA256(raw JSON body) )`.',
        'Monte a string canônica com as linhas: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest (nessa ordem, cada uma em sua própria linha, sem newline no final).',
        'Calcule expected = `HMACSHA256=base64( HMAC-SHA256(canonical, SecretKey) )` e compare com `Signature` usando constant-time.',
        'Exija frescor do timestamp; processe de forma idempotente; ACK rápido (2xx) e descarregue o trabalho pesado.',
    ],

    'example_payload' => [
        'order'       => ['invoice_number' => 'INV-001'],
        'transaction' => ['status' => 'SUCCESS'],
        'provider'    => 'doku',
        'sent_at'     => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/doku',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
