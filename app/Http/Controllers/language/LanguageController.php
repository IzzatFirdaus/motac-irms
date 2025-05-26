<?php

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
  public function swap($locale)
  {
    $allowedLocales = ['ar', 'en', 'my'];
    $textDirection = 'ltr'; // Default to LTR

    if (!in_array($locale, $allowedLocales)) {
      abort(400);
    }

    if ($locale === 'ar') {
      $textDirection = 'rtl';
    }
    // For 'en' and 'my', 'ltr' is correct and already the default

    Session::put('locale', $locale);
    Session::put('textDirection', $textDirection); // Store text direction hint
    App::setLocale($locale);

    return redirect()->back();
  }
}
