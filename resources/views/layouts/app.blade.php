{{-- resources/views/layouts/app.blade.php --}}
{{--
  Main application layout for authenticated users.
  This layout includes the primary navigation (sidebar/topbar), content area, and footer.
  It extends 'layouts.commonMaster' which should provide global setups like Noto Sans font,
  MOTAC color variables, and base accessibility features.
--}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!} {{-- Theme-specific page configuration helper --}}
@endisset

@php
    // Retrieve system-wide configuration (e.g., theme style, layout toggles) from a helper.
    $configData = \App\Helpers\Helpers::appClasses();

    // Visibility of core layout elements
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true);
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true);
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true);

    // Content area styling
    $contentNavbar = $contentNavbar ?? ($configData['contentNavbar'] ?? true);
    $container = $container ?? ($configData['container'] ?? 'container-fluid');
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);

    // Theme Customizer
    $customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true);

    // CSS Class Strings for Layout States
    $navbarDetached = $configData['navbarDetached'] ?? false ? 'navbar-detached' : '';
    $menuFixed = $configData['menuFixed'] ?? true ? 'layout-menu-fixed' : '';
    $navbarFixed = $configData['navbarFixed'] ?? true ? 'layout-navbar-fixed' : '';
    $footerFixed = $configData['footerFixed'] ?? false ? 'layout-footer-fixed' : '';
    $menuCollapsed = $configData['menuCollapsed'] ?? false ? 'layout-menu-collapsed' : '';
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid');
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
    <div
        class="layout-wrapper layout-content-navbar
              {{ $isMenu ? '' : 'layout-without-menu' }}
              {{ $menuFixed }} {{ $navbarFixed }} {{ $footerFixed }} {{ $menuCollapsed }}">

        <div class="layout-container">

            @if ($isMenu)
                @livewire('sections.menu.vertical-menu', ['configData' => $configData])
            @endif

            <div class="layout-page">

                @if (config('jetstream.hasProfileFeatures') || config('jetstream.hasApiFeatures'))
                    <x-banner />
                @endif

                @if ($isNavbar)
                    @livewire('sections.navbar.navbar', [
                        'navbarDetached' => $navbarDetached,
                        'containerNav' => $containerNav,
                    ])
                @endif

                <div class="content-wrapper" id="main-content">
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                        @else
                            <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                    {{-- ADDED: Global alert message display --}}
                    {{-- This partial should handle session flash messages and validation errors --}}
                    @include('_partials._alerts.alert-general')

                    {{-- Render content from Livewire full-page components or traditional Blade views --}}
                    @if (isset($slot))
                        {{ $slot }} {{-- For Livewire full-page components --}}
                    @else
                        @yield('content') {{-- For traditional Blade sections --}}
                    @endif

                </div> {{-- /.container (main content container) --}}

                @if ($isFooter)
                    @livewire('sections.footer.footer', ['containerClass' => $container])
                @endif

                <div class="content-backdrop fade"></div>
            </div> {{-- /.content-wrapper --}}
        </div> {{-- /.layout-page --}}
    </div> {{-- /.layout-container --}}

    @if ($isMenu)
        <div class="layout-overlay layout-menu-toggle"></div>
    @endif

    <div class="drag-target"></div>

    </div> {{-- /.layout-wrapper --}}
@endsection
