<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * LocaleMiddleware - Handles application locale setting and ensures renamed auth view compatibility.
 *
 * This middleware automatically sets the application locale based on:
 * 1. User's stored preference (if authenticated)
 * 2. Session locale value
 * 3. Default application locale from config
 *
 * It also ensures that renamed authentication views are found by Laravel/Fortify/Jetstream.
 */
class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     * Sets the application locale based on user preference, session, or fallback.
     * Also registers view hints for renamed auth pages for compatibility.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // === [Locale Handling] ===
        $configuredLocales = Config::get('app.available_locales', []);
        $fallbackLocale    = Config::get('app.fallback_locale', 'en');
        $finalLocale       = $fallbackLocale; // Default to fallback locale

        // Ensure configured locales is a valid array
        if (! is_array($configuredLocales) || empty($configuredLocales)) {
            Log::warning("LocaleMiddleware: config('app.available_locales') is missing, empty, or not an array. Using fallback locale: {$fallbackLocale}");
            App::setLocale($fallbackLocale);
            $this->registerAuthViewHints(); // Register view hints even if locale is not set

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

        // Register view hints for the renamed auth view files
        $this->registerAuthViewHints();

        // Optional: Log locale changes for debugging (remove in production)
        if (Config::get('app.debug')) {
            Log::debug("LocaleMiddleware: Set application locale to '{$finalLocale}' for user: ".(Auth::check() ? Auth::user()->name : 'guest'));
        }

        return $next($request);
    }

    /**
     * Register view hints for renamed authentication views.
     * This allows packages and controllers that call the default view names to continue working.
     * Example: 'auth.login' → 'auth.login-page', etc.
     *
     * Laravel does not support "aliasing" views natively. Instead, we check if the default view does not exist
     * (e.g. auth.login), but the renamed Blade does (e.g. auth.login-page), and if so, we dynamically create
     * the expected file as a symlink or hard copy if needed.
     */
    protected function registerAuthViewHints(): void
    {
        // Mapping of logical view names to physical files
        $viewMap = [
            'login',
            'register',
            'forgot-password',
            'reset-password',
            'confirm-password',
            'verify-email',
            'two-factor-challenge',
        ];

        $authViewsPath = resource_path('views/auth');
        foreach ($viewMap as $view) {
            $originalPath = $authViewsPath."/{$view}.blade.php";
            $renamedPath  = $authViewsPath."/{$view}-page.blade.php";

            // Only create the link if the renamed exists and the original does not
            if (! file_exists($originalPath) && file_exists($renamedPath)) {
                // Try to create a symlink for the view (preferred for dev), fallback to copy
                try {
                    // On some systems, symlink requires elevated privileges; fallback to copy if fails
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                        // On Windows, symlink for files is not always enabled, try copy
                        copy($renamedPath, $originalPath);
                    } else {
                        symlink($renamedPath, $originalPath);
                    }
                } catch (\Throwable $e) {
                    // If symlink or copy fails, ignore and let the missing view error show as fallback
                    Log::warning("LocaleMiddleware: Could not create alias for auth view '{$view}': ".$e->getMessage());
                }
            }
        }
    }
}
