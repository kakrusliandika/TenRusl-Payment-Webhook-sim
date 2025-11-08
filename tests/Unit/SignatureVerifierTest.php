<?php

use App\Services\Signatures\SignatureVerifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    config([
        'tenrusl.mock_secret' => 'testsecret',
        'tenrusl.xendit_callback_token' => 'xendit-token',
        'tenrusl.midtrans_server_key' => 'midtrans-key',
    ]);
});

it('verifies mock signature ok', function () {
    $body = json_encode(['hello' => 'world']);
    $req = Request::create('/api/v1/webhooks/mock', 'POST', [], [], [], [], $body);

    $sig = hash_hmac('sha256', $body, config('tenrusl.mock_secret'));
    $req->headers->set('X-Mock-Signature', $sig);

    $ver = app(SignatureVerifier::class);
    $res = $ver->verify('mock', $req);

    expect($res['ok'])->toBeTrue()
        ->and($res['hash'])->toBe($sig);
});

it('rejects mock signature invalid', function () {
    $body = json_encode(['hello' => 'world']);
    $req = Request::create('/api/v1/webhooks/mock', 'POST', [], [], [], [], $body);
    $req->headers->set('X-Mock-Signature', 'invalid');

    $ver = app(SignatureVerifier::class);
    $res = $ver->verify('mock', $req);

    expect($res['ok'])->toBeFalse();
});

it('verifies xendit callback token', function () {
    $req = Request::create('/api/v1/webhooks/xendit', 'POST');
    $req->headers->set('x-callback-token', 'xendit-token');

    $ver = app(SignatureVerifier::class);
    $res = $ver->verify('xendit', $req);

    expect($res['ok'])->toBeTrue()
        ->and($res['hash'])->toStartWith('xendit:');
});

it('verifies midtrans signature-key presence', function () {
    $req = Request::create('/api/v1/webhooks/midtrans', 'POST');
    $req->headers->set('Signature-Key', 'dummy');

    $ver = app(SignatureVerifier::class);
    $res = $ver->verify('midtrans', $req);

    expect($res['ok'])->toBeTrue()
        ->and($res['hash'])->toStartWith('midtrans:');
});
