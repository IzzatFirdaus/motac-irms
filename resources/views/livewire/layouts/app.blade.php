{{-- resources/views/livewire/layouts/app.blade.php --}}
@php
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
    $container = $container ?? ($configData['container'] ?? 'container-fluid');
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid');
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);

    // CSS class helpers for layout states
    $navbarDetached = !empty($configData['navbarDetached']) ? 'navbar-detached' : '';
    $menuFixed = !empty($configData['menuFixed']) ? 'layout-menu-fixed' : '';
    $menuCollapsed = !empty($configData['menuCollapsed']) ? 'layout-menu-collapsed' : '';
    $navbarFixed = !empty($configData['navbarFixed']) ? 'layout-navbar-fixed' : '';
    $footerFixed = !empty($configData['footerFixed']) ? 'layout-footer-fixed' : '';
    $menuHover = !empty($configData['showDropdownOnHover']) ? 'layout-menu-hover' : '';
    $templateName = \Illuminate\Support\Str::slug(config('variables.templateName', 'MOTAC IRMS'), '-');

    // Theme style for initial load (light/dark)
    $currentThemeStyle = $configData['myStyle'] ?? 'light';

    // The $menuData variable is expected to be available from MenuServiceProvider
    $currentUserRole = Auth::check() ? Auth::user()?->getRoleNames()->first() : null;
@endphp

{{--
    The <html> tag includes theme and direction attributes to prevent FOUC (Flash of Unstyled Content)
    and to ensure correct theme and RTL/LTR direction from the first paint.
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

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Core & Theme CSS: core.css and theme-default.css or selected theme --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/core.css') }}" class="{{ $configData['hasCustomizer'] ?? false ? 'template-customizer-core-css' : '' }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/' . ($configData['myTheme'] ?? 'theme-default') . '.css') }}" class="{{ $configData['hasCustomizer'] ?? false ? 'template-customizer-theme-css' : '' }}" />

    {{-- Vendor Libraries CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />

    {{-- Page-specific CSS (can be injected via @section('page-css')) --}}
    @yield('page-css')

    {{-- Helpers.js for layout/theme utilities --}}
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    {{-- Updated include with new naming convention --}}
    @include('layouts.sections.layout-scripts-includes')

    {{-- Livewire styles --}}
    @livewireStyles

    {{-- Stack for page-level style overrides --}}
    @stack('page-style')
</head>

<body>
    {{-- Main Layout Wrapper --}}
    <div class="layout-wrapper layout-content-navbar {{ $isNavbar ? '' : 'layout-without-navbar' }}">
        <div class="layout-container">
            @if ($isMenu)
                {{-- Sidebar Menu (Vertical) - now using the renamed Livewire component --}}
                @livewire('sections.menu.vertical-menu', [
                    'menuData' => $menuData ?? null,
                    'role' => $currentUserRole,
                    'configData' => $configData,
                ])
            @endif

            <div class="layout-page">
                @if ($isNavbar)
                    {{--
                        Top Navbar - Uses the new Livewire Navbar component.
                        This replaces the old navbar-user-profile partial, and provides
                        language switcher, theme toggle, notifications, and profile dropdown.
                        Passes container and detachment class to the component.
                    --}}
                    @livewire('sections.navbar.navbar', [
                        'containerNav' => $containerNav,
                        'navbarDetachedClass' => $navbarDetached,
                    ])
                @endif

                <div class="content-wrapper" id="main-content">
                    @if ($isFlex)
                        {{-- Flexible container for full-height layouts --}}
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                    @else
                        {{-- Default container with padding --}}
                        <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                        {{-- System-wide general alerts (e.g., success/error messages) --}}
                        @include('_partials._alerts.alert-general')

                        {{-- Main page content: support both Blade slot and yield --}}
                        @if (isset($slot))
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endif
                    </div>

                    @if ($isFooter)
                        {{-- Footer section - now using the correct Blade partial --}}
                        @include('layouts.sections.footer.footer-section')
                    @endif
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>

        @if ($isMenu)
            {{-- Overlay for mobile/side menu --}}
            <div class="layout-overlay layout-menu-toggle"></div>
        @endif
        <div class="drag-target"></div>
    </div>

    {{-- Scripts: includes all vendor, core, and page-level scripts --}}
    @include('layouts.sections.layout-scripts')

    {{-- Livewire scripts --}}
    @livewireScripts

    {{-- Stack for page-level scripts --}}
    @stack('page-script')

    {{--
        Theme Switcher: Handles user theme (light/dark) preference.
        Sets theme on page load and toggles on demand, also notifies Livewire of changes.
    --}}
    <script>
        /**
         * MOTAC IRMS - Unified Theme Switcher Logic
         * Allows user to toggle between light/dark theme and notifies Livewire navbar
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
