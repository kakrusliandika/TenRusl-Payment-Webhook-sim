<?php

return [

    'hint' => 'Assinatura de callback no estilo MD5/HMAC.',

    'summary' => <<<'TEXT'
A Skrill envia um callback de estado para o seu `status_url` e espera que você valide a mensagem usando `md5sig`, um **MD5 em MAIÚSCULAS** de uma concatenação bem definida de campos (por exemplo: `merchant_id + transaction_id + UPPERCASE(MD5(secret_word)) + mb_amount + mb_currency + status`). Você só deve confiar no payload se o valor calculado por você coincidir com o `md5sig` recebido. A Skrill também suporta um `sha2sig` alternativo (SHA-2 em maiúsculas) mediante solicitação, construído de forma análoga ao `md5sig`.

Na prática, mantenha a validação no back-end (nunca exponha a secret word) e faça o hash dos **valores exatos** tal como foram postados de volta para você. Torne o endpoint idempotente (dedup por transaction ou event ID), retorne 2xx rapidamente após persistir e adie trabalho não crítico. Durante o debugging, registre o resultado da verificação e um hash do corpo, mantendo segredos fora dos logs. Tenha cuidado com formatação—campos de valor e moeda devem ser usados literalmente ao montar a string—para que as comparações sejam estáveis entre retries e ambientes.
TEXT
    ,

    // dari view Skrill
    'docs' => 'https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide_v10.3.pdf',

    'signature_notes' => [
        'Recrie `md5sig` exatamente: concatene os campos documentados (ex.: merchant_id, transaction_id, UPPERCASE(MD5(secret_word)), mb_amount, mb_currency, status) e calcule **MD5 em MAIÚSCULAS**.',
        'Compare com o `md5sig` recebido; opcionalmente use `sha2sig` (SHA-2 em MAIÚSCULAS) se habilitado pela Skrill.',
        'Valide somente no servidor, usando os valores postados exatamente; mantenha o handler idempotente e retorne 2xx rapidamente.',
    ],

    'example_payload' => [
        'transaction_id' => 'SKR-001',
        'mb_amount'      => '10.00',
        'mb_currency'    => 'EUR',
        'status'         => '2',
        'md5sig'         => '<UPPERCASE_MD5>',
        'provider'       => 'skrill',
        'sent_at'        => now()->toIso8601String(),
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
            'path'   => '/api/webhooks/skrill',
            'desc'   => __('pages.receive_webhook'),
        ],
    ],
];
