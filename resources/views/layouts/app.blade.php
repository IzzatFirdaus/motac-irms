{{-- resources/views/layouts/app.blade.php --}}
{{--
  Main application layout for authenticated users (typically for non-full-page Livewire views or traditional Blade views).
  This layout includes the primary navigation (sidebar/topbar), content area, and footer.
  It should provide global setups like Noto Sans font, MOTAC color variables, and base accessibility features.
--}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!} {{-- Theme-specific page configuration helper --}}
@endisset

@php
    // Retrieve system-wide configuration (e.g., theme style, layout toggles) from a helper.
    $configData = \App\Helpers\Helpers::appClasses(); // [cite: 1]

    // Visibility of core layout elements
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true); // [cite: 1]
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true); // [cite: 1]
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true); // [cite: 1]

    // Content area styling
    $container = $container ?? ($configData['container'] ?? 'container-fluid'); // Default for main content container // [cite: 1]
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false); // [cite: 1]

    // CSS Class Strings for Layout States based on $configData
    $navbarDetached = !empty($configData['navbarDetached']) ? 'navbar-detached' : ''; // [cite: 1]
    $menuFixed = !empty($configData['menuFixed']) ? 'layout-menu-fixed' : ''; // [cite: 1]
    $menuCollapsed = !empty($configData['menuCollapsed']) ? 'layout-menu-collapsed' : ''; // [cite: 1]
    $navbarFixed = !empty($configData['navbarFixed']) ? 'layout-navbar-fixed' : ''; // [cite: 1]
    $footerFixed = !empty($configData['footerFixed']) ? 'layout-footer-fixed' : ''; // [cite: 1]
    $menuHover = !empty($configData['showDropdownOnHover']) ? 'layout-menu-hover' : ''; // [cite: 1]

    // If APP_NAME is multi-word, slugify it for use in data-template
    $templateName = \Illuminate\Support\Str::slug(config('app.name', 'laravel-motac-irms'), '-'); // [cite: 1]

    // Determine the active theme style for data-bs-theme and class
    $currentThemeStyle = $configData['myStyle'] ?? 'light'; // [cite: 1]
@endphp

{{-- Main HTML structure --}}
<html lang="{{ app()->getLocale() }}"
    class="{{ $currentThemeStyle }}-style layout-navbar-fixed {{ $navbarFixed }} {{ $menuFixed }} {{ $footerFixed }} {{ $menuCollapsed }} {{ $menuHover }}" {{-- REVISED: Dynamic theme style class --}}
    dir="{{ $configData['textDirection'] ?? 'ltr' }}"
    data-theme="{{ $configData['myTheme'] ?? 'theme-default' }}"
    data-bs-theme="{{ $currentThemeStyle }}" {{-- ADDED: Crucial for Bootstrap 5 theme switching & JS logic --}}
    data-assets-path="{{ asset('assets/') . '/' }}"
    data-base-url="{{ url('/') }}"
    data-framework="laravel"
    data-template="{{ $templateName }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | {{ config('app.name', 'MOTAC IRMS') }}</title>
    <meta name="description" content="@yield('description', config('variables.templateDescription'))" />
    <meta name="keywords" content="@yield('keywords', config('variables.templateKeyword'))" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Noto+Sans+JP:wght@300;400;500;600;700&family=Noto+Sans+KR:wght@300;400;500;600;700&family=Noto+Sans+Display:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    {{-- Bootstrap Icons (ensure this is included for icon usage) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/core.css') }}"
        class="{{ ($configData['hasCustomizer'] ?? false) ? 'template-customizer-core-css' : '' }}" />
    {{-- CORRECTED: Used 'hasCustomizer' and null coalescing --}}

    {{-- Theme CSS --}}
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/' . ($configData['myTheme'] ?? 'theme-default') . '.css') }}"
        class="{{ ($configData['hasCustomizer'] ?? false) ? 'template-customizer-theme-css' : '' }}" />
    {{-- CORRECTED: Used 'hasCustomizer' and null coalescing --}}

    {{-- Demo Specific CSS / Your Custom App CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    {{-- Ensure your actual MOTAC theme CSS (e.g., theme-motac.css) is linked, usually via the $configData['myTheme'] variable above --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/theme-motac.css') }}"> --}}


    {{-- Vendor Libs CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />

    @yield('vendor-css') {{-- For page-specific vendor CSS --}}

    @yield('page-css') {{-- For page-specific custom CSS --}}

    {{-- Helpers --}}
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>

    {{-- Theme Customizer Includes (conditionally based on config) --}}
    @include('layouts.sections.scriptsIncludes') {{-- This loads template-customizer.js and config.js --}}

    {{-- Config JS (application-specific JS configurations) - Note: This is loaded by scriptsIncludes.blade.php typically --}}
    {{-- <script src="{{ asset('assets/js/config.js') }}"></script> --}} {{-- This might be redundant if scriptsIncludes handles it --}}

    {{-- Livewire Styles --}}
    @livewireStyles

    {{-- Stack for custom CSS from child views --}}
    @stack('styles')
</head>

<body>
    {{-- Layout wrapper --}}
    <div class="layout-wrapper layout-content-navbar {{ $isNavbar ? '' : 'layout-without-navbar' }}"> {{-- [cite: 1] --}}
        <div class="layout-container">
            {{-- Menu --}}
            @if ($isMenu)
                @livewire('sections.menu.vertical-menu') {{-- [cite: 1] --}}
            @endif
            {{-- / Menu --}}

            {{-- Layout page --}}
            <div class="layout-page">
                {{-- Navbar --}}
                @if ($isNavbar)
                    @livewire('sections.navbar.navbar') {{-- [cite: 1] --}}
                @endif
                {{-- / Navbar --}}

                {{-- Content wrapper --}}
                <div class="content-wrapper" id="main-content">
                    {{-- Main Content Container --}}
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">  {{-- [cite: 1] --}}
                        @else
                            <div class="{{ $container }} flex-grow-1 container-p-y"> {{-- [cite: 1] --}}
                    @endif

                    {{-- Global alert message display --}}
                    @include('_partials._alerts.alert-general')

                    {{-- Render content from Livewire full-page components or traditional Blade views --}}
                    @if (isset($slot))
                        {{ $slot }} {{-- For Livewire full-page components using this layout --}}
                    @else
                        @yield('content') {{-- For traditional Blade sections --}}
                    @endif

                </div> {{-- /.container (closing main content container) --}}

                {{-- Footer --}}
                @if ($isFooter)
                    {{-- Footer Livewire component determines its own container logic --}}
                    @livewire('sections.footer.footer') {{-- [cite: 1] --}}
                @endif
                {{-- / Footer --}}

                <div class="content-backdrop fade"></div>
            </div> {{-- /.content-wrapper --}}
        </div> {{-- /.layout-page --}}
    </div> {{-- /.layout-container --}}

    {{-- Overlay for menu functionality on small screens --}}
    @if ($isMenu)
        <div class="layout-overlay layout-menu-toggle"></div> {{-- [cite: 1] --}}
    @endif

    {{-- Drag Target Area for menu slide-in on small screens --}}
    <div class="drag-target"></div> {{-- [cite: 1] --}}

    </div> {{-- /.layout-wrapper --}}

    {{-- Core JS --}}
    @include('layouts.sections.scripts') {{-- Common JS scripts, includes your theme toggle JS --}}

    {{-- Livewire Scripts --}}
    @livewireScripts

    {{-- Stack for custom JS from child views --}}
    @stack('scripts') {{-- General purpose script stack --}}
    @stack('page-script') {{-- More specific page scripts --}}

</body>

</html>
