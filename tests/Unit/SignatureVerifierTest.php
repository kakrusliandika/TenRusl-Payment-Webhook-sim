<?php

declare(strict_types=1);

use App\Services\Signatures\SignatureVerifier;
use Illuminate\Http\Request;

it('verifies mock provider signature using HMAC header', function () {
    config(['tenrusl.mock_secret' => 'unit-secret']);

    /** @var SignatureVerifier $verifier */
    $verifier = app(SignatureVerifier::class);

    $payload = ['id' => 'evt_'.now()->timestamp, 'type' => 'payment.paid'];
    $raw = json_encode($payload, JSON_UNESCAPED_SLASHES);

    $req = Request::create('/api/v1/webhooks/mock', 'POST', [], [], [], [], $raw);
    $req->headers->set('Content-Type', 'application/json');

    $sig = hash_hmac('sha256', $raw, 'unit-secret');
    $req->headers->set('X-Mock-Signature', $sig);

    // Urutan argumen: provider (string), raw body (string), request (Request)
    $ok = $verifier->verify('mock', $raw, $req);
    expect($ok)->toBeTrue();
});

it('fails verification when signature is wrong', function () {
    config(['tenrusl.mock_secret' => 'unit-secret']);

    /** @var SignatureVerifier $verifier */
    $verifier = app(SignatureVerifier::class);

    $raw = '{"id":"evt_bad","type":"payment.paid"}';
    $req = Request::create('/api/v1/webhooks/mock', 'POST', [], [], [], [], $raw);
    $req->headers->set('Content-Type', 'application/json');
    $req->headers->set('X-Mock-Signature', 'invalid');

    $ok = $verifier->verify('mock', $raw, $req);
    expect($ok)->toBeFalse();
});

it('returns false for unknown / not-configured provider', function () {
    /** @var SignatureVerifier $verifier */
    $verifier = app(SignatureVerifier::class);

    $raw = '{}';
    $req = Request::create('/api/v1/webhooks/unknown', 'POST', [], [], [], [], $raw);
    $req->headers->set('Content-Type', 'application/json');

    $ok = $verifier->verify('unknown', $raw, $req);
    expect($ok)->toBeFalse();
});
