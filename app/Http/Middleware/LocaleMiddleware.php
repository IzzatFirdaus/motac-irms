<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
  /**
   * Handle an incoming request.
   * Sets the application locale based on session or fallback.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    //dd('LocaleMiddleware file has been updated and is now running!');


    $sessionLocale = Session::get('locale');
    $fallbackLocale = Config::get('app.fallback_locale', 'en');
    $finalLocale = $fallbackLocale; // Default to fallback locale

    // --- REVISED: Bulletproof Locale Handling ---

    $configuredLocales = Config::get('app.available_locales');
    $allowedLocaleKeys = [];

    // 1. Defensively check if the config value is a valid, non-empty array.
    if (is_array($configuredLocales) && !empty($configuredLocales)) {

      // 2. Get the allowed keys. This operation is now guaranteed to be safe.
      $allowedLocaleKeys = array_keys($configuredLocales);

      // 3. Check if the locale stored in the session is valid and in the allowed list.
      if ($sessionLocale && in_array($sessionLocale, $allowedLocaleKeys, true)) {
        $finalLocale = $sessionLocale;
      }
    } else {
      // 4. If config is invalid for any reason, log a warning for the developer.
      // The application will safely continue using the fallback locale.
      Log::warning('LocaleMiddleware: config(\'app.available_locales\') is either missing, empty, or not an array. Using fallback locale.');
    }

    // If the session had an invalid locale, correct it to the determined final locale.
    if ($sessionLocale && $sessionLocale !== $finalLocale) {
      Log::debug("LocaleMiddleware: Invalid or disallowed session locale '{$sessionLocale}'. Reverting to '{$finalLocale}'.");
      Session::put('locale', $finalLocale);
    }
    // --- End of Revision ---

    App::setLocale($finalLocale);

    return $next($request);
  }
}
