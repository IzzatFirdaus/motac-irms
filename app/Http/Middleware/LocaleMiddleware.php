<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * LocaleMiddleware - Handles application locale setting
 *
 * This middleware automatically sets the application locale based on:
 * 1. User's stored preference (if authenticated)
 * 2. Session locale value
 * 3. Default application locale from config
 *
 * Works with the custom SuffixedTranslator to load language files
 * like forms_en.php, forms_ms.php automatically.
 */
class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     * Sets the application locale based on user preference, session, or fallback.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get configuration values with safe defaults
        $configuredLocales = Config::get('app.available_locales', []);
        $fallbackLocale = Config::get('app.fallback_locale', 'en');
        $finalLocale = $fallbackLocale; // Default to fallback locale

        // Ensure configured locales is a valid array
        if (!is_array($configuredLocales) || empty($configuredLocales)) {
            Log::warning("LocaleMiddleware: config('app.available_locales') is missing, empty, or not an array. Using fallback locale: {$fallbackLocale}");
            App::setLocale($fallbackLocale);
            return $next($request);
        }

        // Get allowed locale keys from configuration
        $allowedLocaleKeys = array_keys($configuredLocales);

        // Priority 1: Check authenticated user's preferred locale
        if (Auth::check() && Auth::user()->preferred_locale) {
            $userPreferredLocale = Auth::user()->preferred_locale;
            if (in_array($userPreferredLocale, $allowedLocaleKeys, true)) {
                $finalLocale = $userPreferredLocale;
                // Update session to match user preference
                Session::put('locale', $finalLocale);
            } else {
                Log::debug("LocaleMiddleware: User's preferred locale '{$userPreferredLocale}' is not in allowed locales. Using fallback.");
            }
        }
        // Priority 2: Check session locale (if user preference not available or invalid)
        elseif (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if ($sessionLocale && in_array($sessionLocale, $allowedLocaleKeys, true)) {
                $finalLocale = $sessionLocale;
            } else {
                Log::debug("LocaleMiddleware: Session locale '{$sessionLocale}' is invalid or not allowed. Using fallback: {$fallbackLocale}");
                // Clean up invalid session locale
                Session::put('locale', $fallbackLocale);
            }
        }
        // Priority 3: Use application default locale if valid
        else {
            $defaultLocale = Config::get('app.locale', $fallbackLocale);
            if (in_array($defaultLocale, $allowedLocaleKeys, true)) {
                $finalLocale = $defaultLocale;
            }
            // Set session for consistency
            Session::put('locale', $finalLocale);
        }

        // Set the application locale
        App::setLocale($finalLocale);

        // Optional: Log locale changes for debugging (remove in production)
        if (Config::get('app.debug')) {
            Log::debug("LocaleMiddleware: Set application locale to '{$finalLocale}' for user: " . (Auth::check() ? Auth::user()->name : 'guest'));
        }

        return $next($request);
    }
}
