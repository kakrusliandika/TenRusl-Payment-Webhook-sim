<?php

return [

    'hint' => 'Cabeçalho de assinatura HMAC.',

    'summary' => <<<'TEXT'
O Lemon Squeezy assina cada webhook com um HMAC simples sobre o **corpo bruto (raw) da requisição**. O remetente usa o seu “signing secret” do webhook para gerar um HMAC SHA-256 em formato de **hex digest**; esse digest é enviado no cabeçalho `X-Signature`. Sua tarefa é ler os bytes do corpo exatamente como foram recebidos (sem re-stringify e sem alterar espaços em branco), calcular o mesmo HMAC com o seu secret, produzir o resultado como string **hex** e comparar com `X-Signature` usando uma função constant-time (timing-safe). Se os valores diferirem — ou se o cabeçalho estiver ausente — rejeite a requisição antes de executar qualquer lógica de negócio.

Como muitos frameworks fazem parse do body antes de você conseguir hasheá-lo, garanta que a sua rota tenha acesso aos bytes brutos (por exemplo, configure tratamento de “raw body” no Node/Express). Trate a verificação como um gate: só após passar você deve parsear o JSON e atualizar o estado. Faça o handler ser idempotente para que retries/duplicatas não apliquem efeitos colaterais duas vezes e registre apenas diagnósticos mínimos (tamanho do cabeçalho recebido, resultado da verificação, id do evento), nunca segredos. Para testes locais, use os eventos de teste do Lemon Squeezy e simule falhas para confirmar o comportamento de retry/backoff. Documente o fluxo ponta a ponta — verificação, deduplicação e processamento assíncrono — para que o time consiga reproduzir resultados consistentes em diferentes ambientes.
TEXT
    ,

    // dari view Lemon Squeezy
    'docs' => 'https://docs.lemonsqueezy.com/help/webhooks/signing-requests',

    'signature_notes' => [
        'Leia `X-Signature` (HMAC-SHA256 em **hex** do raw body) e obtenha os bytes brutos da requisição.',
        'Calcule o HMAC em hex usando seu signing secret e compare com uma função segura contra timing.',
        'Rejeite em caso de mismatch/cabeçalho ausente; só parseie JSON após a verificação passar.',
        'Garanta que o framework forneça o raw body (sem re-serialização); torne o handler idempotente e faça log de diagnósticos mínimos.',
    ],

    'example_payload' => [
        'meta' => ['event_name' => 'order_created'],
        'data' => ['id' => 'ord_001', 'status' => 'paid'],
        'provider' => 'lemonsqueezy',
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
            'path' => '/api/webhooks/lemonsqueezy',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
