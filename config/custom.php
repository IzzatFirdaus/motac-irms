<?php

// Custom Theme & Layout Configuration for MOTAC Integrated Resource Management System
// -------------------------------------------------------------------------------------
// ! IMPORTANT: Clear browser local storage after changes if the theme customizer was used during development.
// This file's structure is read by App\Helpers\Helpers::appClasses() using Config::get('custom.custom').
// It allows for environment-variable-driven theme configuration with sensible MOTAC defaults.
// Design Language Refs: Overall branding (1.1, 2.1, 7.1), Layout (implied by 6.2 Dashboards)
// System Design Ref: 3.3 (AppServiceProvider/Helpers for global data), 6.1 (Branding and Layout)

return [
    // The 'custom' key is used by Config::get('custom.custom') in Helpers.php
    'custom' => [
        // --- Core Theme Settings ---
        'myLayout' => env('THEME_LAYOUT', 'vertical'), // Default: 'vertical'. Options: 'vertical', 'horizontal'. (Aligns with Design Doc 3.1 Vertical Side Navigation)
        'myTheme' => env('THEME_SKIN', 'theme-motac'),  // Default: 'theme-motac'. Options could be 'theme-default', 'theme-bordered', 'theme-semi-dark'. Assumes 'theme-motac' will contain MOTAC specific styling.
        'myStyle' => env('THEME_STYLE', 'light'),     // Default: 'light'. Options: 'light', 'dark'. (Design Doc 5.0 Dark Mode)

        // --- RTL Support ---
        // 'myRTLSupport': true if theme has distinct LTR/RTL CSS (e.g., core.css vs. core-rtl.css).
        // 'myRTLMode': true to default HTML dir to 'rtl'. MOTAC is primarily LTR (Bahasa Melayu).
        'myRTLSupport' => env('THEME_RTL_ASSETS_SUPPORT', true), // Assumes theme supports separate RTL assets.
        'myRTLMode' => env('THEME_DEFAULT_RTL_MODE', false),    // Default to LTR for MOTAC.

        // --- Theme Customizer UI Panel (for development/admin, typically disabled for end-users) ---
        'hasCustomizer' => env('THEME_HAS_CUSTOMIZER_JS', false),   // Load template-customizer.js? Default to false for production.
        'displayCustomizer' => env('THEME_DISPLAY_CUSTOMIZER_UI', false), // Show the customizer UI panel? Default to false.

        // --- Layout Behavior Flags (Fixed vs. Static elements) ---
        // Design Language: Modern Government Aesthetic often implies clear, stable navigation.
        'menuFixed' => env('THEME_MENU_FIXED', true),             // Default: true (Fixed sidebar)
        'menuCollapsed' => env('THEME_MENU_COLLAPSED', false),    // Default: false (Menu expanded by default)
        'navbarFixed' => env('THEME_NAVBAR_FIXED', true),           // Default: true (Fixed top navbar)
        'navbarDetached' => env('THEME_NAVBAR_DETACHED', false),   // Default: false (Full-width, integrated navbar typical for formal systems)
        'footerFixed' => env('THEME_FOOTER_FIXED', false),          // Default: false (Static footer)

        // --- Horizontal Menu Specific (only if 'myLayout' is 'horizontal') ---
        'showDropdownOnHover' => env('THEME_HORIZONTAL_MENU_HOVER', true),

        // --- Content Container Settings ---
        // 'container-fluid' for full-width, 'container-xxl' (or similar) for boxed.
        // Design Language: "Clean, uncluttered interfaces" could support either, but fluid is often more modern.
        'container' => env('THEME_CONTENT_CONTAINER', 'container-fluid'), // Default: 'container-fluid' for main content area
        'containerNav' => env('THEME_NAVBAR_CONTAINER', 'container-fluid'), // Default: 'container-fluid' for navbar content

        // --- Flags for Conditional Rendering in Layouts (passed via Helpers::appClasses) ---
        'contentNavbar' => env('THEME_CONTENT_NAVBAR', true), // Does the layout typically include a navbar above content?
        'isMenu' => env('THEME_IS_MENU', true),             // Is a sidebar menu generally present?
        'isNavbar' => env('THEME_IS_NAVBAR', true),          // Is a top navbar generally present?
        'isFooter' => env('THEME_IS_FOOTER', true),          // Is a footer generally present?
        'isFlex' => env('THEME_IS_FLEX_LAYOUT', false),      // For special page layouts requiring top-level flex container.

        // --- MOTAC Specific Branding Colors ---
        // Design Language 2.1: Primary Color (MOTAC Blue)
        'primaryColor' => env('THEME_PRIMARY_COLOR_MOTAC', '#0055A4'), // MOTAC Blue

        // --- Controls to show/hide in the Template Customizer UI Panel ---
        // (Relevant if 'displayCustomizer' is true, primarily for development)
        'customizerControls' => [
            'style',        // Toggle Light/Dark mode
            // 'rtl',       // Toggle RTL/LTR (usually controlled by language selection in MOTAC)
            // 'themes',    // Select different color themes/skins (MOTAC likely has one primary theme)
            'layoutType',   // Menu type (static, fixed) - if applicable beyond 'menuFixed'
            'menuFixed',
            'menuCollapsed',
            'layoutNavbarFixed', // Corresponds to navbarFixed
            // 'layoutNavbarType', // If navbar has types like 'sticky', 'static', 'hidden'
            'layoutFooterFixed', // Corresponds to footerFixed
            // 'showDropdownOnHover', // For horizontal menu, if applicable
        ],
    ],
];
