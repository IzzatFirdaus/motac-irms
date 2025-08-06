<?php

declare(strict_types=1);

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

/**
 * LanguageController - Handles language/locale switching
 *
 * This controller manages switching between different application locales
 * (English/Malay) and ensures the user's language preference is persisted
 * across sessions and in the user's profile.
 *
 * Works seamlessly with the SuffixedTranslator to automatically load
 * the correct language files (e.g., forms_en.php vs forms_ms.php).
 */
class LanguageController extends Controller
{
    /**
     * Switch the application locale and redirect back to previous page.
     *
     * @param Request $request
     * @param string $lang The locale to switch to (en|ms)
     * @return RedirectResponse
     */
    public function swap(Request $request, string $lang): RedirectResponse
    {
        // Get available locales from configuration
        $availableLocales = Config::get('app.available_locales', []);
        $fallbackLocale = Config::get('app.fallback_locale', 'en');

        // Validate that the requested locale is supported
        if (!is_array($availableLocales) || !array_key_exists($lang, $availableLocales)) {
            Log::warning("LanguageController: Attempted to switch to unsupported locale '{$lang}'. Using fallback: {$fallbackLocale}");
            $lang = $fallbackLocale;
        }

        // Set the application locale for this request
        App::setLocale($lang);

        // Store the locale in the session for persistence across requests
        Session::put('locale', $lang);

        // If the user is authenticated, persist preference in their profile
        if (Auth::check()) {
            try {
                Auth::user()->update(['preferred_locale' => $lang]);
                Log::info("LanguageController: Updated user preference to '{$lang}' for user: " . Auth::user()->name);
            } catch (\Exception $e) {
                Log::error("LanguageController: Failed to update user locale preference: " . $e->getMessage());
            }
        }

        // Flash a success message in the newly selected language (uses translation key)
        session()->flash('success', __('app.language_switched_' . $lang));

        // Log the language switch for debugging
        if (Config::get('app.debug')) {
            Log::debug("LanguageController: Language switched to '{$lang}' by " . (Auth::check() ? Auth::user()->name : 'guest user'));
        }

        // Redirect back to the previous page
        return redirect()->back()->with('locale_changed', true);
    }
}
