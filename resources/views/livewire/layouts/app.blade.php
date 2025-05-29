{{-- resources/views/livewire/layouts/app.blade.php --}}
{{-- This is the main application layout for full-page Livewire components --}}

@extends('layouts.commonMaster') {{-- Extends the root HTML structure from commonMaster.blade.php --}}

@php
    // $configData is globally available as commonMaster.blade.php calls \App\Helpers\Helpers::appClasses().
    // This block sets local layout variables, using $configData values as defaults.
    // These can be overridden by specific Livewire components if they pass these variables to the layout.

    // Determine if shared UI elements should be displayed.
    // Design Language: Standard Application Layout (Top Navbar, Left Sidebar, Main Content)
    $isMenu = $isMenu ?? $configData['isMenu'] ?? true;
    $isNavbar = $isNavbar ?? $configData['isNavbar'] ?? true;
    $isFooter = $isFooter ?? $configData['isFooter'] ?? true;
    // $contentNavbar = $contentNavbar ?? $configData['contentNavbar'] ?? true; // Usually true for vertical layout

    // Container settings - defaults to 'container-fluid' as per Helpers.php for MOTAC internal system
    // Design Language: Focused & Functional Digital Workspace (container-fluid)
    $containerNav = $containerNav ?? $configData['containerNav'] ?? 'container-fluid';
    $container = $container ?? $configData['container'] ?? 'container-fluid';

    // CSS class for navbar detachment based on theme configuration
    $navbarDetachedClass = ($configData['navbarDetached'] ?? false) ? 'navbar-detached' : '';

    // Option for flex layout on specific pages (less common for general app layout)
    $isFlex = $isFlex ?? $configData['isFlex'] ?? false;

@endphp

@section('layoutContent')
    {{-- This allows page-specific configurations passed from a controller or Livewire component
         to potentially modify global $configData values if App\Helpers\Helpers::updatePageConfig is used.
         More typical for traditional Blade views. --}}
    @isset($pageConfigs)
        {!! App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
    @endisset

    <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
        <div class="layout-container">

            @if ($isMenu)
                {{-- Vertical Menu Livewire Component --}}
                {{-- System Design: Sections 3, 6.2 --}}
                @livewire('sections.menu.vertical-menu')
            @endif

            {{-- Layout Page --}}
            <div class="layout-page">

                {{-- General alerts partial, placed for high visibility --}}
                {{-- System Design Reference: Section 6.3 (alert-general.blade.php) --}}
                {{-- It's often better to place alerts within the $slot or handled by Livewire components directly
                     to be closer to the action that triggered them. However, a global spot is also common. --}}
                @include('_partials._alerts.alert-general')

                @if ($isNavbar)
                    {{-- Navbar Livewire Component --}}
                    {{-- System Design: Sections 3, 6.2 --}}
                    @livewire('sections.navbar.navbar', [
                        'containerNav' => $containerNav,
                        'navbarDetachedClass' => $navbarDetachedClass
                    ])
                @endif

                {{-- Content Wrapper --}}
                <div class="content-wrapper">
                    {{-- Main Content Area --}}
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                            {{ $slot }} {{-- Main Livewire page content is injected here --}}
                        </div>
                    @else
                        {{-- Standard content area with padding --}}
                        {{-- Design Language: Ample space for forms, tables, dashboards --}}
                        <div class="{{ $container }} flex-grow-1 container-p-y">
                            {{ $slot }} {{-- Main Livewire page content is injected here --}}
                        </div>
                    @endif
                    {{-- /Main Content Area --}}

                    @if ($isFooter)
                        {{-- Footer Livewire Component --}}
                        {{-- System Design: Sections 3, 6.2 --}}
                        @livewire('sections.footer.footer')
                    @endif

                    <div class="content-backdrop fade"></div>
                </div>
                {{-- /Content Wrapper --}}
            </div>
            {{-- /Layout Page --}}
        </div>

        {{-- Overlay for menu functionality on small screens --}}
        @if ($isMenu)
            <div class="layout-overlay layout-menu-toggle"></div>
        @endif

        {{-- Drag Target Area for menu slide-in on small screens --}}
        <div class="drag-target"></div>
    </div>
@endsection
