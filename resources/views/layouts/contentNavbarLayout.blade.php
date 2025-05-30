{{-- resources/views/layouts/contentNavbarLayout.blade.php --}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
    $configData = \App\Helpers\Helpers::appClasses();

    // Layout options with fallback
    $contentNavbar = $contentNavbar ?? ($configData['contentNavbar'] ?? true);
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid');
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);
    $customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true);

    // Layout CSS classes
    $navbarDetached = $configData['navbarDetached'] ? 'navbar-detached' : '';
    $menuFixed = $configData['menuFixed'] ? 'layout-menu-fixed' : '';
    $navbarFixed = $configData['navbarFixed'] ? 'layout-navbar-fixed' : '';
    $footerFixed = $configData['footerFixed'] ? 'layout-footer-fixed' : '';
    $menuCollapsed = $configData['menuCollapsed'] ? 'layout-menu-collapsed' : '';

    $container = $container ?? ($configData['container'] ?? 'container-fluid');
@endphp

@extends('layouts.commonMaster') {{-- Includes <head>, Bootstrap, theme setup --}}

@section('layoutContent')
    <div
        class="layout-wrapper layout-content-navbar
              {{ $isMenu ? '' : 'layout-without-menu' }}
              {{ $menuFixed }} {{ $navbarFixed }} {{ $footerFixed }} {{ $menuCollapsed }}">

        <div class="layout-container">

            {{-- Sidebar --}}
            @if ($isMenu)
                @livewire('sections.menu.vertical-menu', ['configData' => $configData])
            @endif

            {{-- Main Layout Page --}}
            <div class="layout-page">

                {{-- System Banner (Jetstream/notifications) --}}
                <x-banner />

                {{-- Top Navbar --}}
                @if ($isNavbar)
                    @livewire('sections.navbar.navbar', [
                        'navbarDetached' => $configData['navbarDetached'],
                        'containerNav' => $containerNav,
                    ])
                @endif

                {{-- Content Wrapper --}}
                <div class="content-wrapper">

                    {{-- Main Content Area --}}
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                        @else
                            <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                    @yield('content')

                </div> {{-- /.container --}}

                {{-- Footer --}}
                @if ($isFooter)
                    @livewire('sections.footer.footer', ['containerClass' => $container])
                @endif

                <div class="content-backdrop fade"></div>
            </div> {{-- /.content-wrapper --}}
        </div> {{-- /.layout-page --}}
    </div> {{-- /.layout-container --}}

    {{-- Overlay for menu (mobile) --}}
    @if ($isMenu)
        <div class="layout-overlay layout-menu-toggle"></div>
    @endif

    {{-- Swipe target --}}
    <div class="drag-target"></div>
    </div> {{-- /.layout-wrapper --}}
@endsection
