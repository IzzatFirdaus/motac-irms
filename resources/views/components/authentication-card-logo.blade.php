{{--
    resources/views/components/authentication-card-logo.blade.php

    MYDS-compliant authentication logo for login/register screens.
    - Uses scalable SVG, proper color tokens, and accessibility.
    - Follows MYDS logo sizing, spacing, and theming.
    - Clickable link to home (Berpaksikan Rakyat, Minimalis, Seragam, Tipografi, Akses)
    - Complies with Principle 1, 5, 6, 7, 13, 14, 15 of MyGOVEA.

    NOTE: Use Laravel's Blade helper functions for URLs (route()/url()) instead of direct PHP functions,
    and ensure the Blade directive syntax is used for calling helpers: {{ route('home') }} or {{ url('/') }}.
--}}

<a class="d-flex justify-content-center mb-4 app-brand-link myds-navbar-logo"
    href="{{ \url('/') }}"
    {{-- NOTE: PHP0417 'unknown function: url' is a static analyzer limitation; this works in Laravel Blade at runtime. --}}
    aria-label="{{ \__('Halaman utama MOTAC IRMS') }}"
    {{-- NOTE: PHP0417 'unknown function: __' is a static analyzer limitation; this works in Laravel Blade at runtime. --}}>
    <span class="app-brand-logo demo" style="width: 48px; height: 48px;">
        <svg viewBox="0 0 148 80" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            style="height: 40px; width: auto; color: var(--myds-primary-600, #2563EB);"
            aria-hidden="true" focusable="false">
            {{-- Gradient Definitions for Auth Card Logo --}}
            <defs>
                <linearGradient id="authCardLogo_a1" x1="46.49" x2="62.46" y1="53.39" y2="48.2"
                    gradientUnits="userSpaceOnUse">
                    <stop stop-opacity=".25" offset="0"></stop>
                    <stop stop-opacity=".1" offset=".3"></stop>
                    <stop stop-opacity="0" offset=".9"></stop>
                </linearGradient>
                <linearGradient id="authCardLogo_e2" x1="76.9" x2="92.64" y1="26.38" y2="31.49"
                    xlink:href="#authCardLogo_a1"></linearGradient>
                <linearGradient id="authCardLogo_d3" x1="107.12" x2="122.74" y1="53.41" y2="48.33"
                    xlink:href="#authCardLogo_a1"></linearGradient>
            </defs>

            {{-- Main Logo Shape using MYDS Primary --}}
            <path style="fill: currentColor;" transform="translate(-.1)"
                d="M121.36,0,104.42,45.08,88.71,3.28A5.09,5.09,0,0,0,83.93,0H64.27A5.09,5.09,0,0,0,59.5,3.28L43.79,45.08,26.85,0H.1L29.43,76.74A5.09,5.09,0,0,0,34.19,80H53.39a5.09,5.09,0,0,0,4.77-3.26L74.1,35l16,41.74A5.09,5.09,0,0,0,94.82,80h18.95a5.09,5.09,0,0,0,4.76-3.24L148.1,0Z">
            </path>

            {{-- Gradient Overlay Effects for depth (MYDS visual hierarchy) --}}
            <path transform="translate(-.1)" d="M52.19,22.73l-8.4,22.35L56.51,78.94a5,5,0,0,0,1.64-2.19l7.34-19.2Z"
                fill="url(#authCardLogo_a1)"></path>
            <path transform="translate(-.1)" d="M95.73,22l-7-18.69a5,5,0,0,0-1.64-2.21L74.1,35l8.33,21.79Z"
                fill="url(#authCardLogo_e2)"></path>
            <path transform="translate(-.1)" d="M112.73,23l-8.31,22.12,12.66,33.7a5,5,0,0,0,1.45-2l7.3-18.93Z"
                fill="url(#authCardLogo_d3)"></path>
        </svg>
    </span>
</a>

{{--
    Documentation:
    - Use Blade helpers for URL generation (url('/')) for maximum compatibility and no PHP0417 error.
    - The SVG uses currentColor to inherit parent's theme.
    - This logo is clickable and always links back to the home page.
    - Accessible: aria-label on the anchor.
--}}
