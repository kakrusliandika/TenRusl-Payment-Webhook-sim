<?php

return [

    'hint' => 'Notificações específicas do produto.',

    'summary' => <<<'TEXT'
O Payoneer Checkout entrega notificações assíncronas (webhooks) para um endpoint que você controla, para que o seu sistema possa reconciliar o estado do pagamento com segurança fora do navegador do usuário. A plataforma permite definir uma URL dedicada de notificação e escolher o estilo de entrega que melhor se adapta ao seu stack — POST (recomendado) ou GET, com JSON ou parâmetros codificados como formulário. Como o conjunto exato de parâmetros e os padrões de assinatura/autenticação são específicos do produto, trate as notificações da Payoneer como uma superfície de integração: documente os headers/campos que identificam o evento, inclua metadados anti-replay quando disponíveis e verifique a autenticidade antes de alterar o estado.

Operacionalmente, comece isolando um handler estreito e idempotente que persista um registro de evento imutável e retorne 2xx rapidamente. Mantenha a lógica de negócio pesada em workers em background para evitar tempestades de retry. Aplique chaves de deduplicação e imponha uma janela de frescor para qualquer timestamp/nonce, protegendo contra replays ou entrega fora de ordem. Quando precisar de maior garantia, anexe um token emitido pelo provedor (ou o seu próprio segredo aleatório) na URL de notificação e valide no servidor. Por fim, publique um runbook para o time documentando endpoints, formatos, códigos de falha e os passos exatos de verificação que você implementa para a sua variante de produto Payoneer — e mantenha isso versionado junto com o código.
TEXT
    ,

    // Dari view Payoneer
    'docs' => 'https://checkoutdocs.payoneer.com/docs/create-notification-endpoints',

    'signature_notes' => [
        'Exponha um endpoint dedicado de notificação (POST recomendado); aceite JSON ou dados de formulário.',
        'Valide a autenticidade conforme documentado para a sua variante de produto (token ou campos de assinatura); rejeite em caso de divergência.',
        'Imponha frescor de timestamp/nonce quando disponível e torne o processamento idempotente (armazene uma chave de deduplicação).',
        'ACK rápido (2xx) e delegue trabalho pesado para jobs em background; mantenha trilha de auditoria sem logar segredos.',
    ],

    'example_payload' => [
        'event'     => 'checkout.transaction.completed',
        'provider'  => 'payoneer',
        'data'      => [
            'orderId'  => 'PO-001',
            'amount'   => 25000,
            'currency' => 'IDR',
            'status'   => 'COMPLETED',
        ],
        'sent_at'   => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/payoneer',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
