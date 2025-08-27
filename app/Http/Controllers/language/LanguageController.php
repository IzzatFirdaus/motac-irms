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

/**
 * LanguageController - Handles language/locale switching for the application.
 *
 * Utilizes MOTAC's custom SuffixedTranslator for dynamic loading of suffixed language files,
 * and ensures user preference is persisted in both session and user profile.
 *
 * Works seamlessly with the SuffixedTranslator to load correct language files (e.g., forms_en.php, forms_ms.php).
 */
class LanguageController extends Controller
{
    /**
     * Switch the application locale and redirect back.
     *
     * @param string $lang The locale to switch to (e.g. 'en', 'ms').
     */
    public function swap(Request $request, string $lang): RedirectResponse
    {
        // Get available locales from config (should be array: ['en' => 'English', 'ms' => 'Bahasa Melayu', ...])
        $availableLocales = Config::get('app.available_locales', []);
        $fallbackLocale   = Config::get('app.fallback_locale', 'en');

        // Validate requested locale
        if (! is_array($availableLocales) || ! array_key_exists($lang, $availableLocales)) {
            Log::warning("LanguageController: Attempted to switch to unsupported locale '{$lang}'. Using fallback: {$fallbackLocale}");
            $lang = $fallbackLocale;
        }

        // Set the application locale for this request
        App::setLocale($lang);

        // If using the SuffixedTranslator, update its locale as well
        if (app()->bound('translator') && method_exists(app('translator'), 'setLocale')) {
            app('translator')->setLocale($lang);
        }

        // Store locale in session for persistence across requests
        Session::put('locale', $lang);

        // If the user is authenticated, persist preference in their profile
        if (Auth::check()) {
            try {
                $user = Auth::user();
                // Only update if different to avoid unnecessary DB updates
                if ($user->preferred_locale !== $lang) {
                    $user->update(['preferred_locale' => $lang]);
                    Log::info("LanguageController: Updated user preference to '{$lang}' for user: " . $user->name);
                }
            } catch (\Exception $e) {
                Log::error('LanguageController: Failed to update user locale preference: ' . $e->getMessage());
            }
        }

        // Flash a success message using the newly selected language
        // Uses translation key: 'app.language_switched_{locale}'
        session()->flash('success', __('app.language_switched_' . $lang));

        // Log the language switch for debugging
        if (Config::get('app.debug')) {
            Log::debug("LanguageController: Language switched to '{$lang}' by " . (Auth::check() ? Auth::user()->name : 'guest user'));
        }

        // Redirect back to the previous page, add a flag for frontend if needed
        return redirect()->back()->with('locale_changed', true);
    }
}
