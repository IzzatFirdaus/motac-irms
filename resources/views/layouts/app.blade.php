@php
    // Config data initialization from Helper or defaults
    $configData = class_exists(App\Helpers\Helpers::class) && method_exists(App\Helpers\Helpers::class, 'appClasses')
            ? App\Helpers\Helpers::appClasses()
            : ['templateName' => 'MOTAC RMS', 'textDirection' => 'ltr']; // Sensible defaults [cite: 5]

    // Theme mode for Bootstrap 5.3+
    // Assuming $configData['style'] would be 'light' or 'dark' if provided by Helpers
    $themeMode = $configData['style'] ?? (Cookie::get('theme_mode', 'light')); // Example: Use cookie or default to light [cite: 5]

    // Text direction based on locale or session
    $currentLocale = str_replace('_', '-', app()->getLocale());
    $textDirection = $configData['textDirection'] ?? ($currentLocale === 'ar' ? 'rtl' : 'ltr'); // [cite: 5]

    // Layout settings, can be overridden by specific page views
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true); // Show sidebar menu by default [cite: 5]
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true); // Show navbar by default [cite: 5]
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true); // Show footer by default [cite: 5]
    $containerClass = $container ?? ($configData['container'] ?? 'container-fluid'); // Use 'container-fluid' for backend apps [cite: 5]

    // Navbar specific properties
    $pageComponentNavbarFull = $navbarFull ?? ($configData['navbarFull'] ?? false); // [cite: 5]
    $pageComponentContainerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid'); // [cite: 5]
@endphp
<!DOCTYPE html>
<html lang="{{ $currentLocale }}" data-bs-theme="{{ $themeMode }}" dir="{{ $textDirection }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $configData['templateName'] ?? config('app.name', 'MOTAC RMS'))</title> {{-- [cite: 5] --}}

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" /> {{-- [cite: 5] --}}

    {{-- Core CSS (Bootstrap & Theme) --}}
    @include('layouts.sections.styles') {{-- This should include your compiled Bootstrap CSS [cite: 5] --}}

    {{-- Page-specific Styles --}}
    @yield('page-style')
    @stack('styles_before') {{-- Stack for styles before Livewire --}}
    @livewireStyles
    @stack('styles_after') {{-- Stack for styles after Livewire --}}
</head>

<body class="d-flex flex-column min-vh-100">

    <div class="layout-wrapper d-flex flex-grow-1">
        @if ($isMenu)
            {{-- Sidebar Menu (Livewire Component) --}}
            @livewire('sections.menu.vertical-menu', ['isMobile' => false]) {{-- [cite: 5] --}}

            {{-- Mobile Offcanvas Menu --}}
            <div class="offcanvas {{ $textDirection === 'rtl' ? 'offcanvas-end' : 'offcanvas-start' }}" tabindex="-1" id="mobileMenuOffcanvas" aria-labelledby="mobileMenuOffcanvasLabel"> {{-- [cite: 5] --}}
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobileMenuOffcanvasLabel">{{ $configData['templateName'] ?? config('app.name', 'MOTAC RMS') }}</h5> {{-- [cite: 5] --}}
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body p-0">
                    @livewire('sections.menu.vertical-menu', ['isMobile' => true]) {{-- [cite: 5] --}}
                </div>
            </div>
        @endif

        {{-- Layout Content --}}
        <div class="layout-page d-flex flex-column flex-grow-1">
            @if ($isNavbar)
                {{-- Navbar (Livewire Component) --}}
                {{-- Pass necessary props for detached/full navbar if your component supports it --}}
                @livewire('sections.navbar.navbar', [
                    'containerNav' => $pageComponentContainerNav,
                    // 'navbarDetached' => $configData['navbarDetached'] ?? '',
                    'navbarFull' => $pageComponentNavbarFull
                ]) {{-- [cite: 5] --}}
            @endif

            {{-- Content Area --}}
            <main class="content-wrapper flex-grow-1 py-3 py-md-4">
                {{-- Main container for page content --}}
                <div class="{{ $containerClass }}">
                    {{-- Flash Messages / Banners --}}
                    <x-jet-banner /> {{-- If using Jetstream banner [cite: 5] --}}
                    @include('_partials._alerts.alert-general') {{-- Your custom alerts partial --}}

                    {{ $slot ?? '' }}  {{-- For Livewire full-page components --}}
                    @yield('content')   {{-- For traditional Blade views --}}
                </div>
            </main>
            {{-- / Content Area --}}

            @if ($isFooter)
                {{-- Footer (Livewire Component or Partial) --}}
                @livewire('sections.footer.footer') {{-- [cite: 5] --}}
            @endif
        </div>
        {{-- / Layout Content --}}
    </div>

    {{-- Core JS (Bootstrap Bundle, Theme) --}}
    @include('layouts.sections.scripts') {{-- This should include Bootstrap JS [cite: 5] --}}

    {{-- Page-specific Scripts --}}
    @yield('page-script')
    @stack('scripts_before') {{-- Stack for scripts before Livewire --}}
    @livewireScripts
    @stack('scripts_after') {{-- Stack for scripts after Livewire --}}
</body>
</html>
