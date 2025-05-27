<?php

declare(strict_types=1);

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // Request is used for IP logging
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth; // For logging user ID
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL; // To generate the previous URL

class LanguageController extends Controller
{
    /**
     * Handle the language swap.
     * This is a single-action controller, invoked when the route points to the class.
     * It's referenced in System Design 3.1 and part of "The Big Picture" flow.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale The desired locale code (e.g., 'en', 'my', 'ar').
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        // Fetches available locales and their properties (name, direction)
        // from config/app.php's 'available_locales' array.
        // System Design 3.3 & 4.1 (config/app.php).
        $configuredLocales = Config::get('app.available_locales', []);
        $supportedLocaleKeys = array_keys($configuredLocales);

        if (in_array($locale, $supportedLocaleKeys, true)) {
            App::setLocale($locale);
            Session::put('locale', $locale);

            // Determine and store text direction based on the configuration for the chosen locale.
            // This is crucial for commonMaster.blade.php and Helpers::appClasses().
            $textDirection = $configuredLocales[$locale]['direction'] ?? (($locale === 'ar') ? 'rtl' : 'ltr'); // Fallback if 'direction' key is missing
            Session::put('textDirection', $textDirection);

            // Update theme style setting in session if text direction changes,
            // as some themes might have distinct LTR/RTL style preferences.
            // This part is optional and depends on how deeply integrated RTL is with styles beyond just `dir`.
            // Session::put('theme_style', $textDirection === 'rtl' ? Config::get('custom.custom.myStyleRtl', 'light') : Config::get('custom.custom.myStyle', 'light'));


            Log::info("Language swapped successfully to '{$locale}' with direction '{$textDirection}'.", [
                'user_id' => Auth::id() ?? 'Guest',
                'ip_address' => $request->ip(),
                'new_locale' => $locale,
                'new_direction' => $textDirection,
            ]);

            // Redirect back to the previous page.
            // Using URL::previous() is generally reliable.
            return redirect()->to(URL::previous());

        }

        Log::warning("Attempted to set unsupported locale: '{$locale}'.", [
            'user_id' => Auth::id() ?? 'Guest',
            'ip_address' => $request->ip(),
            'requested_locale' => $locale,
            'supported_locales' => $supportedLocaleKeys,
        ]);

        // If the locale is not supported, redirect back with an error message (optional)
        // or abort. Aborting is simpler if this URL is not user-typable.
        // return redirect()->back()->with('error', __('Unsupported language.'));
        abort(400, __('Unsupported language locale provided.'));
    }
}
