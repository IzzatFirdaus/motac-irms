{{-- resources/views/layouts/sections/styles.blade.php --}}
{{-- This Blade partial includes all global stylesheets for the application. --}}
{{-- System Design: Phase 2 (Stylesheet Inclusion), "The Big Picture" --}}
{{-- Design Language: Consistent styles, MOTAC branding, LTR/RTL, Light/Dark support. --}}

@php
    // $configData is globally available, sourced from \App\Helpers\Helpers::appClasses() in commonMaster.blade.php
    $rtlSupport = $configData['rtlSupport'] ?? ''; // Empty or '/rtl'
    $currentStyle = $configData['style'] ?? 'light'; // 'light' or 'dark'
    $currentTheme = $configData['theme'] ?? 'theme-motac'; // Defined in Helpers.php, e.g., 'theme-motac'
    $hasCustomizer = $configData['hasCustomizer'] ?? false; // To add specific classes if customizer is active

    // Suffix for dark mode stylesheets, e.g., "-dark.css"
    $styleSuffix = ($currentStyle !== 'light') ? '-' . $currentStyle : '';
@endphp

{{-- Local Font Loading --}}
{{-- Design Language: Typography - Legible, Hierarchical, Official (Sans-serif) --}}
<style>
    /*
      Example for locally hosted Public Sans (replace with your chosen primary UI font if different).
      Ensure font files are located in public/assets/fonts/
      This is a recommended approach for consistency and performance.
    */
    /*
    @font-face {
        font-family: 'Public Sans';
        src: url("{{ asset('assets/fonts/public-sans/PublicSans-Regular.woff2') }}") format('woff2'),
             url("{{ asset('assets/fonts/public-sans/PublicSans-Regular.woff') }}") format('woff');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'Public Sans';
        src: url("{{ asset('assets/fonts/public-sans/PublicSans-Bold.woff2') }}") format('woff2'),
             url("{{ asset('assets/fonts/public-sans/PublicSans-Bold.woff') }}") format('woff');
        font-weight: 700;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'Public Sans';
        src: url("{{ asset('assets/fonts/public-sans/PublicSans-SemiBold.woff2') }}") format('woff2'),
             url("{{ asset('assets/fonts/public-sans/PublicSans-SemiBold.woff') }}") format('woff');
        font-weight: 600;
        font-style: normal;
        font-display: swap;
    }
    */

    /* Noto Kufi Arabic for Arabic language support, if maintained and RTL is active */
    @if( ($configData['textDirection'] ?? 'ltr') === 'rtl' && ($configData['myRTLSupport'] ?? false) )
    @font-face {
        font-family: 'Noto Kufi Arabic';
        font-weight: 100 900; /* Variable font weight range */
        src: url("{{ asset('assets/fonts/noto-kufi-arabic/NotoKufiArabic-VariableFont_wght.ttf') }}") format('truetype-variations');
        font-display: swap;
    }
    @endif
</style>
{{-- OR use Google Fonts CDN if preferred (ensure privacy implications are considered for internal systems) --}}
{{--
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
@if( ($configData['textDirection'] ?? 'ltr') === 'rtl' && ($configData['myRTLSupport'] ?? false) )
<link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
@endif
--}}

{{-- Vendor Fonts (Icons) --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />

{{-- Core CSS --}}
{{-- Dynamically loads LTR/RTL and Light/Dark versions of core.css --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/css' . $rtlSupport . '/core' . $styleSuffix .'.css') }}" class="{{ $hasCustomizer ? 'template-customizer-core-css' : '' }}" />

{{-- Theme CSS --}}
{{-- Dynamically loads LTR/RTL and Light/Dark versions of the selected MOTAC theme (e.g., theme-motac.css) --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/css' . $rtlSupport . '/' . $currentTheme . $styleSuffix .'.css') }}" class="{{ $hasCustomizer ? 'template-customizer-theme-css' : '' }}" />

{{-- Demo CSS (usually contains styles for the theme demo pages, review if all are needed for MOTAC) --}}
<link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

{{-- Vendor Libs CSS (Commonly used across the application) --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" /> {{-- For search autocomplete --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" /> {{-- For notifications --}}
{{-- Other global vendor CSS like Select2, Flatpickr can be added here if used extensively.
     Otherwise, load them on specific pages/components via @push('page-style') or @yield('vendor-style'). --}}
{{-- Example:
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
--}}

{{-- Custom MOTAC System Styles --}}
{{-- Design Language: Consistent Design Language - Place MOTAC-specific overrides here --}}
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

{{-- Page specific vendor CSS injected by child views --}}
@yield('vendor-style')

{{-- Page specific custom CSS injected by child views --}}
@yield('page-style')

{{-- Custom CSS pushed from Blade views (e.g., Livewire components) --}}
@stack('custom-css')

{{-- Livewire Styles --}}
@livewireStyles
