{{-- resources/views/layouts/commonMaster.blade.php --}}
<!DOCTYPE html>

@php
    // Fetch application configuration using the App\Helpers\Helpers class.
    // This $configData likely controls theme aspects (style, navbar/menu fixed, etc.)
    $configData = \App\Helpers\Helpers::appClasses();

    // Determine current locale and text direction.
    // Design Language 1.2: Bahasa Melayu First (ltr).
    $currentLocale = app()->getLocale();
    $textDirection = $configData['textDirection'] ?? ($currentLocale === 'ar' ? 'rtl' : 'ltr'); // Example 'ar' for RTL

    // Determine active theme (light/dark) for Bootstrap.
    // Design Language 5.0: Dark Mode Specification.
    $activeTheme = $configData['style'] ?? 'light'; // 'light' or 'dark'
    $bsTheme = $activeTheme === 'dark' ? 'dark' : 'light';

    // Define a default MOTAC application name if not provided by $configData
    $appName = __($configData['templateName'] ?? __('Sistem Pengurusan Sumber Bersepadu MOTAC'));
@endphp

{{--
  The html tag includes dynamic classes for theme styling, locale, and text direction.
  - lang: Set to the current application locale (e.g., "ms" for Bahasa Melayu).
  - class: Applies theme-specific classes for light/dark styles and layout options.
  - dir: Sets text direction (ltr/rtl).
  - data-bs-theme: For Bootstrap 5's native color mode handling.
  - data-theme: Custom theme identifier, useful for CSS scoping.
--}}
<html lang="{{ $currentLocale }}"
    class="{{ $activeTheme }}-style {{ $configData['navbarFixed'] ?? '' }} {{ $configData['menuFixed'] ?? '' }} {{ $configData['menuCollapsed'] ?? '' }} {{ $configData['footerFixed'] ?? '' }} {{ $configData['customizerHidden'] ?? '' }}"
    dir="{{ $textDirection }}" data-theme="{{ $configData['theme'] ?? 'theme-motac' }}" {{-- Ensure this theme aligns with MOTAC specs --}}
    data-bs-theme="{{ $bsTheme }}" data-assets-path="{{ asset('assets/') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel"
    data-template="{{ ($configData['layout'] ?? 'vertical') . '-menu-' . ($configData['theme'] ?? 'theme-motac') . '-' . $activeTheme }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | {{ $appName }}</title>
    <meta name="description"
        content="{{ __($configData['templateDescription'] ?? __('Sistem Dalaman Bersepadu untuk Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) bagi pengurusan permohonan emel dan pinjaman peralatan ICT.')) }}" />
    <meta name="keywords"
        content="{{ __($configData['templateKeyword'] ?? 'motac, bpm, sistem bersepadu, pengurusan sumber, pinjaman ict, permohonan emel, malaysia') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon - Design Language: MOTAC Branding --}}
    {{-- Ensure 'favicon-motac.ico' is the official MOTAC favicon. --}}
    <link rel="icon" type="image/x-icon"
        href="{{ asset($configData['appFavicon'] ?? 'assets/img/favicon/favicon-motac.ico') }}" />

    {{-- MOTAC Typography: Noto Sans (Design Language 2.2) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    {{--
        Core Stylesheets:
        - Bootstrap & Theme Global Styles.
        - These should define MOTAC color variables (--motac-primary, etc. from Design Doc 2.1)
          and apply base body styles (font-family: 'Noto Sans', line-height: 1.6 for BM - Design Doc 2.2).
        - Dark mode styles (Design Doc 5.0) should also be handled here, activated by `data-bs-theme="dark"`.
    --}}
    @include('layouts/sections/styles')

    {{-- Helper PWA Manifest (optional, if PWA features are desired) --}}
    {{-- <link rel="manifest" href="{{ asset('manifest.json') }}"> --}}
    {{-- <meta name="theme-color" content="#0055A4"/> MOTAC Blue for PWA theme color --}}


    {{-- Head Scripts (e.g., helpers, theme customizer init) --}}
    @include('layouts/sections/scriptsIncludes')

    {{-- Inline styles for quick overrides or critical CSS --}}
    <style>
        /* Ensure Noto Sans is the base font for the application, overriding theme defaults if necessary */
        body {
            font-family: 'Noto Sans', sans-serif !important;
            /* Important to override potential theme styles */
            line-height: 1.6;
            /* Optimal readability for Bahasa Melayu - Design Doc 2.2 */
            background-color: var(--motac-background, #F8F9FA);
            /* Fallback to light mode background */
            color: var(--motac-text, #212529);
            /* Fallback to light mode text */
        }

        /*
         Define MOTAC Color Palette CSS Variables (Design Language 2.1)
         These should ideally be in a core CSS file loaded by layouts/sections/styles.
         This is a fallback or primary definition location.
        */
        :root {
            --motac-primary: #0055A4;
            --motac-secondary: #8C1D40;
            --motac-success: #28A745;
            --motac-danger: #DC3545;
            --motac-warning: #FFC107;
            /* Note: Bootstrap $warning is often different */
            --motac-info: #0dcaf0;
            /* Bootstrap default info, adjust if MOTAC has specific */
            --motac-light: #F8F9FA;
            /* Same as MOTAC Background Light */
            --motac-dark: #212529;
            /* Same as MOTAC Text Light */

            --motac-background: #F8F9FA;
            --motac-surface: #FFFFFF;
            --motac-text: #212529;
            --motac-text-muted: #6C757D;
            --motac-border: #DEE2E6;

            /* For Bootstrap theme color mapping if not using SASS overrides */
            --bs-primary: var(--motac-primary);
            --bs-secondary: var(--motac-secondary);
            --bs-success: var(--motac-success);
            --bs-danger: var(--motac-danger);
            /* --bs-warning: var(--motac-warning); Ensure this aligns with Design Doc use of $warning */
            /* --bs-info: var(--motac-info); */
        }

        [data-bs-theme="dark"] {
            --motac-primary: #3D8FD1;
            --motac-secondary: #A9496B;
            --motac-success: #4ADE80;
            --motac-danger: #F87171;
            --motac-warning: #FACC15;
            /* Example dark warning, adjust from Design Doc */
            --motac-info: #38BDF8;
            /* Example dark info, adjust */

            --motac-background: #121826;
            --motac-surface: #1E293B;
            --motac-text: #E9ECEF;
            --motac-text-muted: #ADB5BD;
            --motac-border: #495057;
            /* From your first HTML example's CSS */

            /* For Bootstrap theme color mapping in dark mode */
            --bs-primary: var(--motac-primary);
            --bs-secondary: var(--motac-secondary);
            --bs-success: var(--motac-success);
            --bs-danger: var(--motac-danger);
            /* ... and so on for other bs variables ... */
            --bs-body-bg: var(--motac-background);
            --bs-body-color: var(--motac-text);
            --bs-border-color: var(--motac-border);
        }

        /* Custom focus indicator for accessibility (Design Language 6.1) */
        *:focus-visible {
            outline: 3px solid var(--motac-primary, #0055A4) !important;
            outline-offset: 2px;
            box-shadow: none !important;
            /* Remove default Bootstrap shadow if it conflicts */
        }
    </style>

</head>

<body>

    {{-- Accessibility: Skip to Main Content Link (Design Language 6.1) --}}
    {{-- This should be the VERY FIRST focusable item on the page. --}}
    <a href="#main-content" class="visually-hidden-focusable">{{ __('Langkau ke Kandungan Utama') }}</a>

    {{-- Yields the main layout structure (e.g., from app.blade.php or blankLayout.blade.php) --}}
    @yield('layoutContent')

    {{-- Core JavaScript files (jQuery, Bootstrap, Theme main scripts) --}}
    @include('layouts/sections/scripts')

</body>

</html>
