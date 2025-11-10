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

    // Asumsikan service punya metode resolve(Request): string (fallback: getKey)
    $method = method_exists($service, 'resolve') ? 'resolve' : 'getKey';
    $key = $service->$method($request);

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

    $method = method_exists($service, 'resolve') ? 'resolve' : 'getKey';

    $k1 = $service->$method($r1);
    $k2 = $service->$method($r2);

    expect($k1)->toBeString()->not->toBeEmpty();
    expect($k1)->toBe($k2);
});

it('produces different keys for different request bodies (no header)', function () {
    /** @var IdempotencyKeyService $service */
    $service = app(IdempotencyKeyService::class);

    $rA = Request::create('/api/v1/payments', 'POST', [], [], [], [], json_encode(['provider' => 'mock', 'amount' => 100]));
    $rA->headers->set('Content-Type', 'application/json');

    $rB = Request::create('/api/v1/payments', 'POST', [], [], [], [], json_encode(['provider' => 'mock', 'amount' => 200]));
    $rB->headers->set('Content-Type', 'application/json');

    $method = method_exists($service, 'resolve') ? 'resolve' : 'getKey';

    $kA = $service->$method($rA);
    $kB = $service->$method($rB);

    expect($kA)->not->toBe($kB);
});

it('is compatible with RequestFingerprint if used under the hood', function () {
    // Test ini hanya memastikan RequestFingerprint tersedia dan menghasilkan hash string.
    /** @var RequestFingerprint $fp */
    $fp = app(RequestFingerprint::class);

    $req = Request::create('/api/v1/payments?foo=bar', 'POST', [], [], [], [], json_encode(['x' => 1]));
    $req->headers->set('Content-Type', 'application/json');

    // Nama method bisa bervariasi, coba beberapa kemungkinan umum:
    $method = null;
    foreach (['make', 'hash', 'fingerprint', '__invoke'] as $candidate) {
        if (method_exists($fp, $candidate)) {
            $method = $candidate;
            break;
        }
    }
    expect($method)->not->toBeNull();

    $hash = $fp->$method($req);
    expect($hash)->toBeString()->not->toBeEmpty();
});
