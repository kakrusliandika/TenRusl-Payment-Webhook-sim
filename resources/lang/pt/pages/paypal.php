<?php

return [

    'hint' => 'API de verificação de assinatura de Webhook.',

    'summary' => <<<'TEXT'
O PayPal exige a verificação server-side de cada webhook por meio da API oficial Verify Webhook Signature. O seu listener deve extrair os headers enviados com a notificação—`PAYPAL-TRANSMISSION-ID`, `PAYPAL-TRANSMISSION-TIME`, `PAYPAL-CERT-URL` e `PAYPAL-TRANSMISSION-SIG`—além do seu `webhook_id` e do corpo **bruto** da requisição (`webhook_event`). Envie esses dados ao endpoint de verificação e aceite o evento somente se o PayPal retornar um resultado de sucesso. Isso substitui mecanismos antigos de verificação e facilita a consistência entre produtos REST.

Construa o consumer como um gate rápido e idempotente: verifique primeiro, persista um registro do evento, responda com 2xx e envie o trabalho pesado para uma fila. Use comparação constant-time para verificações locais e mantenha os bytes brutos intactos ao encaminhar para o PayPal, evitando bugs sutis de re-serialização. Aplique uma tolerância de tempo curta em torno de `PAYPAL-TRANSMISSION-TIME` para reduzir janela de replay e registre apenas dados mínimos de auditoria (request ID, resultado da verificação, hash do body—sem segredos). Com esse padrão, entregas duplicadas e falhas parciais não causarão processamento duplo, e sua trilha de auditoria seguirá confiável em incidentes.
TEXT
    ,

    // Dari view PayPal
    'docs' => 'https://developer.paypal.com/docs/api/webhooks/v1/',

    'signature_notes' => [
        'Colete os headers: PAYPAL-TRANSMISSION-ID, PAYPAL-TRANSMISSION-TIME, PAYPAL-CERT-URL, PAYPAL-TRANSMISSION-SIG; preserve o body bruto.',
        'Chame a Verify Webhook Signature API com esses valores mais webhook_id e webhook_event; aceite apenas em caso de sucesso.',
        'Trate a verificação como um gate; imponha tolerância curta para mitigar replay e torne o consumer idempotente.',
        'Retorne 2xx rapidamente, enfileire trabalho pesado e registre diagnósticos mínimos (sem segredos).',
    ],

    'example_payload' => [
        'id'          => 'WH-' . now()->timestamp,
        'event_type'  => 'PAYMENT.CAPTURE.COMPLETED',
        'resource'    => [
            'id'     => 'CAP-001',
            'status' => 'COMPLETED',
        ],
        'provider'    => 'paypal',
        'create_time' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/paypal',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
