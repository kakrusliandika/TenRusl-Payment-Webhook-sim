<?php

return [

    'hint' => 'Header de assinatura com timestamp.',

    'summary' => <<<'TEXT'
A Stripe assina todas as requisições de webhook e expõe a assinatura calculada no header `Stripe-Signature`. O seu endpoint deve verificar a requisição antes de executar qualquer trabalho. Com as bibliotecas oficiais da Stripe, passe três entradas para a rotina de verificação: o raw request body exatamente como recebido, o header `Stripe-Signature` e o secret do endpoint. Só prossiga quando a verificação for bem-sucedida; caso contrário, retorne um non-2xx e interrompa o processamento. Quando não for possível usar uma biblioteca oficial, implemente a verificação manual conforme documentado, incluindo checagens de tolerância de timestamp para reduzir o risco de replay.

Trate a verificação de assinatura como um gate estrito. Mantenha o handler idempotente (armazene IDs de eventos), responda com 2xx rapidamente após persistir e empurre trabalho pesado para jobs em background. Garanta que o seu framework forneça os **raw bytes** — evite re-serializar o JSON antes de hashear, porque qualquer mudança em espaços ou ordem quebra a verificação. Por fim, registre diagnósticos mínimos (resultado da verificação, tipo do evento, hash do body — sem segredos) e monitore falhas durante rotação de secrets ou mudanças no endpoint.
TEXT
    ,

    // dari view Stripe
    'docs' => 'https://docs.stripe.com/webhooks',

    'signature_notes' => [
        'Leia o header `Stripe-Signature`; obtenha o endpoint secret no dashboard da Stripe.',
        'Verifique com bibliotecas oficiais passando: raw request body, `Stripe-Signature` e endpoint secret.',
        'Se verificar manualmente, imponha tolerância de timestamp para reduzir replay e compare assinaturas com função timing-safe.',
        'Aceite apenas no sucesso; armazene IDs de eventos para idempotência e retorne 2xx rapidamente após persistir.',
    ],

    'example_payload' => [
        'id' => 'evt_'.now()->timestamp,
        'type' => 'payment_intent.succeeded',
        'data' => [
            'object' => [
                'id' => 'pi_01H',
                'status' => 'succeeded',
            ],
        ],
        'provider' => 'stripe',
        'created_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/stripe',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
