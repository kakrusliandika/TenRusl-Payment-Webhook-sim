<?php

return [

    'hint' => 'Assinatura de callback específica do provedor.',

    'summary' => <<<'TEXT'
Os callbacks da OY! fazem parte de uma postura de segurança mais ampla baseada em chaves de API registradas e allowlisting de IP de origem para requisições de parceiros. A OY! também oferece o recurso Authorization Callback para que você controle e aprove callbacks antes que eles cheguem ao seu sistema, adicionando um gate explícito para evitar mudanças de estado não intencionais. Na prática, ainda assim trate todo callback recebido como não confiável até ser verificado, imponha frescor (janela de timestamp/nonce) e torne o consumidor idempotente para que retries e entregas fora de ordem permaneçam seguras.

Como provedores públicos diferem na forma de assinar callbacks, nosso simulador demonstra uma linha de base endurecida com um header HMAC (por exemplo, `X-Callback-Signature`) calculado sobre o raw request body exato usando um segredo compartilhado. Isso ilustra os mesmos princípios usados em produção: hashing de bytes brutos (sem re-serialização), comparação constant-time e janelas curtas contra replay. Combine isso com um pequeno store de deduplicação e acknowledgements 2xx rápidos para manter saudável a lógica de retry do provedor, evitando efeitos colaterais duplicados.

Operacionalmente, mantenha uma trilha de auditoria (hora de recebimento, resultado da verificação, hash do body — não o segredo), faça rotação segura de segredos e monitore as taxas de falha de verificação. Se você depender de allowlists, lembre-se de que podem mudar; a verificação criptográfica (ou o gate explícito de autorização da OY) deve permanecer como a principal âncora de confiança. Mantenha o endpoint enxuto, previsível e bem documentado para que outros serviços e colegas possam reutilizá-lo com confiança.
TEXT
    ,

    // dari view OY
    'docs' => 'https://docs.oyindonesia.com/',

    'signature_notes' => [
        'Use a postura de segurança da OY!: chave de API registrada + allowlisting de IP de origem para requisições de parceiros.',
        'Aproveite o Authorization Callback (dashboard) para aprovar callbacks antes que cheguem ao seu sistema.',
        'Neste simulador, verifique `X-Callback-Signature = HMAC-SHA256(raw_body, secret)` como modelo de boas práticas; aplique comparação constant-time e checagens de frescor.',
        'Torne o processamento idempotente e retorne 2xx rapidamente para manter os retries do provedor saudáveis.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment.completed',
        'provider' => 'oy',
        'data' => [
            'partner_trx_id' => 'PRT-001',
            'amount' => 25000,
            'currency' => 'IDR',
            'status' => 'COMPLETED',
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
            'path' => '/api/webhooks/oy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
