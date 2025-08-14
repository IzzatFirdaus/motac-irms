<?php

declare(strict_types=1);

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class LanguageController extends Controller
{
    /**
     * Handle the language swap.
     * SDD Ref: 3.1
     *
     * @param  string  $locale  The desired locale code (e.g., 'ms', 'en').
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        $configuredLocales = Config::get('app.available_locales', []);
        $supportedLocaleKeys = array_keys($configuredLocales);

        if (in_array($locale, $supportedLocaleKeys, true)) {
            App::setLocale($locale);
            Session::put('locale', $locale);

            $textDirection = $configuredLocales[$locale]['direction'] ?? (($locale === 'ar') ? 'rtl' : 'ltr'); // Default LTR if not specified, RTL for 'ar'
            Session::put('textDirection', $textDirection);

            Log::info(sprintf("Language swapped successfully to '%s' with direction '%s'.", $locale, $textDirection), [
                'user_id' => Auth::id() ?? 'Guest',
                'ip_address' => $request->ip(),
                'new_locale' => $locale,
                'new_direction' => $textDirection,
            ]);

            return redirect()->to(URL::previous());
        }

        Log::warning(sprintf("Attempted to set unsupported locale: '%s'.", $locale), [
            'user_id' => Auth::id() ?? 'Guest',
            'ip_address' => $request->ip(),
            'requested_locale' => $locale,
            'supported_locales' => $supportedLocaleKeys,
        ]);

        abort(400, __('Unsupported language locale provided.'));
    }
}
