<!DOCTYPE html>

@php
    // Get $configData from the Helper. Helpers.php should now provide locale-aware textDirection.
    // However, for maximum robustness for the 'dir' attribute, we'll derive it directly here too.
$configData = \App\Helpers\Helpers::appClasses(); // Assuming appClasses() is static and Helper is in App\Helpers
$currentLocale = app()->getLocale();
$textDirection = $currentLocale === 'ar' ? 'rtl' : 'ltr';
@endphp

<html lang="{{ $currentLocale }}"
    class="{{ $configData['style'] ?? 'light' }}-style {{ $navbarFixed ?? '' }} {{ $menuFixed ?? '' }} {{ $menuCollapsed ?? '' }} {{ $footerFixed ?? '' }} {{ $customizerHidden ?? '' }}"
    dir="{{ $textDirection }}" {{-- Explicitly set based on current locale --}} data-theme="{{ $configData['theme'] ?? 'theme-default' }}"
    data-assets-path="{{ asset('/assets') . '/' }}" data-base-url="{{ url('/') }}" data-framework="laravel"
    data-template="{{ $configData['layout'] ?? 'vertical' }}-menu-{{ $configData['theme'] ?? 'theme-default' }}-{{ $configData['style'] ?? 'light' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>
        @yield('title') | HRMS
    </title>
    <meta name="description"
        content="{{ config('variables.templateDescription') ? config('variables.templateDescription') : '' }}" />
    <meta name="keywords"
        content="{{ config('variables.templateKeyword') ? config('variables.templateKeyword') : '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ config('variables.productPage') ? config('variables.productPage') : '' }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.png') }}" />

    {{-- styles.blade.php will use $configData['rtlSupport'] from Helpers.php --}}
    @include('layouts/sections/styles')

    @include('layouts/sections/scriptsIncludes')
</head>

<body>

    @yield('layoutContent')
    @include('layouts/sections/scripts')

</body>

</html>
