<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\WebhookRequest;
use App\Services\Signatures\SignatureVerifier;
use App\Services\Webhooks\WebhookProcessor;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class WebhooksController extends Controller
{
    public function __construct(
        private SignatureVerifier $signatures,
        private WebhookProcessor $processor
    ) {}

    public function store(string $provider, WebhookRequest $request)
    {
        /** @var SymfonyRequest $sym */
        $sym = $request; // hint tipe agar Intelephense paham properti headers
        $sigHash = (string) $sym->headers->get('X-Webhook-Signature-Hash', '');

        if ($sigHash === '') {
            $res = $this->signatures->verify($provider, $request);
            if (! $res['ok']) {
                return response()->json([
                    'error'   => 'unauthorized',
                    'message' => $res['msg'] ?? 'Signature verification failed',
                    'code'    => '401',
                ], 401);
            }
            $sigHash = (string) $res['hash'];
        }

        $body    = $request->validated();
        $outcome = $this->processor->process($provider, $body, $sigHash);

        if ($outcome['status'] === 'processed') {
            return response()->json([
                'received'   => true,
                'provider'   => $provider,
                'event_id'   => $body['event_id'] ?? null,
                'status'     => 'processed',
                'duplicated' => (bool) $outcome['duplicated'],
            ], 200);
        }

        return response()->json([
            'error'   => 'processing_failed',
            'message' => 'Event will be retried',
            'code'    => '500',
        ], 500);
    }
}
