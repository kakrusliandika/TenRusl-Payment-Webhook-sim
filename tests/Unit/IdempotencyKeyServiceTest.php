<?php

declare(strict_types=1);

use App\Services\Idempotency\IdempotencyKeyService;
use App\Services\Idempotency\RequestFingerprint;
use Illuminate\Http\Request;

it('prefers explicit Idempotency-Key header over fingerprint', function () {
    /** @var IdempotencyKeyService $service */
    $service = app(IdempotencyKeyService::class);

    $body = ['provider' => 'mock', 'amount' => 12345, 'currency' => 'IDR'];
    $request = Request::create('/api/v1/payments', 'POST', [], [], [], [], json_encode($body));
    $request->headers->set('Content-Type', 'application/json');
    $request->headers->set('Idempotency-Key', 'fixed-key-123');

    $key = $service->resolveKey($request);

    expect($key)->toBe('fixed-key-123');
});

it('generates stable key for identical requests when header is missing', function () {
    /** @var IdempotencyKeyService $service */
    $service = app(IdempotencyKeyService::class);

    $payload = ['provider' => 'mock', 'amount' => 100000, 'currency' => 'IDR'];

    $r1 = Request::create('/api/v1/payments', 'POST', [], [], [], [], json_encode($payload));
    $r1->headers->set('Content-Type', 'application/json');

    $r2 = Request::create('/api/v1/payments', 'POST', [], [], [], [], json_encode($payload));
    $r2->headers->set('Content-Type', 'application/json');

    $k1 = $service->resolveKey($r1);
    $k2 = $service->resolveKey($r2);

    expect($k1)->toBeString();
    expect($k1 === '')->toBeFalse();
    expect($k1)->toBe($k2);
});

it('produces different keys for different request bodies (no header)', function () {
    /** @var IdempotencyKeyService $service */
    $service = app(IdempotencyKeyService::class);

    $rA = Request::create('/api/v1/payments', 'POST', [], [], [], [], json_encode(['provider' => 'mock', 'amount' => 100]));
    $rA->headers->set('Content-Type', 'application/json');

    $rB = Request::create('/api/v1/payments', 'POST', [], [], [], [], json_encode(['provider' => 'mock', 'amount' => 200]));
    $rB->headers->set('Content-Type', 'application/json');

    $kA = $service->resolveKey($rA);
    $kB = $service->resolveKey($rB);

    expect($kA === $kB)->toBeFalse();
});

it('is compatible with RequestFingerprint if used under the hood', function () {
    /** @var RequestFingerprint $fp */
    $fp = app(RequestFingerprint::class);

    $req = Request::create('/api/v1/payments?foo=bar', 'POST', [], [], [], [], json_encode(['x' => 1]));
    $req->headers->set('Content-Type', 'application/json');

    // Cek method yang paling mungkin dipakai (punya kamu: hash(Request): string)
    $method = method_exists($fp, 'hash') ? 'hash' : null;

    expect($method === null)->toBeFalse();

    /** @var string $method */
    $hash = $fp->$method($req);

    expect($hash)->toBeString();
    expect($hash === '')->toBeFalse();
});
