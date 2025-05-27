<?php

declare(strict_types=1);

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // Import Request for IP address
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Handle the language swap.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale The desired locale code.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        // Fetches available locales from config, with a default example.
        // Your actual config('app.available_locales') should be used.
        $configuredLocales = Config::get('app.available_locales', [
            'en' => ['name' => 'English', 'direction' => 'ltr'],
            'ms' => ['name' => 'Bahasa Melayu', 'direction' => 'ltr'],
            'ar' => ['name' => 'العربية', 'direction' => 'rtl'], // Example for Arabic
        ]);
        $supportedLocales = array_keys($configuredLocales);

        if (in_array($locale, $supportedLocales, true)) {
            Session::put('locale', $locale);
            App::setLocale($locale);

            // Determine text direction from config if available, otherwise default or simple logic
            $textDirection = $configuredLocales[$locale]['direction'] ?? (($locale === 'ar') ? 'rtl' : 'ltr');
            Session::put('textDirection', $textDirection);

            Log::info(
                "Language swapped to {$locale}, direction set to {$textDirection}.",
                ['user_id' => Auth::id(), 'ip_address' => $request->ip()]
            );

            return redirect()->back();
        }

        Log::warning(
            'Attempted to set unsupported locale: ' . $locale,
            [
                'ip_address' => $request->ip(),
                'user_id' => Auth::check() ? Auth::id() : 'Guest',
            ]
        );

        // Consider a more user-friendly error page or a redirect with a flash message
        // instead of abort(400) if preferred for UX.
        abort(400, __('Unsupported language locale.'));
    }
}
