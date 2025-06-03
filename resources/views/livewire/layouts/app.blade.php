{{-- resources/views/livewire/layouts/app.blade.php --}}
{{-- This is the main application layout for full-page Livewire components --}}

@extends('layouts.commonMaster') {{-- Extends the root HTML structure from commonMaster.blade.php --}}

@php
    // $configData is globally available as commonMaster.blade.php calls \App\Helpers\Helpers::appClasses().
    // This block sets local layout variables, using $configData values as defaults.
    // These can be overridden by specific Livewire components if they pass these variables to the layout.

    // Determine if shared UI elements should be displayed.
    // Design Language Documentation: Section 3.1 (Navigation) implies standard Top Action Bar and Vertical Side Navigation.
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true); // For Vertical Side Navigation
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true); // For Top Action Bar
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true); // For a consistent application footer

    // Container settings - defaults to 'container-fluid' as per Helpers.php, aligning with
    // Design Language Documentation: "Focused & Functional Digital Workspace" implies full-width for internal tools.
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid'); // For Navbar content
    $container = $container ?? ($configData['container'] ?? 'container-fluid'); // For Main content area

    // CSS class for navbar detachment based on theme configuration.
    // This should be styled by the MOTAC theme (e.g., if navbar has different bg/shadow when detached).
    $navbarDetachedClass = $configData['navbarDetached'] ?? false ? 'navbar-detached' : '';

    // Option for flex layout on specific pages.
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false);

@endphp

@section('layoutContent')
    {{-- This allows page-specific configurations to potentially modify global $configData values.
         Ensure \App\Helpers\Helpers::updatePageConfig() respects MOTAC Design Language. --}}
    @isset($pageConfigs)
        {!! App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
    @endisset

    {{-- The main .layout-wrapper should be styled by the MOTAC theme to use
         var(--motac-background) from Design Language Documentation (Section 2.1). --}}
    <div class="layout-wrapper layout-content-navbar {{ $isMenu ? '' : 'layout-without-menu' }}">
        <div class="layout-container">

            @if ($isMenu)
                {{-- Vertical Menu Livewire Component --}}
                {{-- Ensure 'sections.menu.vertical-menu' implements the HTML structure,
                     styling (using var(--motac-surface), Noto Sans, MOTAC colors),
                     and icons as per Design Language Documentation (Section 3.1, 2.2, 2.4). --}}
                @livewire('sections.menu.vertical-menu')
            @endif

            {{-- Layout Page --}}
            <div class="layout-page">

                {{-- General alerts partial.
                     Ensure '_partials._alerts.alert-general.blade.php' uses Bootstrap alerts styled
                     with MOTAC semantic colors (Critical, Success, etc.) from
                     Design Language Documentation (Section 2.1 & 3.3). --}}
                @include('_partials._alerts.alert-general')

                @if ($isNavbar)
                    {{-- Navbar Livewire Component --}}
                    {{-- Ensure 'sections.navbar.navbar' (which likely renders the navbar.blade.php we revised)
                         uses MOTAC branding, colors, Noto Sans, and icons as per
                         Design Language Documentation (Section 3.1, 7.1). --}}
                    @livewire('sections.navbar.navbar', [
                        'containerNav' => $containerNav,
                        'navbarDetachedClass' => $navbarDetachedClass,
                    ])
                @endif

                {{-- Content Wrapper --}}
                {{-- This .content-wrapper should be styled by the MOTAC theme, potentially using var(--motac-background). --}}
                <div class="content-wrapper">
                    {{-- Main Content Area ($slot) --}}
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                            {{ $slot }} {{-- Main Livewire page content is injected here --}}
                        </div>
                    @else
                        {{-- Standard content area with padding.
                             Review 'container-p-y' class: Ensure its padding values align with the
                             4px baseline grid and spacing system defined in
                             Design Language Documentation (Section 2.3).
                             Replace with MOTAC custom spacing classes if necessary. --}}
                        <div class="{{ $container }} flex-grow-1 container-p-y">
                            {{ $slot }} {{-- Main Livewire page content is injected here --}}
                        </div>
                    @endif
                    {{-- /Main Content Area --}}

                    @if ($isFooter)
                        {{-- Footer Livewire Component --}}
                        {{-- Ensure 'sections.footer.footer' is styled according to MOTAC branding
                             (e.g., simple, formal, with var(--motac-surface) or var(--motac-background)
                             and text color var(--motac-text)). --}}
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
