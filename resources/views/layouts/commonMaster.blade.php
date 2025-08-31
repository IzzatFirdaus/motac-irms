{{-- resources/views/layouts/commonMaster.blade.php --}}
{{-- This is the root master layout for all MOTAC system pages. It sets up the HTML, head, and body, and includes theme styles, scripts, and accessibility features. --}}

<!DOCTYPE html>

@php
    $configData = \App\Helpers\Helpers::appClasses();
    $currentLocale = app()->getLocale();
    $textDirection = $configData['textDirection'] ?? ($currentLocale === 'ar' ? 'rtl' : 'ltr');
    $appName = __($configData['templateName'] ?? __('Sistem Pengurusan Sumber Bersepadu MOTAC'));
@endphp

<html lang="{{ $currentLocale }}" class="" dir="{{ $textDirection }}" data-theme="{{ $configData['myTheme'] ?? 'theme-motac' }}"
    data-assets-path="{{ asset('assets/') . '/' }}" data-base-url="{{ url('/') }}" data-framework="laravel"
    data-template="{{ ($configData['myLayout'] ?? 'vertical') . '-menu-' . ($configData['myTheme'] ?? 'theme-motac') . '-light' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | {{ $appName }}</title>
    <meta name="description"
        content="{{ __($configData['templateDescription'] ?? 'Sistem Dalaman Bersepadu untuk Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) bagi pengurusan permohonan emel dan pinjaman peralatan ICT.') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon-motac.ico') }}" />

    {{-- =================================================================================== --}}
    {{-- == START: UNIFIED THEME MANAGEMENT SCRIPT == --}}
    {{-- This single script now controls the theme to prevent flashing and conflicts. --}}
    {{-- It runs in the <head> before the body is rendered. --}}
    {{-- =================================================================================== --}}
    <script>
        (function() {
            const themeStorageKey = 'theme-preference';
            let preference = localStorage.getItem(themeStorageKey);
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const currentTheme = preference || (systemPrefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', currentTheme);
        })();
    </script>
    {{-- =================================================================================== --}}
    {{-- == END: UNIFIED THEME MANAGEMENT SCRIPT == --}}
    {{-- =================================================================================== --}}

    {{-- Include all main layout stylesheets (with RTL, theme, and custom MOTAC styles) --}}
    @include('layouts.sections.layout-styles')

    {{-- Include core JS helpers, theme config, and template customizer if enabled --}}
    @include('layouts.sections.layout-scripts-includes')

    {{-- Vite assets: main app and optional modules --}}
    @vite(['resources/js/app.js'])
</head>

<body>
    {{-- Accessibility skip link for screen readers/keyboard navigation --}}
    <a href="#main-content" class="visually-hidden-focusable">{{ __('Langkau ke Kandungan Utama') }}</a>

    {{-- The layout-specific content will be injected here --}}
    @yield('layoutContent')

    {{-- Include all common JavaScript files and main initialization --}}
    @include('layouts.sections.layout-scripts')
</body>
</html>
