{{-- resources/views/layouts/commonMaster.blade.php --}}
<!DOCTYPE html>

@php
    $configData = \App\Helpers\Helpers::appClasses();
    $currentLocale = app()->getLocale();
    $textDirection = $configData['textDirection'] ?? (($currentLocale === 'ar') ? 'rtl' : 'ltr'); // AR is just an example for RTL
    $activeTheme = $configData['style'] ?? 'light';
    $bsTheme = ($activeTheme === 'dark') ? 'dark' : 'light';
@endphp

<html lang="{{ $currentLocale }}"
    class="{{ $activeTheme }}-style {{ $configData['navbarFixed'] ?? '' }} {{ $configData['menuFixed'] ?? '' }} {{ $configData['menuCollapsed'] ?? '' }} {{ $configData['footerFixed'] ?? '' }} {{ $configData['customizerHidden'] ?? '' }}"
    dir="{{ $textDirection }}"
    data-theme="{{ $configData['theme'] ?? 'theme-motac' }}"
    data-bs-theme="{{ $bsTheme }}"
    data-assets-path="{{ asset('assets/') . '/' }}"
    data-base-url="{{ url('/') }}"
    data-framework="laravel"
    data-template="{{ ($configData['layout'] ?? 'vertical') . '-menu-' . ($configData['theme'] ?? 'theme-motac') . '-' . $activeTheme }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | {{ __($configData['templateName'] ?? __('Sistem Pengurusan Sumber MOTAC')) }}</title>
    <meta name="description"
        content="{{ __($configData['templateDescription'] ?? __('Sistem Dalaman Bersepadu untuk Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) bagi pengurusan permohonan emel dan pinjaman peralatan ICT.')) }}" />
    <meta name="keywords"
        content="{{ __($configData['templateKeyword'] ?? 'motac, bpm, sistem dalaman, pengurusan sumber, pinjaman ict, permohonan emel, malaysia') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ asset($configData['appFavicon'] ?? 'assets/img/favicon/favicon-motac.ico') }}" /> {{-- Use configurable favicon --}}

    @include('layouts/sections/styles')
    @include('layouts/sections/scriptsIncludes')
</head>

<body>
    @yield('layoutContent')
    @include('layouts/sections/scripts')
</body>
</html>
