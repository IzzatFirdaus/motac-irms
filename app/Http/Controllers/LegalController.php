<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

class LegalController extends Controller
{
    /**
     * Show the Privacy Policy page.
     */
    public function policy()
    {
        // Determine locale and use correct key for the hybrid language-markdown approach
        $locale     = app()->getLocale();
        $key        = $locale === 'ms' ? 'policy_ms.content' : 'policy_en.content';
        $policyHtml = Str::markdown(__($key));

        return view('policy', [
            'policyHtml' => $policyHtml,
            'locale'     => $locale,
        ]);
    }

    /**
     * Show the Terms of Service page.
     */
    public function terms()
    {
        $locale    = app()->getLocale();
        $key       = $locale === 'ms' ? 'terms_ms.content' : 'terms_en.content';
        $termsHtml = Str::markdown(__($key));

        return view('terms', [
            'termsHtml' => $termsHtml,
            'locale'    => $locale,
        ]);
    }
}
