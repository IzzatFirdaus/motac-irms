{{-- resources/views/layouts/app.blade.php --}}
@php
    // Set page configuration if available
    if (isset($pageConfigs)) {
        \App\Helpers\Helpers::updatePageConfig($pageConfigs);
    }

    // Retrieve system-wide configuration from the helper
    $configData = \App\Helpers\Helpers::appClasses();

    // --- Layout Control Variables ---
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);
    $container = $container ?? ($configData['container'] ?? 'container-fluid');
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);

    // --- CSS Class Strings for Layout States ---
    $navbarDetached = !empty($configData['navbarDetached']) ? 'navbar-detached' : '';
    $menuFixed = !empty($configData['menuFixed']) ? 'layout-menu-fixed' : '';
    $menuCollapsed = !empty($configData['menuCollapsed']) ? 'layout-menu-collapsed' : '';
    $navbarFixed = !empty($configData['navbarFixed']) ? 'layout-navbar-fixed' : '';
    $footerFixed = !empty($configData['footerFixed']) ? 'layout-footer-fixed' : '';
    $menuHover = !empty($configData['showDropdownOnHover']) ? 'layout-menu-hover' : '';
    $templateName = \Illuminate\Support\Str::slug(config('variables.templateName', 'Sistem MOTAC'), '-');

    // This server-side variable sets the initial theme for the page load to prevent FOUC.
    // The client-side script will take over from here.
    $currentThemeStyle = $configData['myStyle'] ?? 'light';

    // Prepare data for child components
    $currentUserRole = Auth::check() ? Auth::user()?->getRoleNames()->first() : null;

@endphp

{{-- The `class` and `data-bs-theme` attributes are set here on the server to prevent a "flash of unstyled content" --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="{{ $currentThemeStyle }}-style layout-navbar-fixed {{ $navbarFixed }} {{ $menuFixed }} {{ $footerFixed }} {{ $menuCollapsed }} {{ $menuHover }}"
    dir="{{ $configData['textDirection'] ?? 'ltr' }}" data-theme="{{ $configData['myTheme'] ?? 'theme-default' }}"
    data-bs-theme="{{ $currentThemeStyle }}" data-assets-path="{{ asset('assets/') . '/' }}"
    data-base-url="{{ url('/') }}" data-framework="laravel" data-template="{{ $templateName }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', __('Halaman Utama')) | {{ config('variables.templateName', 'Sistem MOTAC') }}</title>
    <meta name="description" content="@yield('description', config('variables.templateDescription', 'Sistem Pengurusan Sumber Bersepadu MOTAC'))" />
    <meta name="keywords" content="@yield('keywords', config('variables.templateKeyword', 'motac, bpm, sistem dalaman'))" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon-motac.ico') }}" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Core & Theme CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/core.css') }}" class="{{ $configData['hasCustomizer'] ?? false ? 'template-customizer-core-css' : '' }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/' . ($configData['myTheme'] ?? 'theme-default') . '.css') }}" class="{{ $configData['hasCustomizer'] ?? false ? 'template-customizer-theme-css' : '' }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-motac.css') }}" />


    {{-- Vendor Libs CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />

    {{-- Page-specific CSS --}}
    @yield('page-css')

    {{-- Helpers --}}
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    @include('layouts.sections.scriptsIncludes')

    @livewireStyles
    @stack('page-style')
</head>

<body>
    {{-- Main Layout Wrapper --}}
    <div class="layout-wrapper layout-content-navbar {{ $isNavbar ? '' : 'layout-without-navbar' }}">
        <div class="layout-container">
            @if ($isMenu)
                @livewire('sections.menu.vertical-menu', [
                    'menuData' => $menuData ?? null,
                    'role' => $currentUserRole,
                    'configData' => $configData,
                ])
            @endif

            <div class="layout-page">
                @if ($isNavbar)
                    {{-- The initial activeTheme is passed here to the navbar component --}}
                    @livewire('sections.navbar.navbar', [
                        'containerNav' => $configData['containerNav'] ?? 'container-fluid',
                        'navbarDetachedClass' => $navbarDetached,
                        'navbarFull' => $configData['navbarFull'] ?? true,
                        'navbarHideToggle' => ($configData['myLayout'] ?? 'vertical') === 'horizontal',
                    ])
                @endif

                <div class="content-wrapper" id="main-content">
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                    @else
                        <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                        @include('_partials._alerts.alert-general')

                        @if (isset($slot))
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endif
                    </div>

                    @if ($isFooter)
                        @livewire('sections.footer.footer')
                    @endif
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        @if ($isMenu)
            <div class="layout-overlay layout-menu-toggle"></div>
        @endif
        <div class="drag-target"></div>
    </div>

    @include('layouts.sections.scripts')
    @livewireScripts
    @stack('page-script')

    {{-- START: Finalized Theme Switcher JavaScript --}}
    <script>
        /**
         * MOTAC IRMS - Unified Theme Switcher Logic (Client-Side)
         *
         * This script manages the theme toggle instantly on the client-side
         * to avoid waiting for a server roundtrip.
         */
        (function() {
            'use strict';

            const themeStorageKey = 'theme-preference';
            const toggleAttrName = 'data-bs-toggle';
            const toggleAttrValue = 'theme';

            // Gets the theme preference from localStorage, or defaults to the system preference.
            const getThemePreference = () => {
                const storedTheme = localStorage.getItem(themeStorageKey);
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };

            // Sets the theme on the <html> element and updates all toggle icons and tooltips.
            const setTheme = (theme) => {
                localStorage.setItem(themeStorageKey, theme);
                document.documentElement.setAttribute('data-bs-theme', theme);

                document.querySelectorAll(`[${toggleAttrName}="${toggleAttrValue}"]`).forEach(toggle => {
                    const sunIcon = toggle.querySelector('.bi-sun-fill');
                    const moonIcon = toggle.querySelector('.bi-moon-stars-fill');

                    if (theme === 'dark') {
                        // In dark mode, the button should offer to switch to light mode.
                        toggle.setAttribute('title', 'Switch to light mode');
                        toggle.setAttribute('aria-label', 'Switch to light mode');
                        if (sunIcon) sunIcon.style.display = 'inline-block';
                        if (moonIcon) moonIcon.style.display = 'none';
                    } else {
                        // In light mode, the button should offer to switch to dark mode.
                        toggle.setAttribute('title', 'Switch to dark mode');
                        toggle.setAttribute('aria-label', 'Switch to dark mode');
                        if (sunIcon) sunIcon.style.display = 'none';
                        if (moonIcon) moonIcon.style.display = 'inline-block';
                    }
                });

                // Dispatch an event for Livewire or other parts of the app to listen to if needed.
                if (window.Livewire) {
                    window.Livewire.dispatch('themeHasChanged', {
                        theme: theme
                    });
                }
            };

            // Add click listeners to all theme togglers when the page is ready.
            window.addEventListener('DOMContentLoaded', () => {
                // Set the theme immediately on page load
                setTheme(getThemePreference());

                document.querySelectorAll(`[${toggleAttrName}="${toggleAttrValue}"]`).forEach(toggle => {
                    toggle.addEventListener('click', (event) => {
                        event.preventDefault();
                        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                        setTheme(newTheme);
                    });
                });
            });
        })();
    </script>
    {{-- END: Finalized Theme Switcher JavaScript --}}

</body>
</html>
