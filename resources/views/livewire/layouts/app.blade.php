@extends('layouts.commonMaster') {{-- Ensures it uses the root HTML structure and $configData --}}

@php
    // Config values are primarily inherited from $configData (set in commonMaster.blade.php).
    // These allow specific pages that extend this layout (though less common for Livewire full-page components)
    // or this layout itself to provide overrides if $pageConfigs is not used for such specific overrides.

    // Determine if shared UI elements should be displayed. Defaults come from $configData,
    // which is sourced from Helpers::appClasses() via commonMaster.blade.php
    //.
    $isMenu = $isMenu ?? $configData['isMenu'] ?? true;
    $isNavbar = $isNavbar ?? $configData['isNavbar'] ?? true;
    $isFooter = $isFooter ?? $configData['isFooter'] ?? true;
    $contentNavbar = $contentNavbar ?? $configData['contentNavbar'] ?? true; // Usually true if navbar is part of content area

    // Container settings
    $containerNav = $containerNav ?? $configData['containerNav'] ?? 'container-xxl'; // For navbar's container
    $container = $container ?? $configData['container'] ?? 'container-xxl'; // For main content area

    // Layout class for navbar detachment (if theme supports)
    $navbarDetachedClass = ($configData['navbarDetached'] ?? false) ? 'navbar-detached' : '';

    // Flex layout option
    $isFlex = $isFlex ?? $configData['isFlex'] ?? false; // For pages needing specific flex behaviors

@endphp

@section('layoutContent')
    {{-- This allows page-specific configurations (passed from a controller/Livewire component)
         to potentially modify global $configData values if Helper::updatePageConfig is designed to do so.
         This is more common with traditional Blade views than full-page Livewire components. --}}
    @isset($pageConfigs)
        {!! App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
    @endisset

    <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
        <div class="layout-container">

            @if ($isMenu)
                {{-- Vertical Menu Livewire Component --}}
                @livewire('sections.menu.vertical-menu')
            @endif

            <div class="layout-page">

                {{-- Include general alerts partial - System Design 6.3 --}}
                @include('_partials._alerts.alert-general')

                @if ($isNavbar)
                    {{-- Navbar Livewire Component --}}
                    {{-- The $navbarDetachedClass should be applied within the navbar component or its container logic if needed --}}
                    @livewire('sections.navbar.navbar', ['containerNav' => $containerNav, 'navbarDetachedClass' => $navbarDetachedClass])
                @endif

                <div class="content-wrapper">
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                            {{ $slot }} {{-- Main Livewire page content --}}
                        </div>
                    @else
                        <div class="{{ $container }} flex-grow-1 container-p-y">
                            {{ $slot }} {{-- Main Livewire page content --}}
                        </div>
                    @endif
                    @if ($isFooter)
                        {{-- Footer Livewire Component --}}
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
@endsection
