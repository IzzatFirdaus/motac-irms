{{-- resources/views/layouts/contentNavbarLayout.blade.php --}}
{{--
  This is the main application layout for pages that include a content area with a top navbar.
  It typically includes the vertical menu (sidebar), top navbar, main content section, and footer.
  It extends 'layouts.commonMaster', which should provide global setups like Noto Sans font,
  MOTAC color variables, and base accessibility features.
--}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!} {{-- Theme-specific page configuration helper --}}
@endisset

@php
    // Retrieve system-wide configuration from App\Helpers\Helpers::appClasses().
    // This $configData likely controls theme aspects (style, layout toggles, container types, etc.)
    // and should be configured to reflect MOTAC's preferred defaults.
$configData = \App\Helpers\Helpers::appClasses();

// --- Layout Control Variables ---
// These variables allow per-page customization of the layout structure and appearance.
// They default to values from $configData or sensible MOTAC-aligned defaults.

// Visibility of core layout elements:
$isMenu = $isMenu ?? ($configData['isMenu'] ?? true); // Display the vertical sidebar menu? (Ref: Design Doc 3.1)
$isNavbar = $isNavbar ?? ($configData['isNavbar'] ?? true); // Display the top navigation bar? (Ref: Design Doc 3.1)
$isFooter = $isFooter ?? ($configData['isFooter'] ?? true); // Display the footer?
$contentNavbar = $contentNavbar ?? ($configData['contentNavbar'] ?? true); // Standard layout with navbar above content.

// Content container settings:
$container = $container ?? ($configData['container'] ?? 'container-fluid'); // Main content container type (e.g., 'container-fluid', 'container-xxl')
$containerNav = $containerNav ?? ($configData['containerNav'] ?? 'container-fluid'); // Container type for the navbar content (if navbar is detached)

// Special layout flags:
$isFlex = $isFlex ?? ($configData['isFlex'] ?? false); // Use flexbox for the main content container? (For specific page structures)
$customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true); // Hide theme customizer UI?

// --- CSS Class Strings for Layout States ---
// These are derived from $configData and dynamically added to control layout appearance
// (e.g., fixed/static menu/navbar, collapsed menu).
// The underlying CSS for these classes must align with MOTAC's visual standards.
    $navbarDetached = $configData['navbarDetached'] ?? false ? 'navbar-detached' : ''; // Class for a detached navbar style
    $menuFixed = $configData['menuFixed'] ?? true ? 'layout-menu-fixed' : ''; // Fixed sidebar
    $navbarFixed = $configData['navbarFixed'] ?? true ? 'layout-navbar-fixed' : ''; // Fixed top navbar
    $footerFixed = $configData['footerFixed'] ?? false ? 'layout-footer-fixed' : ''; // Fixed footer
    $menuCollapsed = $configData['menuCollapsed'] ?? false ? 'layout-menu-collapsed' : ''; // Collapsed sidebar state
@endphp

@extends('layouts.commonMaster') {{-- Inherits Noto Sans font, MOTAC base styles, skip link, etc. --}}

@section('layoutContent')
    {{--
      Main layout wrapper. Classes control overall layout behavior (e.g., presence of menu, fixed elements).
      Styling for these classes (backgrounds, spacing) should adhere to MOTAC Design Language
      (e.g., Section 2.1 Background/Surface colors, Section 2.3 Spacing principles).
    --}}
    <div
        class="layout-wrapper layout-content-navbar
                {{ $isMenu ? '' : 'layout-without-menu' }}
                {{ $menuFixed }} {{ $navbarFixed }} {{ $footerFixed }} {{ $menuCollapsed }}">

        <div class="layout-container">

            {{-- Vertical Sidebar Menu (Design Language Section 3.1) --}}
            {{-- This Livewire component ('sections.menu.vertical-menu') is critical for navigation.
                 It must:
                 - Adhere to MOTAC branding: Display MOTAC logo/icon prominently at its top.
                 - Typography: Use Noto Sans, with sizes and weights as per Design Doc 2.2.
                 - Color Scheme: Use MOTAC colors for background, text, active/hover states (Design Doc 2.1).
                 - Iconography: Pair Bootstrap Icons with Bahasa Melayu labels (Design Doc 2.4).
                 - Accessibility: Be fully keyboard navigable with clear focus states (Design Doc 6.1).
            --}}
            @if ($isMenu)
                @livewire('sections.menu.vertical-menu', ['configData' => $configData])
            @endif

            {{-- Main Page Area (includes Navbar, Content, Footer) --}}
            <div class="layout-page">

                {{-- System Banner (e.g., Jetstream notifications, custom alerts) --}}
                {{-- Ensure this component, if used, is styled according to MOTAC's alert/notification design. --}}
                <x-banner />

                {{-- Top Navigation Bar (Design Language Section 3.1 - Top Action Bar) --}}
                {{-- This Livewire component ('sections.navbar.navbar') must implement:
                     - MOTAC Branding: Prominent MOTAC logo lockup (e.g., 40px height).
                     - Language Toggle: "BM/EN" with flag icons.
                     - User Profile: Display user name, role (as a badge), and dropdown for profile/logout.
                     - Styling: Use Noto Sans, MOTAC primary color for background/accents (Design Doc 2.1, 2.2).
                     - Responsiveness: Adapt gracefully to smaller screen sizes.
                --}}
                @if ($isNavbar)
                    @livewire('sections.navbar.navbar', [
                        'navbarDetached' => $configData['navbarDetached'], // Boolean to control detached style in component
                        'containerNav' => $containerNav,
                    ])
                @endif

                {{-- Content Wrapper - This is the primary target for the "Skip to main content" link. --}}
                <div class="content-wrapper" id="main-content">

                    {{-- Main Content Area --}}
                    {{-- The '$container' class (e.g., 'container-fluid') and theme padding classes (e.g., 'container-p-y')
                         should provide spacing that aligns with MOTAC's Spacing System (Design Doc 2.3)
                         or allow the yielded content to manage its own internal spacing.
                    --}}
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                        @else
                            <div class="{{ $container }} flex-grow-1 container-p-y"> {{-- 'container-p-y' likely adds vertical padding --}}
                    @endif

                    @yield('content') {{-- Page-specific content from Blade views is injected here --}}
                    {{-- If this layout is also used by Livewire full-page components, you might also need:
                             @if (isset($slot)) {{ $slot }} @else @yield('content') @endif
                        --}}

                </div> {{-- /.container (main content container) --}}
                {{-- /Main Content Area --}}

                {{-- Footer --}}
                {{-- This Livewire component ('sections.footer.footer') should:
                         - Maintain a professional and unobtrusive design.
                         - Typography: Use Noto Sans with appropriate text colors (Design Doc 2.1, 2.2).
                         - Content: Typically MOTAC copyright, system version, or relevant links.
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

    {{-- Swipe Target - Theme element for touch interactions (e.g., opening menu) --}}
    <div class="drag-target"></div>

    </div> {{-- /.layout-wrapper --}}
@endsection
