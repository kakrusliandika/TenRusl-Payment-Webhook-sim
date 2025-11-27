<?php

return [

    'hint' => 'Assinatura de token de callback.',

    'summary' => <<<'TEXT'
A Xendit “assina” eventos de webhook usando um token por conta exposto no header `x-callback-token`. A sua integração deve comparar esse header com o token obtido no dashboard da Xendit e rejeitar qualquer requisição com token ausente ou diferente. Alguns produtos de webhook também incluem um `webhook-id`, que você pode armazenar para evitar processamento duplicado em caso de retries.

Operacionalmente, mantenha a verificação como o primeiro passo, persista um registro imutável do evento, reconheça com 2xx prontamente e mova o trabalho pesado para filas. Garanta idempotência usando `webhook-id` (ou sua própria chave) e aplique uma janela de tempo estreita se houver metadados de timestamp. Documente o fluxo completo (verificação, deduplicação, retries e códigos de erro) para que equipe e serviços integrem de forma consistente em todos os ambientes.
TEXT
    ,

    // dari view Xendit
    'docs' => 'https://docs.xendit.co/docs/handling-webhooks',

    'signature_notes' => [
        'Compare `x-callback-token` com seu token único do dashboard da Xendit; rejeite em caso de divergência.',
        'Use `webhook-id` (quando presente) para deduplicar; trate a verificação como um gate rígido antes de parsear JSON.',
        'Responda 2xx rapidamente e adie o trabalho pesado; registre diagnósticos mínimos sem expor segredos.',
    ],

    'example_payload' => [
        'id'       => 'evt_xnd_' . now()->timestamp,
        'event'    => 'invoice.paid',
        'data'     => [
            'id'     => 'inv_001',
            'status' => 'PAID',
        ],
        'provider' => 'xendit',
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
            'path'   => '/api/webhooks/xendit',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
