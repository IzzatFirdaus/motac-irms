{{-- resources/views/layouts/sections/styles.blade.php --}}
@php
    $configData = \App\Helpers\Helpers::appClasses();
    $rtlSupport = $configData['rtlSupport'] ?? '';
    $currentStyle = $configData['style'] ?? 'light';
    $currentTheme = $configData['theme'] ?? 'theme-motac';
    $hasCustomizer = $configData['hasCustomizer'] ?? false;
    $styleSuffix = $currentStyle !== 'light' ? '-' . $currentStyle : '';
@endphp

{{-- Google Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet">
@if (($configData['textDirection'] ?? 'ltr') === 'rtl' && ($configData['myRTLSupport'] ?? false))
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
@endif

{{-- Vendor Fonts (Icons) --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/fontawesome.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/tabler-icons.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}" />
{{-- ADDED: Bootstrap Icons CSS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
{{-- OR if you have it locally (ensure path is correct): --}}
{{-- <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/bootstrap-icons.css') }}" /> --}}


{{-- Core CSS --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/css' . $rtlSupport . '/core' . $styleSuffix . '.css') }}"
    class="{{ $hasCustomizer ? 'template-customizer-core-css' : '' }}" />

{{-- Theme CSS --}}
<link rel="stylesheet"
    href="{{ asset('assets/vendor/css' . $rtlSupport . '/' . $currentTheme . $styleSuffix . '.css') }}"
    class="{{ $hasCustomizer ? 'template-customizer-theme-css' : '' }}" />

{{-- Demo CSS (Contains styles for theme demo pages - review if needed for MOTAC) --}}
<link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

{{-- Vendor Libs CSS --}}
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}" />

{{-- Custom MOTAC System Styles --}}
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

@yield('vendor-style')
@yield('page-style')
@stack('custom-css')
@livewireStyles

{{-- The duplicate Google Fonts link at the end of your original file has been removed for cleanliness.
     It's already included at the top. --}}
