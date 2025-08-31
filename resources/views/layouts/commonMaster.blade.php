{{-- resources/views/layouts/commonMaster.blade.php --}}
{{-- This is the root master layout for all MOTAC system pages. It sets up the HTML, head, and body, and includes theme styles, scripts, and accessibility features. --}}

<!DOCTYPE html>

@php
    $configData = \App\Helpers\Helpers::appClasses();
    $currentLocale = app()->getLocale();
    $textDirection = $configData['textDirection'] ?? ($currentLocale === 'ar' ? 'rtl' : 'ltr');
    $appName = __($configData['templateName'] ?? __('Sistem Pengurusan Sumber Bersepadu MOTAC'));
@endphp

<html lang="{{ $currentLocale }}" class="" dir="{{ $textDirection }}" data-theme="{{ $configData['myTheme'] ?? 'theme-motac' }}"
    data-assets-path="{{ asset('assets/') . '/' }}" data-base-url="{{ url('/') }}" data-framework="laravel"
    data-template="{{ ($configData['myLayout'] ?? 'vertical') . '-menu-' . ($configData['myTheme'] ?? 'theme-motac') . '-light' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
    {{-- Skip link for keyboard and assistive technology users (WCAG / MYDS) --}}
    <a class="myds-skip-link" href="#main-content">{{ __('Langkau ke kandungan utama') }}</a>
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="color-scheme" content="light dark" />

    <title>@yield('title') | {{ $appName }}</title>
    <meta name="description"
        content="{{ __($configData['templateDescription'] ?? 'Sistem Dalaman Bersepadu untuk Kementerian Pelancongan, Seni dan Budaya Malaysia (MOTAC) bagi pengurusan permohonan emel dan pinjaman peralatan ICT.') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon-motac.ico') }}" />

    {{-- =================================================================================== --}}
    {{-- == START: UNIFIED THEME MANAGEMENT SCRIPT == --}}
    {{-- This single script now controls the theme to prevent flashing and conflicts. --}}
    {{-- It runs in the <head> before the body is rendered. --}}
    {{-- =================================================================================== --}}
    <script>
        (function() {
            const themeStorageKey = 'theme-preference';
            let preference = localStorage.getItem(themeStorageKey);
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const currentTheme = preference || (systemPrefersDark ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', currentTheme);
        })();
    </script>
    {{-- =================================================================================== --}}
    {{-- == END: UNIFIED THEME MANAGEMENT SCRIPT == --}}
    {{-- =================================================================================== --}}

    {{-- Include all main layout stylesheets (with RTL, theme, and custom MOTAC styles) --}}
    @include('layouts.sections.layout-styles')

    {{-- Include core JS helpers, theme config, and template customizer if enabled --}}
    @include('layouts.sections.layout-scripts-includes')

    {{-- Vite assets: main app and optional modules --}}
    @vite(['resources/js/app.js'])

    {{-- MYDS and MyGOVEA compliant styles --}}
    <style>
        /* MYDS Color Tokens (Light Mode) */
        :root {
            /* MYDS Typography */
            --myds-font-body: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            --myds-font-heading: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";

            /* MYDS Primary Colors */
            --myds-blue: #2563EB;
            --myds-primary: var(--myds-blue);

            /* MYDS Semantic Colors */
            --myds-success: #16A34A;
            --myds-danger: #DC2626;
            --myds-warning: #D97706;
            --myds-info: #0284C7;

            /* MYDS Gray Scale */
            --myds-gray-50: #F8FAFC;
            --myds-gray-100: #F1F5F9;
            --myds-gray-200: #E2E8F0;
            --myds-gray-300: #CBD5E1;
            --myds-gray-400: #94A3B8;
            --myds-gray-500: #64748B;
            --myds-gray-600: #475569;
            --myds-gray-700: #334155;
            --myds-gray-800: #1E293B;
            --myds-gray-900: #0F172A;

            /* MYDS Background Tokens */
            --myds-bg-primary: #FFFFFF;
            --myds-bg-secondary: var(--myds-gray-50);
            --myds-bg-muted: var(--myds-gray-100);

            /* MYDS Text Tokens */
            --myds-txt-primary: var(--myds-gray-900);
            --myds-txt-secondary: var(--myds-gray-700);
            --myds-txt-muted: var(--myds-gray-500);

            /* MYDS Focus Ring */
            --myds-focus-ring: var(--myds-blue);

            /* MYDS Grid System */
            --myds-grid-gap-desktop: 24px;
            --myds-grid-gap-tablet: 24px;
            --myds-grid-gap-mobile: 18px;
            --myds-container-max-width: 1280px;
        }

        /* Dark mode color tokens */
        [data-bs-theme="dark"] {
            --myds-bg-primary: var(--myds-gray-900);
            --myds-bg-secondary: var(--myds-gray-800);
            --myds-bg-muted: var(--myds-gray-700);
            --myds-txt-primary: var(--myds-gray-50);
            --myds-txt-secondary: var(--myds-gray-200);
            --myds-txt-muted: var(--myds-gray-400);
        }

        /* MYDS Typography System */
        body {
            font-family: var(--myds-font-body);
            color: var(--myds-txt-primary);
            background-color: var(--myds-bg-primary);
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: var(--myds-font-heading);
            color: var(--myds-txt-primary);
        }

        /* MYDS Heading Sizes */
        h1 { font-size: 2.25rem; line-height: 2.75rem; font-weight: 600; } /* 36px */
        h2 { font-size: 1.875rem; line-height: 2.375rem; font-weight: 600; } /* 30px */
        h3 { font-size: 1.5rem; line-height: 2rem; font-weight: 600; } /* 24px */
        h4 { font-size: 1.25rem; line-height: 1.75rem; font-weight: 500; } /* 20px */
        h5 { font-size: 1rem; line-height: 1.5rem; font-weight: 500; } /* 16px */
        h6 { font-size: 0.875rem; line-height: 1.25rem; font-weight: 500; } /* 14px */

        /* MYDS Body Text */
        p, .body-medium { font-size: 1rem; line-height: 1.5rem; } /* 16px */
        .body-small { font-size: 0.875rem; line-height: 1.25rem; } /* 14px */
        .body-large { font-size: 1.125rem; line-height: 1.625rem; } /* 18px */

        /* WCAG 2.1 Accessibility - Skip Links */
        .visually-hidden-focusable:not(:focus):not(:active) {
            position: absolute !important;
            height: 1px; width: 1px;
            overflow: hidden;
            clip: rect(1px, 1px, 1px, 1px);
            white-space: nowrap;
        }
        .visually-hidden-focusable:focus,
        .visually-hidden-focusable:active {
            position: static !important;
            height: auto; width: auto;
            padding: 12px 16px;
            background: var(--myds-gray-900);
            color: #ffffff;
            border-radius: 8px;
            z-index: 10000;
            font-weight: 600;
            text-decoration: none;
        }

        /* WCAG 2.1 Focus Indicators */
        :focus-visible {
            outline: 3px solid var(--myds-focus-ring);
            outline-offset: 2px;
        }

        /* MYDS Grid System Implementation */
        .myds-container {
            max-width: var(--myds-container-max-width);
            margin: 0 auto;
            padding: 0 var(--myds-grid-gap-desktop);
        }

        @media (max-width: 1023px) {
            .myds-container {
                padding: 0 var(--myds-grid-gap-tablet);
            }
        }

        @media (max-width: 767px) {
            .myds-container {
                padding: 0 var(--myds-grid-gap-mobile);
            }
        }

        /* MYDS Button Styles */
        .btn-myds-primary {
            background-color: var(--myds-primary);
            border-color: var(--myds-primary);
            color: #ffffff;
            font-weight: 500;
            padding: 12px 24px;
            border-radius: 8px;
            min-height: 48px; /* WCAG touch target */
        }

        .btn-myds-primary:hover,
        .btn-myds-primary:focus {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            color: #ffffff;
        }

        /* MyGOVEA Citizen-Centric Design Principles */
        .mygovea-accessible {
            /* Ensure minimum touch targets (48px) */
            min-height: 48px;
            min-width: 48px;
        }

        /* Language support for RTL */
        [dir="rtl"] {
            text-align: right;
        }
        [dir="rtl"] .myds-container {
            padding-right: var(--myds-grid-gap-desktop);
            padding-left: var(--myds-grid-gap-desktop);
        }
    </style>
</head>

<body>
    {{-- Accessibility skip link for screen readers/keyboard navigation --}}
    <a href="#main-content" class="visually-hidden-focusable">{{ __('Langkau ke Kandungan Utama') }}</a>

    {{-- The layout-specific content will be injected here --}}
    @yield('layoutContent')

    {{-- Include all common JavaScript files and main initialization --}}
    @include('layouts.sections.layout-scripts')
</body>
</html>
