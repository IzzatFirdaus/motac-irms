{{-- layouts/app.blade.php --}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
    // Retrieve system-wide configuration (e.g., Bootstrap theme, layout toggles)
    $configData = \App\Helpers\Helpers::appClasses();

    // Layout state variables with fallback to configData or defaults
    $contentNavbar = $contentNavbar ?? ($configData['contentNavbar'] ?? true);
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid');
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);
    $customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true);

    // CSS class controls for layout
    $navbarDetached = $configData['navbarDetached'] ? 'navbar-detached' : '';
    $menuFixed = $configData['menuFixed'] ? 'layout-menu-fixed' : '';
    $navbarFixed = $configData['navbarFixed'] ? 'layout-navbar-fixed' : '';
    $footerFixed = $configData['footerFixed'] ? 'layout-footer-fixed' : '';
    $menuCollapsed = $configData['menuCollapsed'] ? 'layout-menu-collapsed' : '';

    $container = $container ?? ($configData['container'] ?? 'container-fluid');
@endphp

@extends('layouts.commonMaster') {{-- Master layout: includes <head> and Bootstrap theme support --}}

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

                {{-- Jetstream Banner Support (optional) --}}
                @if (config('jetstream.hasProfileFeatures') || config('jetstream.hasApiFeatures'))
                    <x-banner />
                @endif

                {{-- Top Navigation Bar --}}
                @if ($isNavbar)
                    @livewire('sections.navbar.navbar', [
                        'navbarDetached' => $configData['navbarDetached'],
                        'containerNav' => $containerNav,
                    ])
                @endif

                {{-- Main Content Wrapper --}}
                <div class="content-wrapper">

                    {{-- Content --}}
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                        @else
                            <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                    {{-- Blade slot or traditional section --}}
                    @if (isset($slot))
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endif

                </div> {{-- /.container --}}
                {{-- /Content --}}

                {{-- Footer --}}
                @if ($isFooter)
                    @livewire('sections.footer.footer', ['containerClass' => $container])
                @endif

                <div class="content-backdrop fade"></div>
            </div> {{-- /.content-wrapper --}}
        </div> {{-- /.layout-page --}}
    </div> {{-- /.layout-container --}}

    {{-- Mobile Menu Overlay --}}
    @if ($isMenu)
        <div class="layout-overlay layout-menu-toggle"></div>
    @endif

    <div class="drag-target"></div>
    </div> {{-- /.layout-wrapper --}}
@endsection
