{{-- resources/views/layouts/commonMaster.blade.php --}}
<!DOCTYPE html>

@php
    $configData = \App\Helpers\Helpers::appClasses();
    $currentLocale = app()->getLocale();
    $textDirection = $configData['textDirection'] ?? ($currentLocale === 'ar' ? 'rtl' : 'ltr');
    $activeTheme = $configData['myStyle'] ?? 'light';
    $appName = __($configData['templateName'] ?? __('Sistem Pengurusan Sumber Bersepadu MOTAC'));
@endphp

<html lang="{{ $currentLocale }}"
    class="{{ $activeTheme }}-style {{ $configData['navbarFixed'] ?? '' }} {{ $configData['menuFixed'] ?? '' }} {{ $configData['menuCollapsed'] ?? '' }} {{ $configData['footerFixed'] ?? '' }} {{ $configData['customizerHidden'] ?? '' }}"
    dir="{{ $textDirection }}" data-theme="{{ $configData['myTheme'] ?? 'theme-motac' }}"
    data-assets-path="{{ asset('assets/') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel"
    data-template="{{ ($configData['myLayout'] ?? 'vertical') . '-menu-' . ($configData['myTheme'] ?? 'theme-motac') . '-' . $activeTheme }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | {{ $appName }}</title>
    <meta name="description"
        content="{{ __($configData['templateDescription'] ?? __('Sistem Dalaman Bersepadu untuk Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) bagi pengurusan permohonan emel dan pinjaman peralatan ICT.')) }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon-motac.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    {{-- FINAL: Theme Management Script with Debug Logging --}}
    <script>
      (function() {
          console.log('[Theme Manager]: Script starting.');
          const themeStorageKey = 'theme-preference';
          let preference;

          try {
              preference = localStorage.getItem(themeStorageKey);
              console.log(`[Theme Manager]: Found theme in localStorage: "${preference}"`);
          } catch (e) {
              console.warn('[Theme Manager]: Could not access localStorage.');
          }

          const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
          console.log(`[Theme Manager]: System prefers dark mode: ${systemPrefersDark}`);

          const currentTheme = preference || (systemPrefersDark ? 'dark' : 'light');
          console.log(`[Theme Manager]: Applying theme: "${currentTheme}"`);

          document.documentElement.setAttribute('data-bs-theme', currentTheme);

          window.toggleTheme = () => {
              const current = document.documentElement.getAttribute('data-bs-theme');
              const newTheme = current === 'dark' ? 'light' : 'dark';

              console.log(`[Theme Manager]: Toggling theme from "${current}" to "${newTheme}"`);

              try {
                  localStorage.setItem(themeStorageKey, newTheme);
              } catch (e) {}

              document.documentElement.setAttribute('data-bs-theme', newTheme);

              // Dispatch events for Livewire and other scripts
              if (window.Livewire) {
                  console.log('[Theme Manager]: Dispatching Livewire event "themeHasChanged".');
                  window.Livewire.dispatch('themeHasChanged', { theme: newTheme });
              }
          };
      })();
    </script>

    @include('layouts/sections/styles')
    @include('layouts/sections/scriptsIncludes')
</head>

<body>
    <a href="#main-content" class="visually-hidden-focusable">{{ __('Langkau ke Kandungan Utama') }}</a>
    @yield('layoutContent')
    @include('layouts/sections/scripts')
</body>
</html>
