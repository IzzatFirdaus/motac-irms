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
    // This data should be configured to reflect MOTAC's preferred defaults.
    $configData = \App\Helpers\Helpers::appClasses();

    // --- Layout Control Variables ---
    // These variables allow per-page customization of the layout.
    // They should default to values in $configData or sensible MOTAC-aligned defaults.

    // Visibility of core layout elements
    $isMenu = $isMenu ?? ($configData['isMenu'] ?? true); // Show vertical menu (sidebar)? (Design Doc 3.1)
    $isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true); // Show top navigation bar? (Design Doc 3.1)
    $isFooter = $isFooter ?? ($configData['isFooter'] ?? true); // Show footer?

    // Content area styling
    $contentNavbar = $contentNavbar ?? ($configData['contentNavbar'] ?? true); // Is there a navbar above the content? (Usually true)
    $container = $container ?? ($configData['container'] ?? 'container-fluid'); // Main content container type (e.g., 'container-fluid', 'container-xxl')
    $isFlex = $isFlex ?? ($configData['isFlex'] ?? false); // Use flexbox for content container? (Special use cases)

    // Theme Customizer (usually hidden in production or for certain layouts)
    $customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true);

    // --- CSS Class Strings for Layout States ---
    // These are derived from $configData and control layout appearance (fixed/static menu/navbar, collapsed menu).
    // Ensure these theme classes are styled to meet MOTAC's visual standards.
    $navbarDetached = $configData['navbarDetached'] ?? false ? 'navbar-detached' : '';
    $menuFixed = $configData['menuFixed'] ?? true ? 'layout-menu-fixed' : ''; // Default to fixed menu for modern feel
    $navbarFixed = $configData['navbarFixed'] ?? true ? 'layout-navbar-fixed' : ''; // Default to fixed navbar
    $footerFixed = $configData['footerFixed'] ?? false ? 'layout-footer-fixed' : ''; // Footer usually not fixed
    $menuCollapsed = $configData['menuCollapsed'] ?? false ? 'layout-menu-collapsed' : '';

    // Container class for the navbar content (if detached)
    $containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid');

@endphp

@extends('layouts.commonMaster') {{-- Inherits Noto Sans, MOTAC colors, skip link from commonMaster --}}

@section('layoutContent')
    {{--
      The main layout wrapper. Classes here control overall layout behavior.
      The styling of these classes (e.g., background colors, spacing) should align with
      MOTAC Design Language (Sections 2.1 Background/Surface, 2.3 Spacing).
    --}}
    <div class="layout-wrapper layout-content-navbar
              {{ $isMenu ? '' : 'layout-without-menu' }}
              {{ $menuFixed }} {{ $navbarFixed }} {{ $footerFixed }} {{ $menuCollapsed }}">

        <div class="layout-container">

            {{-- Vertical Sidebar Menu (Design Language 3.1) --}}
            {{-- This Livewire component should:
                 - Display MOTAC logo/icon at the top (as per Design Doc 3.1 example).
                 - Use Noto Sans for text with appropriate sizes/weights (Design Doc 2.2).
                 - Adhere to MOTAC color scheme for active/hover states (Design Doc 2.1).
                 - Ensure icons are paired with Bahasa Melayu labels (Design Doc 2.4).
                 - Be keyboard navigable with clear focus indicators (Design Doc 6.1).
            --}}
            @if ($isMenu)
                @livewire('sections.menu.vertical-menu', ['configData' => $configData])
            @endif

            {{-- Main Page Area --}}
            <div class="layout-page">

                {{-- Jetstream Banner Support (Optional, keep if Jetstream is used) --}}
                @if (config('jetstream.hasProfileFeatures') || config('jetstream.hasApiFeatures'))
                    <x-banner />
                @endif

                {{-- Top Navigation Bar (Design Language 3.1 - Top Action Bar) --}}
                {{-- This Livewire component should implement:
                     - MOTAC logo lockup (40px height).
                     - Language toggle: BM/EN with flag icons.
                     - User profile with role badge.
                     - Use Noto Sans and MOTAC primary color for its background (Design Doc 2.1).
                     - Behave responsively.
                --}}
                @if ($isNavbar)
                    @livewire('sections.navbar.navbar', [
                        'navbarDetached' => $navbarDetached, // Pass the derived class string
                        'containerNav' => $containerNav,
                    ])
                @endif

                {{-- Main Content Wrapper --}}
                {{-- This is the target for the "Skip to main content" link --}}
                <div class="content-wrapper" id="main-content">

                    {{-- Content Area --}}
                    {{-- The $container class (e.g., container-fluid) and padding (e.g., container-p-y from theme)
                         should provide adequate spacing aligning with MOTAC's Spacing System (Design Doc 2.3)
                         or allow content within to manage its own spacing.
                    --}}
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                    @else
                        <div class="{{ $container }} flex-grow-1 container-p-y"> {{-- container-p-y likely adds vertical padding --}}
                    @endif

                        {{-- Render content from Livewire full-page components or traditional Blade views --}}
                        @if (isset($slot))
                            {{ $slot }} {{-- For Livewire full-page components --}}
                        @else
                            @yield('content') {{-- For traditional Blade sections --}}
                        @endif

                    </div> {{-- /.container (main content container) --}}
                    {{-- /Content Area --}}

                    {{-- Footer --}}
                    {{-- This Livewire component should:
                         - Have a professional and unobtrusive design.
                         - Use Noto Sans and appropriate text colors (Design Doc 2.1, 2.2).
                         - Display MOTAC copyright and system version if required.
                    --}}
                    @if ($isFooter)
                        @livewire('sections.footer.footer', ['containerClass' => $container])
                    @endif

                    <div class="content-backdrop fade"></div> {{-- Theme element for overlay effects --}}
                </div> {{-- /.content-wrapper --}}
            </div> {{-- /.layout-page --}}
        </div> {{-- /.layout-container --}}

        {{-- Mobile Menu Overlay - Theme element for responsive menu behavior --}}
        @if ($isMenu)
            <div class="layout-overlay layout-menu-toggle"></div>
        @endif

        {{-- Target for menu drag (used by some themes for touch interaction) --}}
        <div class="drag-target"></div>

    </div> {{-- /.layout-wrapper --}}
@endsection
