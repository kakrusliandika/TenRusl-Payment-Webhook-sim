<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site / Brand
    |--------------------------------------------------------------------------
    */
    'site_name' => 'TenRusl',

    /*
    |--------------------------------------------------------------------------
    | Global Navigation
    |--------------------------------------------------------------------------
    */
    'home_title'      => '首页',
    'providers_title' => '服务商',
    'features'        => '功能',
    'endpoints'       => '端点',
    'signature'       => '签名',
    'tooling'         => '工具',
    'openapi'         => 'OpenAPI',
    'github'          => 'GitHub',

    /*
    |--------------------------------------------------------------------------
    | Accessibility / ARIA
    |--------------------------------------------------------------------------
    */
    'aria' => [
        'primary_nav'  => '主导航',
        'utility_nav'  => '辅助导航',
        'toggle_menu'  => '切换主导航菜单',
        'toggle_theme' => '切换明暗主题',
        'skip_to_main' => '跳转到主要内容',
        'language'     => '更改界面语言',
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer / Legal
    |--------------------------------------------------------------------------
    */
    'terms'       => '条款',
    'privacy'     => '隐私',
    'cookies'     => 'Cookie',
    'footer_demo' => '用于学习、测试和讲解现代 webhook-first 流程的支付架构演示环境。',
    'build'       => '构建',

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults (override per page via layout/seo.blade.php)
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'default_title'       => '支付 Webhook 模拟器',
        'default_description' => '在不暴露或依赖真实支付网关凭据的情况下，模拟真实支付流程：支持幂等操作、Webhook 签名验证、事件去重以及真实的重试/退避行为。',
        'default_image_alt'   => '支付 Webhook 模拟器架构示意图，展示支付服务商、Webhook 回调、重试以及状态更新。',
    ],

    /*
    |--------------------------------------------------------------------------
    | Home (Landing)
    |--------------------------------------------------------------------------
    */
    'home' => [
        'title'       => '支付 Webhook 模拟器',
        'description' => '在安全且接近生产环境的 sandbox 中稳定你的支付集成。在真正指向支付网关之前，先演练 Idempotency-Key 处理、签名验证、事件去重以及带退避的重试逻辑。',
        'hero' => [
            'heading_prefix' => 'PWS',
            'heading_emph'   => '幂等',
            'lede'           => '从多个服务商模拟接收支付事件，验证原始请求体签名，去重嘈杂的重试请求，并端到端观察真实的退避策略——无需触碰真实凭据或生产 Webhook。',
            'cta_docs'       => 'OpenAPI 文档',
            'cta_features'   => '查看功能',
            'cta_github'     => '在 GitHub 上查看源码',
            'stats' => [
                'providers' => ['label' => '模拟服务商'],
                'tests'     => ['label' => '自动化测试'],
                'openapi'   => ['label' => '已文档化端点'],
            ],
            'chip'           => 'POST : /api/webhooks/mock 携带签名的 JSON 负载',
            'simulate'       => '在接入生产前，先用幂等性、签名校验和重试逻辑来模拟真实支付 Webhook。',
        ],
        'sections' => [
            'features' => [
                'title' => '功能',
                'lede'  => '通过真实且可重复的模拟，让你的支付集成更加健壮稳定，贴近现代支付网关在生产环境中发送、签名和重试 Webhook 事件的行为。',
                'ship' => '在不使用真实网关凭据的情况下交付更安全的支付流程。',
                'items' => [
                    [
                        'title' => '幂等性',
                        'desc'  => '正确演练 Idempotency-Key 的处理，让重复请求、重放以及客户端重试都归并到单一且一致的支付记录，而不会破坏系统状态。',
                    ],
                    [
                        'title' => '签名验证',
                        'desc'  => '使用 HMAC、时间戳和共享密钥验证原始请求体及服务商头信息，为你在上线前打磨签名验证逻辑提供一个安全环境。',
                    ],
                    [
                        'title' => '去重与重试',
                        'desc'  => '模拟重复 Webhook 投递和指数退避，让你可以设计出幂等、健壮且可安全多次执行而无副作用的处理器。',
                    ],
                    [
                        'title' => 'OpenAPI',
                        'desc'  => '浏览交互式 OpenAPI 文档，了解每一个支付与 Webhook 端点的定义，包括模式、示例以及可直接运行的 curl 片段。',
                    ],
                    [
                        'title' => 'Postman',
                        'desc'  => '导入精心整理的 Postman 集合，调用端点、调整负载，并从你常用的 API 客户端演练各种错误场景。',
                    ],
                    [
                        'title' => 'CI 集成',
                        'desc'  => '把模拟器接入 CI 流水线，让测试、linter 与契约校验在每一次 push 时自动运行，在问题进入生产前捕获集成回归。',
                    ],
                ],
            ],
            'endpoints' => [
                'title' => '端点',
                'lede'  => '定义一组小而真实的接口表面：创建支付、轮询状态，并以幂等语义接收服务商风格的 Webhook。',
                'cards' => [
                    [
                        'title' => 'POST /api/payments',
                        'desc'  => '创建一条新的模拟支付记录。该端点需要 Idempotency-Key 请求头，以便你验证应用如何对重复的支付尝试进行去重。',
                    ],
                    [
                        'title' => 'GET /api/payments/{id}',
                        'desc'  => '获取指定支付的最新状态，包括状态流转以及模拟器已处理的相关 Webhook 事件。',
                    ],
                    [
                        'title' => 'POST /api/webhooks/{provider}',
                        'desc'  => '为指定服务商（mock、xendit、midtrans 等）接收 Webhook 回调，使用贴合真实的负载、头信息和签名方案并针对各个集成做了适配。',
                    ],
                ],
            ],
            'providers' => [
                'title' => '服务商',
                'lede'  => '在同一个 sandbox 中体验来自多个支付网关的真实 Webhook 流程，无需使用真实凭据。',
                'cta_all' => '查看全部服务商',
                'map'   => [
                    'mock'         => 'Mock',
                    'xendit'       => 'Xendit',
                    'midtrans'     => 'Midtrans',
                    'stripe'       => 'Stripe',
                    'paypal'       => 'PayPal',
                    'paddle'       => 'Paddle',
                    'lemonsqueezy' => 'Lemon Squeezy',
                    'airwallex'    => 'Airwallex',
                    'tripay'       => 'Tripay',
                    'doku'         => 'DOKU',
                    'dana'         => 'DANA',
                    'oy'           => 'OY!',
                    'payoneer'     => 'Payoneer',
                    'skrill'       => 'Skrill',
                    'amazon_bwp'   => 'Amazon BWP',
                ],
            ],
            'signature' => [
                'title' => '签名验证',
                'lede'  => '使用 HMAC 签名、时间戳以及严格的头部检查来验证原始 Webhook 负载。',
                'compare'  => '将服务商发送的签名与你基于原始请求体、共享密钥和时间戳计算出的签名进行常数时间比较。',
                'reject'    => '自动拒绝签名不匹配或时间戳过期的请求。',
                'cards' => [
                    [
                        'title' => 'HMAC / 时间戳',
                        'desc'  => '尝试使用带时间戳的 HMAC 签名来抵御重放攻击。观察原始内容哈希、共享密钥和签名头如何组合成每个 Webhook 事件可验证的审计轨迹。',
                    ],
                    [
                        'title' => '基于头部',
                        'desc'  => '处理各服务商特有的头部和令牌，从简单的 bearer 密钥到结构化签名包，确保应用可以拒绝伪造负载，同时保持合法事件顺畅流转。',
                    ],
                ],
            ],
            'tooling' => [
                'title' => '工具',
                'work'  => '适配你的本地开发栈',
                'cards' => [
                    [
                        'title' => 'OpenAPI',
                        'desc'  => '使用内置 OpenAPI 浏览器查看模式、生成示例请求并在浏览器中尝试端点，方便与你的团队分享和记录支付流程。',
                    ],
                    [
                        'title' => 'Postman',
                        'desc'  => '克隆 Postman 集合以运行脚本化流程、参数化环境，并快速比较不同服务商在同一请求序列下的行为。',
                    ],
                    [
                        'title' => 'CI',
                        'desc'  => '把模拟器集成进持续集成任务，让每个分支和 pull request 都在你期望的相同支付与 Webhook 场景下获得验证。',
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers (Catalog + Search)
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'title'       => '服务商',
        'description' => '浏览已支持的支付服务商，查看其 Webhook 负载格式，并对比各自的签名方案，以便设计出一致且与具体服务商解耦的集成层。',
        'map' => [
            'mock'         => 'Mock',
            'xendit'       => 'Xendit',
            'midtrans'     => 'Midtrans',
            'stripe'       => 'Stripe',
            'paypal'       => 'PayPal',
            'paddle'       => 'Paddle',
            'lemonsqueezy' => 'Lemon Squeezy',
            'airwallex'    => 'Airwallex',
            'tripay'       => 'Tripay',
            'doku'         => 'DOKU',
            'dana'         => 'DANA',
            'oy'           => 'OY!',
            'payoneer'     => 'Payoneer',
            'skrill'       => 'Skrill',
            'amazon_bwp'   => 'Amazon BWP',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pluralization / Examples
    |--------------------------------------------------------------------------
    */
    'plurals' => [
        'tests' => '{0} 尚未定义任何测试|{1} :count 个自动化测试|[2,*] :count 个测试',
        'items' => '{0} 没有可用的项目|{1} :count 个项目|[2,*] :count 个项目',
    ],
];
