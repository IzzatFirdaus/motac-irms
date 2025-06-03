{{-- resources/views/layouts/blankLayout.blade.php --}}
{{--
  This layout is intended for pages that do not require the main application
  navigation (sidebar, navbar), such as login, registration, error pages, etc.
  It should adhere to MOTAC's Design Language for professionalism, branding, and accessibility.
--}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!} {{-- Theme-specific page configuration helper --}}
@endisset

@php
    // $configData is used for theme-specific classes (e.g., light/dark mode, RTL support)
    // Ensure \App\Helpers\Helpers::appClasses() provides values consistent with MOTAC's design.
// (e.g., correct paths for LTR/RTL Noto Sans stylesheets if handled by the helper)
$configData = \App\Helpers\Helpers::appClasses();

// $customizerHidden is likely to hide a theme's UI customizer tool on blank pages.
    $customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true);
@endphp

@extends('layouts.commonMaster') {{-- This master layout MUST include:
                                    - Noto Sans font import (Design Language 2.2)
                                    - MOTAC base styles (body font-family, line-height 1.6 for BM - Design Language 2.2)
                                    - Global meta tags (charset, viewport)
                                    - HTML lang attribute set to app()->getLocale()
                                    - CSS custom properties for MOTAC Color Palette (Design Language 2.1)
                                    - Dark Mode base styles if blank pages support it (Design Language 5.0)
                                    - A "Skip to main content" link (Design Language 6.1)
                                --}}

@section('layoutContent')
    {{--
    Accessibility Note (Design Language 6.1 - Skip Links):
    'layouts.commonMaster' should ideally include a "Langkau ke Kandungan Utama" link
    as the very first focusable element after the <body> tag.
    This link should target the `id` of the main content wrapper below (e.g., #main-content-blank).
  --}}

    {{--
    The classes 'authentication-wrapper' and 'authentication-basic' are likely from your UI theme.
    Their styling (padding, centering, background if any) needs to be reviewed and customized
    via your theme's CSS/SCSS to align with MOTAC's Design Language:
    - Section 1.1: Professionalism & Trustworthiness (Clean, uncluttered interfaces)
    - Section 2.1: Color Palette (e.g., Background #F8F9FA, Surface #FFFFFF for panels in light mode)
    - Section 2.2: Typography (Ensure Noto Sans is inherited and scales are respected)
    - Section 2.3: Spacing System (If theme uses spacers, align them with 4px grid if possible)
  --}}
    <div class="authentication-wrapper authentication-basic px-4" id="main-content-blank"> {{-- Added ID for skip link target --}}
        <div class="authentication-inner py-4"> {{-- py-4 provides vertical padding; adjust to match 4px grid if needed --}}
            @yield('content') {{-- Specific page content (e.g., login form from login.blade.php) rendered here --}}
        </div>
    </div>

    {{--
    Alternative structure (commented out in your original file):
    If this layout is intended for diverse blank pages beyond just authentication
    (e.g., full-page error displays, simple public info pages), a more generic centering
    container might be considered, or a different blank layout file could be created.

    <div class="container-fluid d-flex flex-column align-items-center justify-content-center p-0 min-vh-100" id="main-content-blank-generic">
      @yield('content')
    </div>
  --}}
@endsection
