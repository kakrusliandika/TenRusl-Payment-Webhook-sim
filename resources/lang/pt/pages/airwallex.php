<?php

return [

    'hint' => 'HMAC-SHA256 sobre x-timestamp + body.',

    'summary' => <<<'TEXT'
Os webhooks da Airwallex são assinados para que você possa verificar a autenticidade e a integridade antes de tocar no banco de dados. Cada requisição inclui dois cabeçalhos críticos: `x-timestamp` e `x-signature`. Para validar uma mensagem, leia o corpo HTTP bruto exatamente como foi recebido, concatene o valor de `x-timestamp` (como string) com esse corpo bruto para formar a entrada do digest e, em seguida, calcule um HMAC usando SHA-256 com o segredo compartilhado (shared secret) da URL de notificação como chave. A Airwallex espera o resultado como um **hex digest**; compare esse valor com o cabeçalho `x-signature` usando uma comparação em tempo constante (constant-time) para evitar vazamentos de tempo. Se as assinaturas não coincidirem, ou se o timestamp estiver ausente ou inválido, falhe de forma segura (fail closed) e retorne uma resposta não 2xx.

Como replays são um risco real em qualquer sistema de webhooks, aplique uma janela de validade (freshness window) ao `x-timestamp`. Rejeite mensagens antigas demais ou muito adiantadas no futuro e armazene os IDs dos eventos já processados para deduplicar efeitos colaterais posteriores (idempotência na camada da aplicação). Considere o payload como não confiável até que a verificação seja concluída com sucesso; não faça stringify do JSON novamente antes de aplicar o hash — use exatamente os mesmos bytes brutos recebidos para evitar diferenças sutis de espaços em branco ou ordenação. Quando a verificação for bem-sucedida, responda rapidamente com um `2xx`; faça o trabalho pesado de forma assíncrona para manter a lógica de retries saudável e reduzir duplicatas acidentais.

Para fluxos locais e de CI, a Airwallex oferece ferramentas de primeira linha: configure suas URLs de notificação no dashboard, visualize payloads de exemplo e **envie eventos de teste** para o seu endpoint. Ao depurar, registre o `x-timestamp` recebido, uma pré-visualização da assinatura calculada (sem nunca registrar segredos) e qualquer identificador de evento, se presente. Se você rotacionar a chave secreta, faça o rollout com cuidado e monitore a taxa de erros de assinatura. Por fim, documente toda a cadeia — verificação, deduplicação, retries e respostas de erro — para que outras pessoas do time possam reproduzir os resultados com as mesmas regras de hash sobre o corpo bruto e com a mesma janela de tempo.
TEXT
    ,

    'docs' => 'https://www.airwallex.com/docs/developer-tools/webhooks/listen-for-webhook-events',

    'signature_notes' => [
        'Extraia `x-timestamp` e `x-signature` dos cabeçalhos.',
        'Monte value_to_digest = <x-timestamp> + <corpo HTTP bruto> (bytes exatos).',
        'Calcule expected = HMAC-SHA256(value_to_digest, <webhook secret>) em HEX; compare com `x-signature` usando comparação em tempo constante.',
        'Rejeite se as assinaturas não coincidirem ou se o timestamp estiver vencido; também deduplique IDs de eventos já processados para garantir idempotência.',
    ],

    'example_payload' => [
        'id'       => 'evt_' . now()->timestamp,
        'type'     => 'payment_intent.succeeded',
        'data'     => [
            'payment_intent_id' => 'pi_awx_001',
            'amount'            => 25000,
            'currency'          => 'IDR',
            'status'            => 'succeeded',
        ],
        'provider'   => 'airwallex',
        'created_at' => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/airwallex',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
