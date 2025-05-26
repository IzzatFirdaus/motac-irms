<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App; // Added to ensure App facade is available
use Illuminate\Support\Facades\Session; // Added to ensure Session facade is available
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Locale is enabled and allowed to be changed
    // Updated the in_array check to include 'my' for Malay language
    if (Session::has('locale') && in_array(Session::get('locale'), ['ar', 'en', 'my'])) { //
      App::setLocale(Session::get('locale')); //
    } else {
      // If the locale in session is not in the allowed list, or no locale is set,
      // default to English, or your application's configured default/fallback locale.
      App::setLocale(config('app.fallback_locale', 'en')); //
    }

    return $next($request);
  }
}
