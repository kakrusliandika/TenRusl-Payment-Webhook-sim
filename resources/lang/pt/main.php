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
    'home_title'      => 'Início',
    'providers_title' => 'Provedores',
    'features'        => 'Funcionalidades',
    'endpoints'       => 'Endpoints',
    'signature'       => 'Assinatura',
    'tooling'         => 'Ferramentas',
    'openapi'         => 'OpenAPI',
    'github'          => 'GitHub',

    /*
    |--------------------------------------------------------------------------
    | Accessibility / ARIA
    |--------------------------------------------------------------------------
    */
    'aria' => [
        'primary_nav'  => 'Navegação principal',
        'utility_nav'  => 'Navegação utilitária',
        'toggle_menu'  => 'Alternar o menu de navegação principal',
        'toggle_theme' => 'Alternar entre tema claro e escuro',
        'skip_to_main' => 'Ir diretamente para o conteúdo principal',
        'language'     => 'Alterar o idioma da interface',
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer / Legal
    |--------------------------------------------------------------------------
    */
    'terms'       => 'Termos',
    'privacy'     => 'Privacidade',
    'cookies'     => 'Cookies',
    'footer_demo' => 'Ambiente de demonstração de arquitetura de pagamentos para aprender, testar e explicar fluxos modernos baseados em webhooks.',
    'build'       => 'Build',

    /*
    |--------------------------------------------------------------------------
    | SEO Defaults (override per page via layout/seo.blade.php)
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'default_title'       => 'Simulador de Webhooks de Pagamento',
        'default_description' => 'Simule fluxos de pagamento do mundo real com operações idempotentes, assinaturas de webhooks verificadas, deduplicação de eventos e comportamento realista de repetição/backoff — tudo sem expor nem depender de credenciais reais de gateways de pagamento.',
        'default_image_alt'   => 'Diagrama da arquitetura do Simulador de Webhooks de Pagamento mostrando provedores de pagamento, callbacks de webhooks, novas tentativas e atualizações de status.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Home (Landing)
    |--------------------------------------------------------------------------
    */
    'home' => [
        'title'       => 'Simulador de Webhooks de Pagamento',
        'description' => 'Estabilize a sua integração de pagamentos em um ambiente sandbox seguro, semelhante à produção. Exercite o tratamento de Idempotency-Key, a verificação de assinaturas, a deduplicação de eventos e as repetições com backoff antes de apontar requisições para um gateway de pagamento real.',
        'hero' => [
            'heading_prefix' => 'PWS',
            'heading_emph'   => 'Idempotente',
            'lede'           => 'Simule eventos de pagamento recebidos de múltiplos provedores, verifique assinaturas sobre o corpo bruto da requisição, deduplicate tentativas ruidosas e observe estratégias de backoff realistas de ponta a ponta — sem tocar em credenciais reais nem webhooks de produção.',
            'cta_docs'       => 'Referência OpenAPI',
            'cta_features'   => 'Explorar funcionalidades',
            'cta_github'     => 'Ver código no GitHub',
            'stats' => [
                'providers' => ['label' => 'Provedores simulados'],
                'tests'     => ['label' => 'Testes automatizados'],
                'openapi'   => ['label' => 'Endpoints documentados'],
            ],
            'chip'           => 'POST : /api/webhooks/mock com um payload JSON assinado',
            'simulate'       => 'Simule webhooks de pagamento reais com idempotência, verificações de assinatura e lógica de repetição — antes de chegar à produção.',
        ],
        'sections' => [
            'features' => [
                'title' => 'Funcionalidades',
                'lede'  => 'Torne sua integração de pagamentos mais robusta e estável com simulações realistas e repetíveis que refletem como gateways modernos enviam, assinam e repetem eventos de webhooks em produção.',
                'ship' => 'Entregue fluxos de pagamento mais seguros sem tocar em credenciais reais do gateway.',
                'items' => [
                    [
                        'title' => 'Idempotência',
                        'desc'  => 'Exercite o tratamento correto de Idempotency-Key para que requisições duplicadas, replays e repetições do cliente resultem em um único registro de pagamento consistente, em vez de corromper o estado.',
                    ],
                    [
                        'title' => 'Verificação de Assinatura',
                        'desc'  => 'Valide o corpo bruto da requisição e os cabeçalhos do provedor usando HMAC, carimbos de tempo e segredos compartilhados, oferecendo um ambiente seguro para aperfeiçoar sua lógica de verificação de assinaturas antes do go live.',
                    ],
                    [
                        'title' => 'Deduplicação e Repetição',
                        'desc'  => 'Simule entregas duplicadas de webhooks e backoff exponencial para projetar handlers idempotentes e resilientes, seguros para serem executados várias vezes sem efeitos colaterais.',
                    ],
                    [
                        'title' => 'OpenAPI',
                        'desc'  => 'Navegue por documentação OpenAPI interativa que descreve cada endpoint de pagamento e de webhook, com esquemas, exemplos e snippets de curl prontos para uso.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc'  => 'Importe a coleção Postman curada para chamar endpoints, ajustar payloads e ensaiar condições de erro a partir do seu cliente de API preferido em poucos cliques.',
                    ],
                    [
                        'title' => 'Integração com CI',
                        'desc'  => 'Conecte o simulador às suas pipelines de CI para que testes, linters e verificações de contrato sejam executados a cada push, capturando regressões de integração muito antes de chegar à produção.',
                    ],
                ],
            ],
            'endpoints' => [
                'title' => 'Endpoints',
                'lede'  => 'Defina uma superfície pequena e realista: crie pagamentos, consulte o status e receba webhooks no estilo dos provedores com semântica idempotente.',
                'cards' => [
                    [
                        'title' => 'POST /api/payments',
                        'desc'  => 'Cria um novo registro de pagamento simulado. Este endpoint exige o cabeçalho Idempotency-Key para que você possa verificar como sua aplicação deduplica tentativas de pagamento repetidas.',
                    ],
                    [
                        'title' => 'GET /api/payments/{id}',
                        'desc'  => 'Busca o estado mais recente de um pagamento específico, incluindo transições de status e quaisquer eventos de webhook associados que já tenham sido processados pelo simulador.',
                    ],
                    [
                        'title' => 'POST /api/webhooks/{provider}',
                        'desc'  => 'Recebe callbacks de webhooks para um provedor específico (mock, xendit, midtrans e outros) usando payloads, cabeçalhos e esquemas de assinatura realistas, adaptados a cada integração.',
                    ],
                ],
            ],
            'providers' => [
                'title' => 'Provedores',
                'lede'  => 'Experimente fluxos de webhooks realistas de vários gateways de pagamento em um único sandbox, sem usar credenciais reais.',
                'cta_all' => 'Ver todos os provedores',
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
                'title' => 'Verificação de Assinatura',
                'lede'  => 'Verifique payloads brutos de webhooks com assinaturas HMAC, carimbos de tempo e verificações rigorosas de cabeçalhos.',
                'compare'  => 'Compare a assinatura enviada pelo provedor com a que você calcula a partir do corpo bruto da requisição, do segredo compartilhado e do carimbo de tempo — em tempo constante.',
                'reject'    => 'Rejeite automaticamente assinaturas que não coincidem e carimbos de tempo expirados.',
                'cards' => [
                    [
                        'title' => 'HMAC / Carimbo de tempo',
                        'desc'  => 'Experimente assinaturas HMAC com carimbos de tempo que protegem contra ataques de replay. Observe como hashes do corpo bruto, segredos compartilhados e cabeçalhos assinados se combinam para formar uma trilha de auditoria verificável para cada evento de webhook.',
                    ],
                    [
                        'title' => 'Baseado em cabeçalhos',
                        'desc'  => 'Trabalhe com cabeçalhos e tokens específicos de provedores, desde segredos bearer simples até envelopes de assinatura estruturados, garantindo que a aplicação rejeite payloads forjados enquanto mantém o fluxo de eventos legítimos.',
                    ],
                ],
            ],
            'tooling' => [
                'title' => 'Ferramentas',
                'work'  => 'Funciona com o seu stack de desenvolvimento local',
                'cards' => [
                    [
                        'title' => 'OpenAPI',
                        'desc'  => 'Use o explorador OpenAPI integrado para inspecionar esquemas, gerar requisições de exemplo e testar endpoints no navegador, facilitando o compartilhamento e a documentação dos fluxos de pagamento com o time.',
                    ],
                    [
                        'title' => 'Postman',
                        'desc'  => 'Clone a coleção Postman para executar fluxos roteirizados, parametrizar ambientes e comparar rapidamente como diferentes provedores se comportam sob a mesma sequência de requisições.',
                    ],
                    [
                        'title' => 'CI',
                        'desc'  => 'Integre o simulador aos jobs de integração contínua para que cada branch e pull request seja validado contra os mesmos cenários de pagamento e webhooks esperados em produção.',
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
        'title'       => 'Provedores',
        'description' => 'Navegue pelos provedores de pagamento suportados, inspecione os formatos de payload dos seus webhooks e compare esquemas de assinatura lado a lado para desenhar uma camada de integração consistente e agnóstica em relação ao provedor.',
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
        'tests' => '{0} Nenhum teste definido ainda|{1} :count teste automatizado|[2,*] :count testes',
        'items' => '{0} Nenhum item disponível|{1} :count item|[2,*] :count itens',
    ],
];
