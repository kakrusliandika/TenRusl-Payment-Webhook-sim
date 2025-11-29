<?php

return [

    'hint' => 'Проверка signature_key.',

    'summary' => <<<'TEXT'
Midtrans включает вычисленный `signature_key` в каждое HTTP(S)-уведомление, чтобы вы могли проверить источник перед обработкой. Формула явная и стабильная:
    SHA512(order_id + status_code + gross_amount + ServerKey)
Соберите входную строку, используя точные значения из тела уведомления (как строки) и ваш приватный `ServerKey`, затем вычислите SHA-512 hex digest и сравните с `signature_key` через constant-time сравнение. Если проверка не проходит, игнорируйте уведомление. Для подлинных сообщений используйте документированные поля (например, `transaction_status`) для управления вашей машиной состояний — быстро подтверждайте (2xx), отправляйте тяжёлую работу в очередь и делайте обновления идемпотентными на случай ретраев или доставки вне порядка.

Две распространённые ошибки: форматирование и приведение типов. Сохраняйте `gross_amount` ровно в том виде, как он пришёл (не локализуйте и не меняйте десятичные разряды) при формировании строки, и избегайте trim или изменений пробелов/переводов строк. Храните ключ дедупликации на событие или на заказ, чтобы защититься от гонок; логируйте результат проверки и хэш тела для аудита без утечки секретов. Дополните это rate limiting на endpoint и понятными кодами ошибок, чтобы мониторинг отличал временные сбои (подлежащие повтору) от постоянных отказов (невалидная подпись).
TEXT
    ,

    // dari view midtrans
    'docs' => 'https://docs.midtrans.com/docs/https-notification-webhooks',

    'signature_notes' => [
        'Возьмите `order_id`, `status_code`, `gross_amount` из тела (как строки) и добавьте ваш `ServerKey`.',
        'Вычислите `SHA512(order_id + status_code + gross_amount + ServerKey)` и сравните с `signature_key` (constant-time).',
        'При несовпадении отклоняйте; иначе обновляйте состояние по `transaction_status`. Делайте обработку идемпотентной и быстро возвращайте 2xx.',
        'Остерегайтесь изменений формата `gross_amount` и лишних пробелов при конкатенации.',
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
