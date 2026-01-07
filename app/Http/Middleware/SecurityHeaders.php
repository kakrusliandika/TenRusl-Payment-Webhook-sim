<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Bepsvpt\SecureHeaders\SecureHeadersMiddleware as PackageSecureHeaders;
use Closure;
use Illuminate\Http\Request;
use Spatie\Csp\AddCspHeaders as PackageAddCspHeaders;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders (legacy wrapper)
 * -------------------------------
 * @deprecated Gunakan middleware package langsung:
 * - \Bepsvpt\SecureHeaders\SecureHeadersMiddleware
 * - \Spatie\Csp\AddCspHeaders
 *
 * Alasan file ini tetap dipertahankan:
 * - Menghindari breaking change jika ada route/group lama yang masih mereferensikan App\Http\Middleware\SecurityHeaders.
 * - Tidak ada duplikasi header policy; semuanya bersumber dari config package.
 */
final class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var PackageSecureHeaders $secureHeaders */
        $secureHeaders = app(PackageSecureHeaders::class);

        // Untuk API JSON biasanya CSP tidak dibutuhkan.
        if ($request->is('api/*')) {
            return $secureHeaders->handle($request, $next);
        }

        /** @var PackageAddCspHeaders $csp */
        $csp = app(PackageAddCspHeaders::class);

        // Chain: SecureHeaders -> CSP -> next
        return $secureHeaders->handle($request, function (Request $request) use ($csp, $next) {
            return $csp->handle($request, $next);
        });
    }
}
