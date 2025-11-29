<?php

return [

    'hint' => 'Validação de signature_key.',

    'summary' => <<<'TEXT'
A Midtrans inclui um `signature_key` calculado em cada notificação HTTP(S) para que você consiga verificar a origem antes de processar. A fórmula é explícita e estável:
    SHA512(order_id + status_code + gross_amount + ServerKey)
Monte a string de entrada usando os valores exatos do corpo da notificação (como strings) e o seu `ServerKey` privado, depois calcule o hex digest do SHA-512 e compare com o `signature_key` usando comparação constant-time (timing-safe). Se a verificação falhar, descarte a notificação. Para mensagens genuínas, use os campos documentados (por exemplo, `transaction_status`) para conduzir sua máquina de estados — reconheça rapidamente (2xx), enfileire o trabalho pesado e faça atualizações idempotentes para lidar com retries ou entregas fora de ordem.

Dois problemas comuns: formatação e coerção. Mantenha `gross_amount` exatamente como foi recebido (não localize, não altere casas decimais) ao construir a string e evite trim ou mudanças de whitespace/quebra de linha. Armazene uma chave de deduplicação por evento ou por pedido para se proteger contra condições de corrida; registre o resultado da verificação e um hash do body para auditoria sem vazar segredos. Combine isso com rate limiting no endpoint e códigos de falha claros para que o monitoramento diferencie erros temporários (passíveis de retry) de rejeições permanentes (assinatura inválida).
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'Pegue `order_id`, `status_code`, `gross_amount` do body (como strings) e anexe seu `ServerKey`.',
        'Calcule `SHA512(order_id + status_code + gross_amount + ServerKey)` e compare com `signature_key` (constant-time).',
        'Rejeite em caso de mismatch; caso contrário, atualize o estado a partir de `transaction_status`. Mantenha idempotência e retorne 2xx rapidamente.',
        'Cuidado com mudanças de formatação em `gross_amount` e com whitespace acidental ao concatenar.',
    ],

    'example_payload' => [
        'order_id' => 'ORDER-001',
        'status_code' => '200',
        'gross_amount' => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key' => '<sha512>',
        'provider' => 'midtrans',
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
            'path' => '/api/webhooks/midtrans',
            'desc' => __('pages.receive_webhook'),
        ],
    ],
];
