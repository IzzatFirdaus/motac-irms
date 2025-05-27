<?php

// Custom Theme & Layout Configuration for MOTAC RMS
// -------------------------------------------------------------------------------------
// ! IMPORTANT: Clear browser local storage after changes to see effects.
// This file's structure is read by App\Helpers\Helpers::appClasses() using Config::get('custom.custom').
// System Design Reference: 3.3 Helpers::appClasses() merges with config('custom.custom').

return [
  'custom' => [ // This outer 'custom' key is what Config::get('custom.custom') refers to.
    'myLayout' => env('THEME_LAYOUT', 'vertical'), // Options: 'vertical', 'horizontal'
    'myTheme' => env('THEME_SKIN', 'theme-default'), // Options: 'theme-default', 'theme-bordered', 'theme-semi-dark'
    'myStyle' => env('THEME_STYLE', 'light'), // Options: 'light', 'dark'

    // RTL Support settings for the theme
    // 'myRTLSupport' => true: Theme has distinct LTR/RTL CSS asset files (e.g., core.css vs. core-rtl.css)
    // 'myRTLMode' => true:  Attempt to set default HTML dir to 'rtl' if no other language/session preference dictates it.
    // System Design & "The Big Picture" imply dynamic RTL based on language.
    'myRTLSupport' => env('THEME_RTL_ASSETS_SUPPORT', true), // Does the theme have separate RTL asset files?
    'myRTLMode' => env('THEME_DEFAULT_RTL_MODE', false),    // Should the site default to RTL if no other preference?

    // Customizer UI Panel settings
    'hasCustomizer' => env('THEME_HAS_CUSTOMIZER_JS', true),   // Load template-customizer.js or not
    'displayCustomizer' => env('THEME_DISPLAY_CUSTOMIZER_UI', true), // Show the customizer UI panel or not

    // Layout behavior flags
    'menuFixed' => env('THEME_MENU_FIXED', true),
    'menuCollapsed' => env('THEME_MENU_COLLAPSED', false), // Default to expanded menu
    'navbarFixed' => env('THEME_NAVBAR_FIXED', true),
    'navbarDetached' => env('THEME_NAVBAR_DETACHED', false), // If navbar should be detached or full-width style
    'footerFixed' => env('THEME_FOOTER_FIXED', false),

    // Horizontal Menu specific (if 'myLayout' is 'horizontal')
    'showDropdownOnHover' => env('THEME_HORIZONTAL_MENU_HOVER', true),

    // Container settings (used by Helpers.php to set defaults for layouts)
    'container' => env('THEME_CONTENT_CONTAINER', 'container-xxl'), // Options: 'container-fluid', 'container-xxl'
    'containerNav' => env('THEME_NAVBAR_CONTAINER', 'container-xxl'), // Options: 'container-fluid', 'container-xxl'

    // For Helpers::appClasses() to pass to template-customizer.js
    'contentNavbar' => env('THEME_CONTENT_NAVBAR', true), // If navbar is part of the main content area layout
    'isMenu' => env('THEME_IS_MENU', true),
    'isNavbar' => env('THEME_IS_NAVBAR', true),
    'isFooter' => env('THEME_IS_FOOTER', true),
    'isFlex' => env('THEME_IS_FLEX_LAYOUT', false), // For specific flexbox page layouts

    // Example for a theme primary color, if Helpers.php or JS needs it
    'primaryColor' => env('THEME_PRIMARY_COLOR', '#696cff'), // Default theme primary color

    // Controls to show/hide in the template customizer UI panel
    // This list should match controls handled in template-customizer.js
    'customizerControls' => [
      'rtl',          // Toggle RTL/LTR
      'style',        // Toggle Light/Dark mode
      'themes',       // Select different color themes/skins
      'layoutType',   // Menu type (static, fixed, offcanvas) - if applicable
      // 'layoutMenuFlipped', // If horizontal menu flipping is an option
      'showDropdownOnHover', // For horizontal menu
      'layoutNavbarFixed',
      'layoutFooterFixed',
      'menuFixed',        // Added
      'menuCollapsed',    // Added
    ],
  ],
];
