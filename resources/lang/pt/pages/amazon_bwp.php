<?php

return [

    'hint' => 'x-amzn-signature em cada requisição.',

    'summary' => <<<'TEXT'
Buy with Prime (BWP) assina todos os webhooks para que você possa confirmar que eles realmente se originaram na Amazon e não foram alterados em trânsito. Cada requisição inclui a assinatura digital no cabeçalho `x-amzn-signature`. O seu handler deve reconstruir a assinatura esperada exatamente como documentado pelo BWP para o tipo de evento e ambiente em questão; se os valores não corresponderem, rejeite a chamada. Trate qualquer timestamp/nonce que acompanhe a requisição como parte da sua postura contra ataques de replay, aplicando uma janela de validade rígida e armazenando identificadores já processados para evitar duplicatas.

Do ponto de vista operacional, projete o endpoint para ser rápido e determinístico: verifique primeiro, confirme com um `2xx` assim que estiver seguramente registrado e, então, execute o trabalho mais pesado de forma assíncrona. Se você depende de listas de permissão (allowlists), lembre-se de que IPs e redes podem mudar— a verificação criptográfica é o principal alicerce de confiança. Mantenha uma trilha de auditoria segura (ID da requisição, presença da assinatura, resultado da verificação e um hash do corpo—nunca do segredo). Para testes locais, você pode isolar a etapa de verificação atrás de uma flag de ambiente, garantindo que, em produção, todos os caminhos sempre chequem as assinaturas. Ao rotacionar chaves ou atualizar regras de canonização, faça o rollout com cautela, monitore as taxas de erro e documente com precisão o conjunto de cabeçalhos e a lógica de hashing/canonização que você implementa, para que os demais serviços do seu stack permaneçam em sincronia.

Sob a ótica da ergonomia de integração, exponha **motivos claros de falha** (assinatura inválida, timestamp expirado, requisição malformada) e devolva códigos de erro estáveis para que os retries se comportem de maneira previsível. Combine isso com idempotência em nível de aplicação e proteção contra replay para que as transições de estado de pagamento nos sistemas a jusante permaneçam seguras mesmo diante de retries, picos de tráfego ou falhas parciais.
TEXT
    ,

    // Dari view amazon_bwp.blade.php
    'docs' => 'https://documents.buywithprime.amazon.com/bwp-api/docs/subscribe-to-events',

    'signature_notes' => [
        'Leia `x-amzn-signature` nos cabeçalhos da requisição.',
        'Reconstrua a assinatura esperada exatamente como definida pelo Buy with Prime (algoritmo/canonização na documentação oficial); rejeite em caso de divergência.',
        'Se um timestamp/nonce for fornecido, aplique uma janela de frescor rígida para mitigar ataques de replay e armazene IDs já processados para evitar duplicatas.',
    ],

    'example_payload' => [
        'eventType' => 'ORDER_COMPLETED',
        'data'      => [
            'orderId'  => 'BWP-001',
            'status'   => 'COMPLETED',
            'amount'   => 25000,
            'currency' => 'IDR',
        ],
        'provider' => 'amazon_bwp',
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
            'path'   => '/api/webhooks/amazon_bwp',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
