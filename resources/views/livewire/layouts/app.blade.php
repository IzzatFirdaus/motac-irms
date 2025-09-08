{{-- resources/views/livewire/layouts/app.blade.php --}}
{{-- MYDS-compliant main layout for MOTAC IRMS v4.0 --}}
{{--
    This layout applies MYDS colour tokens, typography, grid, accessibility, and component conventions.
    Ensure you have the correct Laravel helpers available in Blade:
    - config(), asset(), url(), __(), Auth, Str
    These are provided automatically in Laravel Blade views.
--}}

@php
    // Blade views have access to Laravel helpers/functions.
    // If you get IDE errors, ignore them—these work at runtime in Laravel.

    use Illuminate\Support\Str; // Required for Str::slug()
    use Illuminate\Support\Facades\Auth; // Required for Auth

    // Apply page-specific configuration if provided
    if (isset($pageConfigs)) {
        \App\Helpers\Helpers::updatePageConfig($pageConfigs);
    }

    // Main layout and theme configuration from helper
    $configData = \App\Helpers\Helpers::appClasses();

    // Layout toggles and CSS class configuration
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);
    $container = $container ?? ($configData['container'] ?? 'myds-container');
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'myds-container');
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);

    // CSS class helpers for layout states
    $navbarDetached = !empty($configData['navbarDetached']) ? 'navbar-detached' : '';
    $menuFixed = !empty($configData['menuFixed']) ? 'layout-menu-fixed' : '';
    $menuCollapsed = !empty($configData['menuCollapsed']) ? 'layout-menu-collapsed' : '';
    $navbarFixed = !empty($configData['navbarFixed']) ? 'layout-navbar-fixed' : '';
    $footerFixed = !empty($configData['footerFixed']) ? 'layout-footer-fixed' : '';
    $menuHover = !empty($configData['showDropdownOnHover']) ? 'layout-menu-hover' : '';
    $templateName = Str::slug(config('variables.templateName', 'MOTAC IRMS'), '-');

    // Theme style for initial load (light/dark)
    $currentThemeStyle = $configData['myStyle'] ?? 'light';

    // The $menuData variable is expected to be available from MenuServiceProvider
    $currentUserRole = Auth::check() ? Auth::user()->getRoleNames()->first() : null;
@endphp

{{--
    The <html> tag includes theme and direction attributes to prevent FOUC
    and ensure correct theme and RTL/LTR direction from the first paint.
--}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="{{ $currentThemeStyle }}-style layout-navbar-fixed {{ $navbarFixed }} {{ $menuFixed }} {{ $footerFixed }} {{ $menuCollapsed }} {{ $menuHover }}"
    dir="{{ $configData['textDirection'] ?? 'ltr' }}"
    data-theme="{{ $configData['myTheme'] ?? 'theme-default' }}"
    data-bs-theme="{{ $currentThemeStyle }}"
    data-assets-path="{{ asset('assets/') . '/' }}"
    data-base-url="{{ url('/') }}"
    data-framework="laravel"
    data-template="{{ $templateName }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    {{-- Dynamic page title and SEO meta --}}
    <title>@yield('title', __('Halaman Utama')) | {{ config('variables.templateName', 'MOTAC IRMS') }}</title>
    <meta name="description" content="@yield('description', config('variables.templateDescription', 'Sistem Pengurusan Sumber Bersepadu MOTAC'))" />
    <meta name="keywords" content="@yield('keywords', config('variables.templateKeyword', 'motac, bpm, sistem dalaman'))" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon-motac.ico') }}" />

    {{-- Fonts: MYDS standard --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    {{-- Poppins for headings, Inter for body --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- MYDS/Custom CSS: Core, theme, variables --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/' . ($configData['myTheme'] ?? 'theme-default') . '.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}" /> {{-- MYDS Color tokens --}}

    {{-- Vendor Libraries CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />

    {{-- Page-specific CSS --}}
    @yield('page-css')

    {{-- Helpers.js for layout/theme utilities --}}
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    @include('layouts.sections.layout-scripts-includes')

    {{-- Livewire styles --}}
    @livewireStyles

    @stack('page-style')
    {{-- MYDS: Add custom <style> for typography and grid if not in CSS --}}
    <style>
        html, body {
            font-family: 'Inter', 'Noto Sans', Arial, sans-serif;
            background-color: var(--bg-white);
            color: var(--txt-black-900);
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 600;
        }
        .myds-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        @media (max-width: 1023px) {
            .myds-container {
                max-width: 100%;
                padding: 0 24px;
            }
        }
        @media (max-width: 767px) {
            .myds-container {
                max-width: 100%;
                padding: 0 18px;
            }
        }
    </style>
</head>

<body>
    {{-- Skip Link for accessibility (MYDS standard) --}}
    <a href="#main-content" class="myds-skip-link">Langkau ke kandungan utama</a>

    {{-- Main Layout Wrapper: MYDS grid and layout --}}
    <div class="layout-wrapper layout-content-navbar {{ $isNavbar ? '' : 'layout-without-navbar' }}">
        <div class="layout-container myds-row">
            @if ($isMenu)
                {{-- Sidebar Menu (Vertical) --}}
                <nav class="myds-sidebar" aria-label="Navigasi utama">
                    @livewire('sections.menu.vertical-menu', [
                        'menuData' => $menuData ?? null,
                        'role' => $currentUserRole,
                        'configData' => $configData,
                    ])
                </nav>
            @endif

            <div class="layout-page myds-col-12 myds-col-md-9">
                @if ($isNavbar)
                    {{-- Top Navbar (MYDS-compliant) --}}
                    <header class="myds-navbar {{ $navbarDetached }}">
                        @livewire('sections.navbar.navbar', [
                            'containerNav' => $containerNav,
                            'navbarDetachedClass' => $navbarDetached,
                        ])
                    </header>
                @endif

                {{-- Main Page Content --}}
                <main class="content-wrapper myds-bg-white" id="main-content" tabindex="-1">
                    @if ($isFlex)
                        {{-- Flexible container for full-height layouts --}}
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                    @else
                        <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                        {{-- MYDS: General Alerts --}}
                        @include('_partials._alerts.alert-general')

                        {{-- Main content --}}
                        @if (isset($slot))
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endif
                    </div>
                </main>

                @if ($isFooter)
                    {{-- Footer: MYDS-compliant --}}
                    <footer class="myds-footer">
                        @include('layouts.sections.footer.footer-section')
                    </footer>
                @endif
                <div class="content-backdrop fade"></div>
            </div>
        </div>

        @if ($isMenu)
            {{-- Overlay for mobile/side menu --}}
            <div class="layout-overlay layout-menu-toggle"></div>
        @endif
        <div class="drag-target"></div>
    </div>

    {{-- Scripts --}}
    @include('layouts.sections.layout-scripts')
    @livewireScripts
    @stack('page-script')

    {{-- Theme Switcher: MYDS, supports light/dark toggle and Livewire sync --}}
    <script>
        /**
         * MOTAC IRMS - Unified Theme Switcher Logic (MYDS-compliant)
         * Allows user to toggle between light/dark theme and notifies Livewire
         */
        (function () {
          'use strict';
          const themeStorageKey = 'theme-preference';

          // Get saved theme or detect system preference
          const getThemePreference = () => {
            const preference = localStorage.getItem(themeStorageKey);
            if (preference) {
              return preference;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
          };

          // Apply theme and persist to localStorage
          const setTheme = (theme) => {
            localStorage.setItem(themeStorageKey, theme);
            document.documentElement.setAttribute('data-bs-theme', theme);
            document.documentElement.setAttribute('data-theme', theme === 'dark' ? 'theme-dark' : 'theme-light');
          };

          // Expose global toggle function for theme switching (e.g., from navbar)
          window.toggleAppTheme = () => {
            const currentTheme = getThemePreference();
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);

            // Notify Livewire components (e.g., Navbar) of theme change
            if (window.Livewire) {
                window.Livewire.dispatch('themeHasChanged', { theme: newTheme });
            }
          };

          // Set theme at page load to prevent flickering
          setTheme(getThemePreference());
        })();
    </script>
</body>
</html>
