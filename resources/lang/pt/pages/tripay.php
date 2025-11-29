<?php

return [

    'hint' => 'X-Callback-Signature.',

    'summary' => <<<'TEXT'
A TriPay entrega callbacks para a URL que você configurar e inclui headers que identificam o evento e ajudam a autenticar o remetente. Em particular, os callbacks carregam `X-Callback-Event` com um valor como `payment_status` e `X-Callback-Signature` para validação de assinatura conforme especificado na documentação da TriPay. Seu consumer deve ler esses headers, verificar a autenticidade da requisição e só então atualizar o estado interno.

Projete o endpoint para ser rápido e idempotente. Use uma janela curta de validade se houver timestamps/nonces e mantenha um armazenamento leve de deduplicação com base em referência ou identificadores de evento. Retorne 2xx rapidamente assim que o evento for registrado e trate efeitos colaterais de forma assíncrona. Para transparência e resposta a incidentes, mantenha uma trilha de auditoria com horário de recebimento, metadados do evento e resultado da verificação, sem registrar segredos.
TEXT
    ,

    // dari view Tripay
    'docs' => 'https://tripay.co.id/developer',

    'signature_notes' => [
        'Inspecione `X-Callback-Event` (ex.: `payment_status`) e `X-Callback-Signature`.',
        'Valide a assinatura conforme a documentação da TriPay; rejeite se houver divergência ou header ausente.',
        'Mantenha o processamento idempotente (dedup por referência/event ID) e reconheça rapidamente (2xx).',
    ],

    'example_payload' => [
        'reference' => 'TRX-001',
        'status' => 'PAID',
        'amount' => 125000,
        'currency' => 'IDR',
        'provider' => 'tripay',
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
            'path' => '/api/webhooks/tripay',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
