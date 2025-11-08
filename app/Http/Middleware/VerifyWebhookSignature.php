<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next)
    {
        $provider = (string) $request->route('provider');
        $raw = $request->getContent();
        $hash = null;

        if ($provider === 'mock') {
            $secret = (string) config('tenrusl.mock_secret');
            $sig = $request->header('X-Mock-Signature');

            if (! $secret || ! $sig) {
                return response()->json([
                    'error' => 'unauthorized',
                    'message' => 'Missing mock signature/secret',
                    'code' => '401',
                ], 401);
            }

            $calc = hash_hmac('sha256', $raw, $secret);
            if (! hash_equals($calc, $sig)) {
                return response()->json([
                    'error' => 'unauthorized',
                    'message' => 'Invalid mock signature',
                    'code' => '401',
                ], 401);
            }

            $hash = $calc;
        }

        if ($provider === 'xendit') {
            $token = (string) config('tenrusl.xendit_callback_token');
            $hdr   = (string) $request->header('x-callback-token', '');

            if (! $token || ! $hdr || ! hash_equals($token, $hdr)) {
                return response()->json([
                    'error' => 'unauthorized',
                    'message' => 'Invalid xendit callback token',
                    'code' => '401',
                ], 401);
            }

            $hash = 'xendit:' . substr(hash('sha256', $hdr), 0, 32);
        }

        if ($provider === 'midtrans') {
            $hdr = $request->header('Signature-Key');
            if (! $hdr) {
                return response()->json([
                    'error' => 'unauthorized',
                    'message' => 'Missing midtrans Signature-Key',
                    'code' => '401',
                ], 401);
            }
            $hash = 'midtrans:' . substr(hash('sha256', $hdr), 0, 32);
        }

        // Simpan jejak verifikasi ke attributes (lama) DAN header (baru, untuk controller)
        $request->attributes->set('webhook_signature_hash', $hash);
        $request->headers->set('X-Webhook-Signature-Hash', $hash);

        return $next($request);
    }
}
