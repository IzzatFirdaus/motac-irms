{{-- resources/views/layouts/app.blade.php --}}
@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
    $configData = \App\Helpers\Helpers::appClasses();

    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);

    $container = $container ?? ($configData['container'] ?? 'container-fluid');
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);

    $navbarDetached = !empty($configData['navbarDetached']) ? 'navbar-detached' : '';
    $menuFixed = !empty($configData['menuFixed']) ? 'layout-menu-fixed' : '';
    $menuCollapsed = !empty($configData['menuCollapsed']) ? 'layout-menu-collapsed' : '';
    $navbarFixed = !empty($configData['navbarFixed']) ? 'layout-navbar-fixed' : '';
    $footerFixed = !empty($configData['footerFixed']) ? 'layout-footer-fixed' : '';
    $menuHover = !empty($configData['showDropdownOnHover']) ? 'layout-menu-hover' : '';

    $templateName = \Illuminate\Support\Str::slug(
        config('variables.templateName', config('app.name', 'Sistem MOTAC')),
        '-',
    );
    $currentThemeStyle = $configData['myStyle'] ?? 'light';

    // $menuData is expected to be globally shared by MenuServiceProvider's View::share('menuData', $data).
// It will be automatically available in this scope if shared correctly.
// If MenuServiceProvider fails or View::share isn't effective here for some reason,
    // $menuData passed to the component might be null or from a previous context if set by a controller.
    // The Livewire component's mount method now has a fallback to try View::shared('menuData') directly.
    $currentUserRole = Auth::check() ? Auth::user()?->getRoleNames()->first() : null;

@endphp

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
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Noto+Sans+JP:wght@300;400;500;600;700&family=Noto+Sans+KR:wght@300;400;500;600;700&family=Noto+Sans+Display:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Placeholder for Tabler Icons if strictly needed by core theme JS for menu toggles (prefer standardization to bi-*) --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" /> --}}


    {{-- Core CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/core.css') }}"
        class="{{ $configData['hasCustomizer'] ?? false ? 'template-customizer-core-css' : '' }}" />

    {{-- Theme CSS --}}
    <link rel="stylesheet"
        href="{{ asset('assets/vendor/css' . ($configData['rtlSupport'] ?? '') . '/' . ($configData['myTheme'] ?? 'theme-default') . '.css') }}"
        {{-- This will load theme-motac.css --}}
        class="{{ $configData['hasCustomizer'] ?? false ? 'template-customizer-theme-css' : '' }}" />

    {{-- Custom App CSS (if demo.css is not used for application styles) --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/css/app-motac.css') }}" /> --}}


    {{-- Vendor Libs CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />

    @yield('vendor-css')
    @yield('page-css')

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    @include('layouts.sections.scriptsIncludes')

    @livewireStyles
    @stack('styles')
</head>

<body>
    {{-- Debug: Check if $menuData is set here from View::share before passing --}}
    {{-- <script>console.log('app.blade.php - $menuData before Livewire component:', @json($menuData ?? null));</script> --}}
    {{-- <script>console.log('app.blade.php - $currentUserRole before Livewire component:', @json($currentUserRole ?? null));</script> --}}
    {{-- <script>console.log('app.blade.php - $configData before Livewire component keys:', @json(array_keys($configData ?? [])));</script> --}}

    <div class="layout-wrapper layout-content-navbar {{ $isNavbar ? '' : 'layout-without-navbar' }}">
        <div class="layout-container">
            @if ($isMenu)
                {{-- Pass the $menuData that should be available from View::share() --}}
                {{-- The Livewire component will also try View::shared('menuData') if this is null --}}
                @livewire('sections.menu.vertical-menu', [
                    'menuData' => $menuData ?? null, // Pass $menuData (could be null if View::share didn't populate it here)
                    'role' => $currentUserRole,
                    'configData' => $configData,
                ])
            @endif

            <div class="layout-page">
                @if ($isNavbar)
                    @livewire('sections.navbar.navbar', [
                        'containerNav' => $configData['containerNav'] ?? 'container-fluid',
                        'navbarDetachedClass' => $navbarDetached,
                        'navbarFull' => $configData['navbarFull'] ?? true,
                        'navbarHideToggle' => ($configData['myLayout'] ?? 'vertical') === 'horizontal',
                        'activeTheme' => $currentThemeStyle,
                    ])
                @endif

                <div class="content-wrapper" id="main-content">
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                        @else
                            <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                    @include('_partials._alerts.alert-general') {{-- Ensure this partial exists --}}

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
    @stack('scripts')
    @stack('page-script')
</body>

</html>
