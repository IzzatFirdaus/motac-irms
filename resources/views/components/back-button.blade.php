{{--
    resources/views/components/back-button.blade.php

    MYDS-compliant Back Button component.
    Reusable navigation control for returning to previous page, with clear semantics, accessibility, and consistent styling.

    Props:
    - $route: string - The URL or route to navigate to (required)
    - $text: string - Button text (default: 'Kembali')
    - $icon: string - Icon class (default: 'bi-arrow-left')

    MYDS Principles Applied:
    - Minimalist, clear UI (Prinsip 5)
    - Consistent (Prinsip 6)
    - Navigation clarity (Prinsip 7)
    - Accessibility/ARIA (Prinsip 1, 17)
    - Default settings (Prinsip 15)
--}}

@props([
    'route',
    'text' => 'Kembali',
    'icon' => 'bi-arrow-left'
])

<a
    href="{{ $route }}"
    {{ $attributes->merge([
        // MYDS button anatomy: outline, spacing, radius, typography, focus ring
        'class' => 'myds-btn myds-btn-outline myds-btn-sm d-inline-flex align-items-center fw-semibold rounded-md px-3 py-2 gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-400',
        'aria-label' => $text,
        'role' => 'button',
    ]) }}
>
    {{-- Leading icon for clear user control --}}
    <i class="bi {{ $icon }} fs-5" aria-hidden="true"></i>
    {{-- Conditional text for accessibility and clarity --}}
    @if ($text)
        <span class="myds-btn-text">{{ $text }}</span>
    @endif
</a>

{{--
    Documentation:
    - Uses MYDS button colors, radius, and font (see custom.css/variables.css).
    - Keyboard accessibility: focus ring, ARIA label, role="button".
    - Always left-aligned icon, as per navigation conventions.
    - Minimal spacing and uppercase font for clarity.
    - Default text is "Kembali", but can be customized.
    - Fully responsive and accessible for assistive tech.
    - See prinsip-reka-bentuk-mygovea.md: Prinsip 1, 5, 6, 7, 15, 17.
--}}
