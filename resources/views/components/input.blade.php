{{--
    resources/views/components/input.blade.php

    MYDS-compliant input field component.
    - Uses MYDS color tokens, focus rings, spacing, and typography.
    - Supports all input types, error states, disabled states, and accessibility features.
    - Follows 18 Principles of MyGOVEA:
      - Berpaksikan Rakyat: Accessible, clear, responsive, citizen-centric.
      - Tipografi: Inter for body, clear sizes.
      - Pencegahan Ralat: Error styling, ARIA attributes.
      - Kawalan Pengguna: Keyboard accessible, visible focus ring.
      - Minimalis: No unnecessary UI, placeholder optional, label via <x-label>.
      - Panduan & Dokumentasi: Inline comments for maintainers.
--}}

@props([
    'disabled' => false,
    'type' => 'text'
])

<input
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
        'class' => 'myds-input form-control form-control-sm' .
            ($disabled ? ' myds-input--disabled' : '') .
            ($attributes->get('aria-invalid') == 'true' || $attributes->get('is-invalid') ? ' myds-input--error' : '')
    ]) }}
    aria-invalid="{{ $attributes->get('aria-invalid') == 'true' ? 'true' : 'false' }}"
    aria-describedby="{{ $attributes->get('aria-describedby') }}"
    autocomplete="{{ $attributes->get('autocomplete') ?? 'off' }}"
/>

{{--
    Notes for maintainers:
    - Use with <x-label> for accessible labeling.
    - For error states, pass 'aria-invalid' => 'true' and add 'is-invalid' class via Laravel validation.
    - MYDS classes: .myds-input applies color, border, radius, focus ring, and sizing per design system.
    - Responsive: Adapts to MYDS grid and spacing for mobile/tablet/desktop.
    - Keyboard accessible: Focus ring appears on tab/focus.
    - See MYDS-Develop-Overview.md and MYDS-Design-Overview.md for details.
--}}
