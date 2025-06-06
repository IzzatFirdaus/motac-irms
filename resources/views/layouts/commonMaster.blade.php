{{-- resources/views/layouts/commonMaster.blade.php --}}
<!DOCTYPE html>

@php
    // Fetch application configuration using the App\Helpers\Helpers class.
    $configData = \App\Helpers\Helpers::appClasses();

    // Determine current locale and text direction.
    // Design Language 1.2: Bahasa Melayu First (ltr).
    $currentLocale = app()->getLocale();
    $textDirection = $configData['textDirection'] ?? ($currentLocale === 'ar' ? 'rtl' : 'ltr'); // Example 'ar' for RTL

    // Determine active theme (light/dark) for Bootstrap.
    // Design Language 5.0: Dark Mode Specification.
    $activeTheme = $configData['myStyle'] ?? 'light'; // Use myStyle consistent with app.blade.php
    $bsTheme = $activeTheme === 'dark' ? 'dark' : 'light';

    // Define a default MOTAC application name if not provided by $configData
    $appName = __($configData['templateName'] ?? __('Sistem Pengurusan Sumber Bersepadu MOTAC'));
@endphp

<html lang="{{ $currentLocale }}"
    class="{{ $activeTheme }}-style {{ $configData['navbarFixed'] ?? '' }} {{ $configData['menuFixed'] ?? '' }} {{ $configData['menuCollapsed'] ?? '' }} {{ $configData['footerFixed'] ?? '' }} {{ $configData['customizerHidden'] ?? '' }}"
    dir="{{ $textDirection }}" data-theme="{{ $configData['myTheme'] ?? 'theme-motac' }}" {{-- Ensure this theme aligns with MOTAC specs --}}
    data-bs-theme="{{ $bsTheme }}" data-assets-path="{{ asset('assets/') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel"
    data-template="{{ ($configData['myLayout'] ?? 'vertical') . '-menu-' . ($configData['myTheme'] ?? 'theme-motac') . '-' . $activeTheme }}"> {{-- Use myLayout and myTheme --}}

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
    {{-- Using the specified MOTAC favicon directly. --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon-motac.ico') }}" />

    {{-- MOTAC Typography: Noto Sans (Design Language 2.2) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    @include('layouts/sections/styles')
    @include('layouts/sections/scriptsIncludes')

    <style>
        body {
            font-family: 'Noto Sans', sans-serif !important; /* */
            line-height: 1.6; /* */
            background-color: var(--motac-background, #F8F9FA); /* */
            color: var(--motac-text, #212529); /* */
        }
        :root {
            --motac-primary: #0055A4; /* */
            --motac-secondary: #8C1D40; /* */
            --motac-success: #28A745; /* */
            --motac-danger: #DC3545; /* */
            --motac-warning: #FFC107; /* */
            --motac-info: #0dcaf0;  /* Default BS info, Design Doc 2.1 allows adjustment */
            --motac-light: #F8F9FA; /* */
            --motac-dark: #212529; /* */
            --motac-background: #F8F9FA; /* */
            --motac-surface: #FFFFFF; /* */
            --motac-text: #212529; /* */
            --motac-text-muted: #6C757D;
            --motac-border: #DEE2E6;
            --bs-primary: var(--motac-primary);
            --bs-secondary: var(--motac-secondary);
            --bs-success: var(--motac-success);
            --bs-danger: var(--motac-danger);
        }
        [data-bs-theme="dark"] {
            --motac-primary: #3D8FD1; /* */
            --motac-secondary: #A9496B; /* */
            --motac-success: #4ADE80; /* */
            --motac-danger: #F87171; /* */
            --motac-warning: #FACC15; /* */
            --motac-info: #38BDF8; /* Example dark info from previous HTML */
            --motac-background: #121826; /* */
            --motac-surface: #1E293B; /* */
            --motac-text: #E9ECEF; /* */
            --motac-text-muted: #ADB5BD;
            --motac-border: #495057; /* From previous HTML, consistent with dark mode */
            --bs-primary: var(--motac-primary);
            --bs-secondary: var(--motac-secondary);
            --bs-success: var(--motac-success);
            --bs-danger: var(--motac-danger);
            --bs-body-bg: var(--motac-background);
            --bs-body-color: var(--motac-text);
            --bs-border-color: var(--motac-border);
        }
        *:focus-visible {
            outline: 3px solid var(--motac-primary, #0055A4) !important; /* */
            outline-offset: 2px; /* */
            box-shadow: none !important;
        }
    </style>
</head>

<body>
    <a href="#main-content" class="visually-hidden-focusable">{{ __('Langkau ke Kandungan Utama') }}</a> {{-- --}}
    @yield('layoutContent')
    @include('layouts/sections/scripts')
</body>
</html>
