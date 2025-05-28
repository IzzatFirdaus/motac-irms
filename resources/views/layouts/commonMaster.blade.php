{{-- resources/views/layouts/commonMaster.blade.php --}}
<!DOCTYPE html>

@php
    // Get $configData from the Helper.
    // System Design: Section 3.3, 4.1, App\Helpers\Helpers::appClasses()
    $configData = \App\Helpers\Helpers::appClasses();
    $currentLocale = app()->getLocale(); // e.g., 'ms', 'en'

    // Determine text direction based on current locale from $configData (which gets it from session or config)
    // Design Language: Bahasa Melayu as Primary Language (Keutamaan Bahasa Melayu) -> 'ltr'
    $textDirection = $configData['textDirection'] ?? (($currentLocale === 'ar') ? 'rtl' : 'ltr');

    // Determine data-bs-theme attribute for Bootstrap 5.3+ dark mode handling
    // Design Language: User-selectable dark mode
    $activeTheme = $configData['style'] ?? 'light'; // 'light' or 'dark'
    $bsTheme = ($activeTheme === 'dark') ? 'dark' : 'light'; // This is correctly derived in Helpers.php too
@endphp

<html lang="{{ $currentLocale }}"
    class="{{ $activeTheme }}-style {{ $configData['navbarFixed'] ?? '' }} {{ $configData['menuFixed'] ?? '' }} {{ $configData['menuCollapsed'] ?? '' }} {{ $configData['footerFixed'] ?? '' }} {{ $configData['customizerHidden'] ?? '' }}"
    dir="{{ $textDirection }}"
    data-theme="{{ $configData['theme'] ?? 'theme-motac' }}" {{-- MOTAC-specific clean theme --}}
    data-bs-theme="{{ $bsTheme }}" {{-- Bootstrap 5.3+ dark mode attribute --}}
    data-assets-path="{{ asset('assets/') . '/' }}" {{-- Standard asset path --}}
    data-base-url="{{ url('/') }}"
    data-framework="laravel"
    data-template="{{ $configData['layout'] ?? 'vertical' }}-menu-{{ $configData['theme'] ?? 'theme-motac' }}-{{ $activeTheme }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    {{-- Design Language: Professionalism & Trustworthiness, Prominent MOTAC Branding --}}
    <title>@yield('title') | {{ $configData['templateName'] ?? __('Sistem Pengurusan Sumber MOTAC') }}</title>
    <meta name="description"
        content="{{ $configData['templateDescription'] ?? __('Sistem Dalaman Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia.') }}" />
    <meta name="keywords"
        content="{{ $configData['templateKeyword'] ?? 'motac, bpm, sistem dalaman, pengurusan sumber, pinjaman ict, permohonan emel' }}">
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Favicon - Design Language: Prominent MOTAC Branding --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon-motac.ico') }}" />

    {{-- Include Styles - This will pull in styles.blade.php --}}
    {{-- System Design: "The Big Picture" (Stylesheet Inclusion) --}}
    @include('layouts/sections/styles')

    {{-- Include Scripts for customizer, helper, analytics, config --}}
    {{-- System Design: "The BigPicture" (Initial JavaScript Configurations) --}}
    @include('layouts/sections/scriptsIncludes')
</head>

<body>
    {{-- Layout Content --}}
    @yield('layoutContent')
    {{--/ Layout Content --}}

    {{-- Include Scripts --}}
    {{-- System Design: "The Big Picture" (Global JavaScript Execution) --}}
    @include('layouts/sections/scripts')

</body>

</html>
