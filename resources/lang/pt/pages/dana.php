<?php

return [

    'hint' => 'RSA (verificar com a chave pública da DANA).',

    'summary' => <<<'TEXT'
A DANA utiliza um esquema de assinatura **assimétrico**: as requisições são assinadas com uma chave privada e os integradores verificam usando a **chave pública oficial da DANA**. Na prática, você obtém a assinatura no header do webhook (por exemplo, `X-SIGNATURE`), faz o decode em base64 e então verifica o corpo HTTP bruto (raw) contra essa assinatura usando RSA-2048 com SHA-256. Somente se a verificação retornar um resultado positivo o payload deve ser considerado autêntico. Se a verificação falhar — ou se a assinatura/o header estiver ausente — responda com um código não 2xx e interrompa o processamento.

Como webhooks podem ser reprocessados (retry) ou chegar fora de ordem, projete seu handler para ser idempotente: persista um identificador único do evento e faça short-circuit de duplicatas; valide timestamp/nonce para garantir frescor e mitigar replays; e trate todos os campos como não confiáveis até depois da verificação da assinatura. Evite reserializar o JSON antes de verificar; faça hash/verificação exatamente sobre os bytes que chegaram pela rede. Mantenha segredos e chaves privadas fora dos logs; se precisar logar, registre apenas diagnósticos de alto nível (resultado da verificação, hash do corpo, ID do evento) e proteja esses logs em repouso.

Para times, publique um runbook curto cobrindo: como carregar ou rotacionar a chave pública da DANA, como verificar em cada linguagem/runtime utilizado, as regras exatas de string-to-sign da sua integração e o que caracteriza falhas permanentes versus transitórias. Combine isso com uma política robusta de retry/backoff, filas de trabalho limitadas, health checks e alertas para falhas de verificação. O resultado é um consumidor de webhooks seguro sob carga, resiliente a retries e em conformidade com a verificação criptográfica que a DANA exige por design.
TEXT
    ,

    // dari view DANA
    'docs' => 'https://dashboard.dana.id/api-docs-v2/guide/authentication',

    'signature_notes' => [
        'Faça o decode em base64 do valor do header `X-SIGNATURE`.',
        'Verifique RSA-2048 + SHA-256 sobre o raw HTTP body exato usando a chave pública oficial da DANA; aceite apenas se a verificação for positiva.',
        'Rejeite qualquer webhook com assinatura ausente/inválida ou payload malformado; nunca confie nos dados antes de uma verificação bem-sucedida.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment.paid',
        'provider' => 'dana',
        'data'     => [
            'transaction_id' => 'DANA-001',
            'amount'         => 25000,
            'currency'       => 'IDR',
            'status'         => 'SUCCESS',
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
            'path'   => '/api/webhooks/dana',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
