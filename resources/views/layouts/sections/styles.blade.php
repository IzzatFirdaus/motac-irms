{{-- resources/views/layouts/sections/styles.blade.php --}}

{{--
    $configData is globally available here, sourced from Helpers::appClasses() in commonMaster.blade.php.
    It should contain keys like 'rtlSupport', 'style', 'theme', 'hasCustomizer'.
--}}
@php
    $rtlSupport = $configData['rtlSupport'] ?? ''; // e.g., '/rtl' or ''
    $currentStyle = $configData['style'] ?? 'light'; // 'light' or 'dark'
    $currentTheme = $configData['theme'] ?? 'theme-default'; // e.g., 'theme-default'
    $hasCustomizer = $configData['hasCustomizer'] ?? false; // From theme config via Helpers

    // Construct the path suffix for dark mode styles if not 'light'
    $styleSuffix = ($currentStyle !== 'light') ? '-' . $currentStyle : '';
@endphp

{{-- Local font loading for Noto Kufi Arabic --}}
<style>
    @font-face {
        font-family: 'Noto Kufi Arabic'; /* Consistent naming */
        font-weight: normal; /* Or specify range if variable font with multiple weights */
        src: url("{{ $configData['assetsPath'] ?? asset('/assets/') }}fonts/NotoKufiArabic-VariableFont_wght.ttf") format('truetype');
        font-display: swap; /* Improve perceived performance */
    }
    /* Example for Public Sans if hosted locally, ensure font files are in assetsPath/fonts/ */
    /*
    @font-face {
        font-family: 'Public Sans';
        src: url("{{ $configData['assetsPath'] ?? asset('/assets/') }}fonts/public-sans-v15-latin-regular.woff2") format('woff2'),
             url("{{ $configData['assetsPath'] ?? asset('/assets/') }}fonts/public-sans-v15-latin-regular.woff") format('woff');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }
    */
</style>
{{-- Or use Google Fonts CDN if preferred and not self-hosting all fonts --}}
{{--
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..700&display=swap" rel="stylesheet">
--}}


<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/fonts/fontawesome.css' }}" />
<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/fonts/tabler-icons.css' }}" />
<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/fonts/flag-icons.css' }}" />

{{-- Dynamically loads LTR/RTL and Light/Dark versions of core.css --}}
{{-- System Design 3.3 (Helpers::appClasses), The Big Picture (Stylesheet Inclusion) --}}
<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/css' . $rtlSupport . '/core' . $styleSuffix .'.css' }}" class="{{ $hasCustomizer ? 'template-customizer-core-css' : '' }}" />

{{-- Dynamically loads LTR/RTL and Light/Dark versions of the selected theme (e.g., theme-default.css) --}}
<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/css' . $rtlSupport . '/' . $currentTheme . $styleSuffix .'.css' }}" class="{{ $hasCustomizer ? 'template-customizer-theme-css' : '' }}" />

<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'css/demo.css' }}" />

<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/libs/perfect-scrollbar/perfect-scrollbar.css' }}" />
<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/libs/node-waves/node-waves.css' }}" />
<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/libs/typeahead-js/typeahead.css' }}" />
<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'vendor/libs/toastr/toastr.css' }}" />
{{-- Add other global vendor CSS like Select2, Flatpickr if used globally, otherwise load them on specific pages --}}

<link rel="stylesheet" href="{{ ($configData['assetsPath'] ?? asset('/assets/')) . 'css/custom.css' }}" />

@yield('vendor-style')

@yield('page-style')

@stack('custom-css')

@livewireStyles
