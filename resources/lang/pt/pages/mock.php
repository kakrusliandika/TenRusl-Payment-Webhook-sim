<?php

return [

    'hint' => 'Teste rápido.',

    'summary' => <<<'TEXT'
Este provedor mock é um playground determinístico e sem credenciais para exercitar todo o ciclo de vida de webhooks: criação de requisições, transições de estado idempotentes, entrega, verificação, retries e tratamento de falhas. Como roda sem dependências externas, você consegue iterar localmente ou no CI, registrar fixtures e demonstrar decisões de arquitetura (como onde posicionar verificação vs. persistência) sem expor segredos reais.

Use-o para simular modos comuns de falha: entregas atrasadas, envios duplicados, eventos fora de ordem e respostas 5xx transitórias que disparam backoff exponencial. O mock também suporta diferentes “modos de assinatura” (none / HMAC-SHA256 / RSA-verify stub), permitindo que o time pratique hashing do raw body, comparação constant-time e janelas de timestamp em um ambiente seguro. Isso ajuda a validar suas chaves de idempotência e tabelas de deduplicação antes de integrar um gateway real.

Para uma documentação de qualidade, mantenha o mock próximo da produção: mesmas formas de endpoints, headers e códigos de erro; a única diferença é a raiz de confiança. Reconheça webhooks válidos rapidamente (2xx) e descarregue o trabalho pesado para jobs em background. Trate o payload do mock como não confiável até a verificação passar — então aplique suas regras de negócio. O resultado é um ciclo de feedback rápido e uma demo portátil que espelha a arquitetura que você pretende entregar.
TEXT
    ,

    // dari view mock
    'docs' => null,

    'signature_notes' => [
        'Modos do simulador: none / HMAC-SHA256 / RSA-verify stub; escolha via config para praticar os caminhos de verificação.',
        'Faça hash do raw request body exato; compare com uma função timing-safe; imponha janelas curtas contra replay.',
        'Registre IDs de eventos processados para idempotência; ACK rápido (2xx) para webhooks válidos e adie o trabalho pesado.',
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
