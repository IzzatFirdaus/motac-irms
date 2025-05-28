<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session; // Optional: for logging locale setting actions
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     * Sets the application locale based on session or fallback.
     * Referenced in System Design 3.1, "The Big Picture" flow, and Kernel.php.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionLocale = Session::get('locale');
        $finalLocale = null;

        // Get available locales from config/app.php's 'available_locales'
        // System Design 3.3, config('app.available_locales') structure.
        $configuredLocales = Config::get('app.available_locales', []);
        $allowedLocaleKeys = [];

        if (!empty($configuredLocales) && is_array($configuredLocales)) {
            if (isset($configuredLocales[0]) && is_string($configuredLocales[0])) {
                // Handles simple array like ['en', 'my', 'ar'] (though associative is preferred for direction)
                $allowedLocaleKeys = $configuredLocales;
            } else {
                // Assumes associative array like ['en' => [...], 'my' => [...]]
                $allowedLocaleKeys = array_keys($configuredLocales);
            }
        }

        // Fallback if allowedLocaleKeys is still empty (e.g., misconfiguration)
        if (empty($allowedLocaleKeys)) {
            $fallbackLocale = Config::get('app.fallback_locale', 'en');
            $allowedLocaleKeys = [$fallbackLocale]; // At least allow the fallback
            Log::warning('LocaleMiddleware: config(\'app.available_locales\') is empty or misconfigured. Using only fallback.', ['fallback' => $fallbackLocale]);
        }


        if ($sessionLocale && in_array($sessionLocale, $allowedLocaleKeys, true)) {
            $finalLocale = $sessionLocale;
        } else {
            // If the locale in session is not in the allowed list, or no locale is in session,
            // default to the application's configured fallback locale.
            $finalLocale = Config::get('app.fallback_locale', 'en');
            if ($sessionLocale) { // If there was a session locale but it was invalid
                Log::debug("LocaleMiddleware: Invalid session locale '{$sessionLocale}'. Reverting to fallback '{$finalLocale}'.");
                Session::put('locale', $finalLocale); // Optionally, correct the session to the fallback.
                // Also update textDirection in session if locale is forcibly changed to fallback
                $textDirection = $configuredLocales[$finalLocale]['direction'] ?? (($finalLocale === 'ar') ? 'rtl' : 'ltr');
                Session::put('textDirection', $textDirection);
            }
        }

        App::setLocale($finalLocale);
        // Log::debug("LocaleMiddleware: Application locale set to '{$finalLocale}'."); // Optional: for debugging

        return $next($request);
    }
}
