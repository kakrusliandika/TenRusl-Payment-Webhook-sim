<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Date; // Laravel Date facade wraps Carbon

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $available = (array) config('localization.available_locales', ['en','id','ar','de','hi','ja','ko','pt','ru','th','zh', 'es']);
        $fallback  = (string) config('app.fallback_locale', 'en');

        $normalize = function (?string $raw) use ($available, $fallback): string {
            if (!$raw) return $fallback;
            if (preg_match('/^[a-z]{2}(?:-[A-Z]{2})?$/', $raw)) {
                $raw = strtolower(explode('-', $raw)[0]);
            } else {
                $raw = strtolower($raw);
            }
            return in_array($raw, $available, true) ? $raw : $fallback;
        };

        $requested = $request->query('lang');
        $session   = Session::get('app_locale');

        if ($requested) {
            $locale = $normalize($requested);
            Session::put('app_locale', $locale);
        } elseif ($session) {
            $locale = $normalize($session);
        } else {
            $locale = $normalize(config('app.locale'));
            Session::put('app_locale', $locale);
        }

        App::setLocale($locale);
        // Prefer Laravel's Date facade (internally uses Carbon) to avoid IDE false-positives.
        try { Date::setLocale($locale); } catch (\Throwable $e) {}

        return $next($request);
    }
}
