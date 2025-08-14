{{--
    resources/views/components/danger-button.blade.php

    MYDS-compliant Danger/Destructive Button component.
    - Uses MYDS color tokens, motion, radius, icon, and accessibility.
    - Intended for delete, remove, or other destructive actions.

    MYDS/Prinsip MyGOVEA References:
    - Tipografi: text uses Inter, uppercase for clear action.
    - Komponen UI/UX: icon before text, clear label, sufficient touch target.
    - Pencegahan Ralat: icon and color indicate danger, ARIA label for clarity.
    - Berpaksikan Rakyat: accessible, focus ring, keyboard/assistive support.
--}}

<button
    {{ $attributes->merge([
        'type' => 'button',
        // MYDS button anatomy: danger color, uppercase, leading icon, padding, radius, shadow, focus ring
        'class' => '
            myds-btn myds-btn-danger
            d-inline-flex align-items-center justify-content-center
            text-uppercase fw-semibold
            px-4 py-2
            rounded-m
            shadow-button
            transition-easeoutback-short
            gap-2
            ' // .myds-btn-danger for MYDS danger color, .rounded-m for 8px radius
    ]) }}
    aria-label="{{ __('Padam') }}"
>
    {{-- MYDS icon: leading, color follows danger token --}}
    <i class="bi bi-exclamation-triangle-fill me-1" aria-hidden="true"></i>
    {{ $slot }}
</button>

{{--
    MYDS Button Notes:
    - .myds-btn-danger: uses --myds-danger-600 for background, --myds-txt-white for text.
    - .rounded-m: 8px border radius (MYDS standard).
    - .shadow-button: 0 1px 3px rgba(0,0,0,0.07) (MYDS button shadow).
    - .transition-easeoutback-short: 200ms cubic-bezier(0.4,1.4,0.2,1) (MYDS motion token).
    - Accessible: aria-label, icon aria-hidden, clear action label.
    - Focus ring: should be styled in CSS to use --myds-fr-danger and --myds-otl-danger-300.
--}}
