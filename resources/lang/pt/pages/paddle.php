<?php

return [

    'hint' => 'Assinatura com chave pública (Classic) / segredo (Billing).',

    'summary' => <<<'TEXT'
O Paddle Billing assina cada webhook com o header `Paddle-Signature`, que inclui um timestamp Unix (`ts`) e uma assinatura (`h1`). Para verificar manualmente, concatene o timestamp, dois-pontos e o raw request body exatamente como recebido para montar o signed payload; em seguida, calcule o HMAC-SHA256 usando o segredo da sua notification destination e compare com `h1` usando uma função constant-time (timing-safe). O Paddle gera um segredo separado por notification destination — trate-o como uma senha e mantenha-o fora do controlo de versões.

Prefira usar os SDKs oficiais ou um middleware de verificação próprio antes de qualquer parsing. Como timing e transformações do body são armadilhas comuns, garanta que o seu framework exponha os bytes crus (por exemplo, Express `express.raw({ type: 'application/json' })`) e imponha uma tolerância curta para `ts` para desencorajar replay. Após a verificação, responda rapidamente (2xx), armazene o event ID para idempotência e mova o trabalho pesado para jobs em background. Isso mantém a entrega confiável e evita efeitos colaterais duplicados sob retries.

Ao migrar do Paddle Classic, observe que a verificação passou de assinaturas por chave pública para HMAC baseado em segredo no Billing. Atualize runbooks e a gestão de segredos e monitore métricas de verificação durante o rollout. Logs claros (sem segredos) e respostas de erro determinísticas simplificam bastante o tratamento de incidentes e o suporte a parceiros.
TEXT
    ,

    // Dari view Paddle
    'docs' => 'https://developer.paddle.com/webhooks/signature-verification',

    'signature_notes' => [
        'Leia o header `Paddle-Signature`; extraia os valores `ts` e `h1`.',
        'Monte o signed payload = `ts + ":" + <raw request body>`; gere o HMAC com o segredo do endpoint.',
        'Compare o seu valor com `h1` usando uma função timing-safe; imponha uma tolerância curta para `ts` para prevenir replay.',
        'Prefira SDKs oficiais ou middleware de verificação; só faça parsing de JSON após a verificação passar.',
    ],

    'example_payload' => [
        'event_id' => 'evt_'.now()->timestamp,
        'event_type' => 'transaction.completed',
        'provider' => 'paddle',
        'data' => [
            'transaction_id' => 'txn_001',
            'amount' => 25000,
            'currency_code' => 'IDR',
            'status' => 'completed',
        ],
        'occurred_at' => now()->toIso8601String(),
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
            'path' => '/api/webhooks/paddle',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
