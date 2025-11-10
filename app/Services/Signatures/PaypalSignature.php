<?php

namespace App\Services\Signatures;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaypalSignature
{
    /**
     * Verifies PayPal webhook via the official Verify Webhook Signature API.
     *
     * Requires headers:
     *  - PAYPAL-TRANSMISSION-ID
     *  - PAYPAL-TRANSMISSION-TIME
     *  - PAYPAL-TRANSMISSION-SIG
     *  - PAYPAL-CERT-URL
     *  - PAYPAL-AUTH-ALGO
     * And config('tenrusl.paypal_webhook_id'), client id/secret.
     */
    public static function verify(string $rawBody, Request $request): bool
    {
        $clientId     = (string) config('tenrusl.paypal_client_id');
        $clientSecret = (string) config('tenrusl.paypal_client_secret');
        $webhookId    = (string) config('tenrusl.paypal_webhook_id');
        $env          = (string) config('tenrusl.paypal_env', 'sandbox');

        if ($clientId === '' || $clientSecret === '' || $webhookId === '') {
            return false;
        }

        $base = $env === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        // Get OAuth access token
        $tokenResp = Http::asForm()
            ->withBasicAuth($clientId, $clientSecret)
            ->post($base . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);

        if (!$tokenResp->ok()) {
            return false;
        }

        $accessToken = $tokenResp->json('access_token');
        if (!$accessToken) {
            return false;
        }

        $payload = [
            'transmission_id'  => $request->header('PAYPAL-TRANSMISSION-ID'),
            'transmission_time'=> $request->header('PAYPAL-TRANSMISSION-TIME'),
            'cert_url'         => $request->header('PAYPAL-CERT-URL'),
            'auth_algo'        => $request->header('PAYPAL-AUTH-ALGO'),
            'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
            'webhook_id'       => $webhookId,
            'webhook_event'    => json_decode($rawBody, true),
        ];

        // Basic sanity check
        foreach (['transmission_id','transmission_time','cert_url','auth_algo','transmission_sig'] as $k) {
            if (empty($payload[$k])) {
                return false;
            }
        }

        $verifyResp = Http::withToken($accessToken)
            ->post($base . '/v1/notifications/verify-webhook-signature', $payload);

        if (!$verifyResp->ok()) {
            return false;
        }

        return strtoupper((string) $verifyResp->json('verification_status')) === 'SUCCESS';
    }
}
