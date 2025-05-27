<!DOCTYPE html>

@php
    // $configData is initialized here using the App\Helpers\Helpers class.
    // This $configData instance is then available to this master layout and any views that extend it (like app.blade.php).
    // This aligns with System Design 3.3 for AppServiceProvider sharing UI config from Helpers.
    // and "The Big Picture" flow.
    try {
        $configData = \App\Helpers\Helpers::appClasses();
    } catch (\Exception $e) {
        // Fallback in case Helpers class or appClasses method has an issue.
        // This prevents a fatal error and allows the page to render with basic defaults.
        Log::critical('CRITICAL ERROR fetching appClasses in commonMaster.blade.php: ' . $e->getMessage());
        $configData = [
            'templateName' => env('APP_NAME', 'MOTAC RMS'),
            'templateDescription' => 'MOTAC Resource Management System',
            'templateKeyword' => 'motac, resource management',
            'locale' => 'en',
            'textDirection' => 'ltr',
            'style' => 'light', // light, dark
            'theme' => 'theme-default',
            'layout' => 'vertical',
            'navbarFixed' => false,
            'menuFixed' => false,
            'menuCollapsed' => false,
            'footerFixed' => false,
            'customizerHidden' => true, // Sensible default if config fails
            'assetsPath' => asset('/assets') . '/',
            'baseUrl' => url('/'),
            'primaryColor' => '#7367f0', // A default primary color
            // Add other essential keys that layouts/sections might expect
            'contentNavbar' => true,
            'containerNav' => 'container-xxl',
            'isNavbar' => true,
            'isMenu' => true,
            'isFlex' => false,
            'isFooter' => true,
            'navbarDetached' => false,
            'container' => 'container-xxl',
            'rtlSupport' => '', // Default to no RTL asset path modification
            'displayCustomizer' => false,
        ];
    }

    $currentLocale = $configData['locale'] ?? str_replace('_', '-', app()->getLocale());
    $textDirection = $configData['textDirection'] ?? 'ltr';
    $themeMode = $configData['style'] ?? 'light'; // Expected by data-bs-theme
    $themeName = $configData['theme'] ?? 'theme-default';
    $layoutName = $configData['layout'] ?? 'vertical';
    $assetsPath = $configData['assetsPath'] ?? asset('/assets') . '/';
    $baseUrl = $configData['baseUrl'] ?? url('/');
@endphp

<html lang="{{ $currentLocale }}"
    class="{{ $themeMode }}-style {{ $configData['navbarFixed'] ? 'layout-navbar-fixed' : '' }} {{ $configData['menuFixed'] ? 'layout-menu-fixed' : '' }} {{ $configData['menuCollapsed'] ? 'layout-menu-collapsed' : '' }} {{ $configData['footerFixed'] ? 'layout-footer-fixed' : '' }} {{ $configData['customizerHidden'] ? 'customizer-hide' : '' }}"
    dir="{{ $textDirection }}"
    data-bs-theme="{{ $themeMode }}"
    data-theme="{{ $themeName }}"
    data-assets-path="{{ $assetsPath }}"
    data-base-url="{{ $baseUrl }}"
    data-framework="laravel"
    data-template="{{ $layoutName }}-menu-{{ $themeName }}-{{ $themeMode }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    {{-- The title is yielded here; Livewire components like Dashboard.php will set it using #[Title] attribute --}}
    <title>@yield('title', $configData['templateName'] ?? config('app.name', 'MOTAC RMS'))</title>

    <meta name="description" content="{{ $configData['templateDescription'] ?? 'Sistem Pengurusan Sumber Bersepadu MOTAC' }}" />
    <meta name="keywords" content="{{ $configData['templateKeyword'] ?? 'motac, pengurusan sumber, pinjaman ict, permohonan emel' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon" href="{{ $assetsPath }}img/favicon/favicon.ico" />

    {{-- styles.blade.php will use the globally available $configData (or the one passed by AppServiceProvider) --}}
    @include('layouts.sections.styles')

    {{-- scriptsIncludes.blade.php also relies on $configData --}}
    @include('layouts.sections.scriptsIncludes')

    @yield('vendor-style')
    @yield('page-style')
    @stack('custom-css')
</head>

<body>
    {{-- Layout content from extending Blade files (e.g., app.blade.php) will be injected here --}}
    @yield('layoutContent')

    @include('layouts.sections.scripts')

    @yield('vendor-script')
    @yield('page-script')
    @stack('custom-scripts')
</body>
</html>
