{{-- resources/views/layouts/layout-content-navbar.blade.php --}}
{{--
  Main application layout for pages that include a content area with a top navbar and (optionally) a sidebar menu.
  Filename updated from contentNavbarLayout.blade.php to layout-content-navbar.blade.php as per new naming convention.
--}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
    // Retrieve config for theme and layout
    $configData = \App\Helpers\Helpers::appClasses();

    // Layout Control Variables (for toggling menu, navbar, footer, etc.)
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);
    $contentNavbar = $contentNavbar ?? ($configData['contentNavbar'] ?? true);

    // Content container settings
    $container = $container ?? ($configData['container'] ?? 'container-fluid');
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid');

    // Special layout flags
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);
    $customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true);

    // CSS class strings for layout states
    $navbarDetached = $configData['navbarDetached'] ?? false ? 'navbar-detached' : '';
    $menuFixed = $configData['menuFixed'] ?? true ? 'layout-menu-fixed' : '';
    $navbarFixed = $configData['navbarFixed'] ?? true ? 'layout-navbar-fixed' : '';
    $footerFixed = $configData['footerFixed'] ?? false ? 'layout-footer-fixed' : '';
    $menuCollapsed = $configData['menuCollapsed'] ?? false ? 'layout-menu-collapsed' : '';
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
    <div
        class="layout-wrapper layout-content-navbar
                {{ $isMenu ? '' : 'layout-without-menu' }}
                {{ $menuFixed }} {{ $navbarFixed }} {{ $footerFixed }} {{ $menuCollapsed }}">

        <div class="layout-container">

            {{-- Vertical Sidebar Menu --}}
            @if ($isMenu)
                @livewire('sections.menu.vertical-menu', ['configData' => $configData])
            @endif

            <div class="layout-page">
                <x-banner /> {{-- System banner/notification component --}}

                {{-- Top Navbar --}}
                @if ($isNavbar)
                    @livewire('sections.navbar.navbar', [
                        'navbarDetached' => $configData['navbarDetached'],
                        'containerNav' => $containerNav,
                    ])
                @endif

                {{-- Main Content Wrapper --}}
                <div class="content-wrapper" id="main-content">
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                    @else
                        <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                    @yield('content')

                    </div>
                </div>

                {{-- Footer --}}
                @if ($isFooter)
                    @livewire('sections.footer.footer', ['containerClass' => $container])
                @endif

                <div class="content-backdrop fade"></div>
            </div>
        </div>

        @if ($isMenu)
            <div class="layout-overlay layout-menu-toggle"></div>
        @endif

        <div class="drag-target"></div>
    </div>
@endsection
