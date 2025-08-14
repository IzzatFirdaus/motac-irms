{{--
    resources/views/components/card.blade.php

    MYDS-compliant Card Component
    - Flexible card with optional header, body, and footer sections
    - Applies MYDS grid, color, spacing, radius, and shadow tokens
    - Accessible structure and ARIA roles
    - Typography, spacing, and card anatomy follow MYDS & MyGOVEA principles

    Props:
    - $title: string (optional)         // Card title
    - $headerClass: string (default: 'myds-card-header py-3')
    - $titleTag: string (default: 'h5') // HTML tag for title
    - $titleClass: string (default: MYDS heading class)
    - $bodyClass: string (optional)
    - $footer: string (optional)        // Footer content
    - $footerClass: string (default: 'myds-card-footer py-3 text-end')

    Usage:
    <x-card title="User Information">
        <p>Card content goes here</p>
    </x-card>

    <x-card :footer="__('Last updated: Today')">
        <x-slot name="title">Custom Title</x-slot>
        Content here
    </x-card>
--}}
@props([
    'title' => null,
    'headerClass' => 'myds-card-header py-3 bg-white border-bottom',
    'titleTag' => 'h5',
    'titleClass' => 'myds-card-title fw-semibold text-primary-700 mb-0',
    'bodyClass' => '',
    'footer' => null,
    'footerClass' => 'myds-card-footer bg-washed border-top py-3 text-end',
])

<div {{ $attributes->merge(['class' => 'myds-card shadow-card radius-l mb-3']) }} role="region" aria-label="{{ $title ?? 'Card Section' }}">
    {{-- Card Header (if title or custom header provided) --}}
    @if ($title || $attributes->has('header'))
        <div class="{{ $headerClass }}">
            @if ($title)
                {{-- MYDS Heading: Poppins, correct size, color --}}
                <{{ $titleTag }} class="{{ $titleClass }}">{{ $title }}</{{ $titleTag }}>
            @endif
            {{ $attributes->get('header') ?? '' }}
        </div>
    @endif

    {{-- Card Body --}}
    <div class="myds-card-body px-4 py-4 {{ $bodyClass }}">
        {{ $slot }}
    </div>

    {{-- Card Footer (if provided) --}}
    @if ($footer)
        <div class="{{ $footerClass }}">
            {{ $footer }}
        </div>
    @endif
</div>

{{--
    MYDS Documentation:
    - Uses MYDS spacing, border-radius (radius-l), and shadow (shadow-card).
    - Card header uses MYDS bg-white and border-bottom for clear separation.
    - Card footer uses MYDS bg-washed and border-top for subtle visual hierarchy.
    - Heading uses Poppins, color token text-primary-700, font weight semibold.
    - Card body applies padding for content comfort.
    - Role="region" and aria-label ensure accessibility and screen reader navigation.
    - All classes are mapped to MYDS tokens for consistency and future-proofing.
    - See MYDS-Design-Overview.md and prinsip-reka-bentuk-mygovea.md for rationale.
--}}
