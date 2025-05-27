@php
    // Config data initialization.
    // The system design document mentions that AppServiceProvider may share global data
    // like UI configuration from Helpers::appClasses() using View::share(). If $configData is already
    // globally shared and available here, this direct initialization might be redundant.
    // However, this ensures $configData is defined for this layout with sensible defaults if not globally provided.
    $configData = class_exists(App\Helpers\Helpers::class) && method_exists(App\Helpers\Helpers::class, 'appClasses')
            ? App\Helpers\Helpers::appClasses()
            : ['templateName' => 'MOTAC RMS', 'textDirection' => 'ltr', 'style' => 'light']; // Added 'style' to defaults for $themeMode

    // Theme mode for Bootstrap 5.3+
    $themeMode = $configData['style'] ?? (Cookie::get('theme_mode', 'light')); //

    // Text direction
    $currentLocale = str_replace('_', '-', app()->getLocale());
    $textDirection = $configData['textDirection'] ?? ($currentLocale === 'ar' ? 'rtl' : 'ltr'); //

    // Layout settings
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true); //
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true); //
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true); //
    $containerClass = $container ?? ($configData['container'] ?? 'container-fluid'); //

    // Navbar specific properties
    $pageComponentNavbarFull = $navbarFull ?? ($configData['navbarFull'] ?? false); //
    $pageComponentContainerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid'); //
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" data-bs-theme="{{ $themeMode }}" dir="{{ $textDirection }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $configData['templateName'] ?? config('app.name', 'MOTAC RMS'))</title> {{-- --}}

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" /> {{-- --}}

    {{-- Core CSS (Bootstrap & Theme) --}}
    @include('layouts.sections.styles') {{-- This should include your compiled Bootstrap CSS --}}

    {{-- Page-specific Styles --}}
    @yield('page-style')
    @stack('styles_before')
    @livewireStyles
    @stack('styles_after')
</head>

<body class="d-flex flex-column min-vh-100">

    <div class="layout-wrapper d-flex flex-grow-1">
        @if ($isMenu) {{-- --}}
            {{-- Sidebar Menu (Livewire Component) --}}
            @livewire('sections.menu.vertical-menu', ['isMobile' => false]) {{-- --}}

            {{-- Mobile Offcanvas Menu --}}
            <div class="offcanvas {{ $textDirection === 'rtl' ? 'offcanvas-end' : 'offcanvas-start' }}" tabindex="-1" id="mobileMenuOffcanvas" aria-labelledby="mobileMenuOffcanvasLabel"> {{-- --}}
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobileMenuOffcanvasLabel">{{ $configData['templateName'] ?? config('app.name', 'MOTAC RMS') }}</h5> {{-- --}}
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    @livewire('sections.menu.vertical-menu', ['isMobile' => true]) {{-- --}}
                </div>
            </div>
        @endif

        {{-- Layout Content --}}
        <div class="layout-page d-flex flex-column flex-grow-1">
            @if ($isNavbar) {{-- --}}
                {{-- Navbar (Livewire Component) --}}
                @livewire('sections.navbar.navbar', [
                    'containerNav' => $pageComponentContainerNav,
                    'navbarFull' => $pageComponentNavbarFull
                ]) {{-- --}}
            @endif

            {{-- Content Area --}}
            <main class="content-wrapper flex-grow-1 py-3 py-md-4">
                <div class="{{ $containerClass }}"> {{-- --}}
                    {{-- Flash Messages / Banners --}}
                    {{-- The following <x-jet-banner /> was causing an "Unable to locate component" error. --}}
                    {{-- It's a Jetstream component. If Jetstream is not fully installed or this banner is not needed (MOTAC design has _alerts.alert-general), it should be commented out. --}}
                    {{-- <x-jet-banner /> --}} {{-- --}}

                    @include('_partials._alerts.alert-general') {{-- Your custom alerts partial, as per MOTAC design --}}

                    {{ $slot ?? '' }}  {{-- For Livewire full-page components --}}
                    @yield('content')   {{-- For traditional Blade views --}}
                </div>
            </main>
            {{-- / Content Area --}}

            @if ($isFooter) {{-- --}}
                {{-- Footer (Livewire Component or Partial) --}}
                @livewire('sections.footer.footer') {{-- --}}
            @endif
        </div>
        {{-- / Layout Content --}}
    </div>

    {{-- Core JS (Bootstrap Bundle, Theme) --}}
    @include('layouts.sections.scripts') {{-- This should include Bootstrap JS --}}

    {{-- Page-specific Scripts --}}
    @yield('page-script')
    @stack('scripts_before')
    @livewireScripts
    @stack('scripts_after')
</body>
</html>
