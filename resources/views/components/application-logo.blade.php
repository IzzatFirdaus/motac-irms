{{--
    resources/views/components/application-logo.blade.php
    MYDS-compliant MOTAC application logo SVG component.
    - Scalable, accessible, and supports theme color via CSS variable.
    - Uses MYDS grid, sizing, and color tokens.
    - Follows MyGOVEA principles: branding, minimalism, accessibility.
    - Responsive to parent's color property via currentColor.
    - Alt text provided via aria-label for accessibility.
--}}

<svg
    viewBox="0 0 148 80"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    {{ $attributes->merge([
        'role' => 'img',
        'aria-label' => __('Logo MOTAC IRMS'),
        'focusable' => 'false',
        'width' => 'auto',
        'height' => '40',  {{-- MYDS recommended logo height --}}
        'style' => 'max-height:40px; max-width:100%; color: var(--myds-primary-600, #2563EB);'
    ]) }}
>
    {{-- Gradient Definitions - for subtle branding shadow --}}
    <defs>
        <linearGradient id="a1_applogo" x1="46.49" x2="62.46" y1="53.39" y2="48.2" gradientUnits="userSpaceOnUse">
            <stop stop-opacity=".25" offset="0"></stop>
            <stop stop-opacity=".1" offset=".3"></stop>
            <stop stop-opacity="0" offset=".9"></stop>
        </linearGradient>
        <linearGradient id="e2_applogo" x1="76.9" x2="92.64" y1="26.38" y2="31.49" xlink:href="#a1_applogo"></linearGradient>
        <linearGradient id="d3_applogo" x1="107.12" x2="122.74" y1="53.41" y2="48.33" xlink:href="#a1_applogo"></linearGradient>
    </defs>

    {{-- Main Logo Path - uses currentColor for theming (MYDS Principle: Branding, Consistency) --}}
    <path
        style="fill: currentColor;" transform="translate(-.1)"
        d="M121.36,0,104.42,45.08,88.71,3.28A5.09,5.09,0,0,0,83.93,0H64.27A5.09,5.09,0,0,0,59.5,3.28L43.79,45.08,26.85,0H.1L29.43,76.74A5.09,5.09,0,0,0,34.19,80H53.39a5.09,5.09,0,0,0,4.77-3.26L74.1,35l16,41.74A5.09,5.09,0,0,0,94.82,80h18.95a5.09,5.09,0,0,0,4.76-3.24L148.1,0Z"
    ></path>

    {{-- Gradient Overlay Paths - subtle highlight/shadow for visual interest --}}
    <path transform="translate(-.1)" d="M52.19,22.73l-8.4,22.35L56.51,78.94a5,5,0,0,0,1.64-2.19l7.34-19.2Z"
        fill="url(#a1_applogo)"></path>
    <path transform="translate(-.1)" d="M95.73,22l-7-18.69a5,5,0,0,0-1.64-2.21L74.1,35l8.33,21.79Z"
        fill="url(#e2_applogo)"></path>
    <path transform="translate(-.1)" d="M112.73,23l-8.31,22.12,12.66,33.7a5,5,0,0,0,1.45-2l7.3-18.93Z"
        fill="url(#d3_applogo)">
    </path>
</svg>
