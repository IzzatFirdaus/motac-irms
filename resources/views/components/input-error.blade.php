{{--
    resources/views/components/input-error.blade.php

    MYDS-compliant error message component for form fields.
    - Uses MYDS colour tokens, typography, and spacing.
    - Accessible: Uses role="alert" for screen readers.
    - Principles: Error prevention, clarity, accessibility (MyGOVEA Principles 9, 14, 17).
--}}

@props(['for'])

@error($for)
  <span
    {{ $attributes->merge([
        'class' => 'myds-error-message d-block mt-2 small fw-semibold',
        'role' => 'alert',
        'style' =>
            // MYDS semantic error color and spacing
            'color: var(--myds-danger-700, #B91C1C); background: none;'
    ]) }}
  >
    {{ $message }}
  </span>
@enderror

{{--
    MYDS Documentation:
    - Uses semantic error color (danger-700).
    - Typography: Inter, small, semibold for visibility.
    - Margin-top spacing follows MYDS spacing (8-12px).
    - Accessible: role="alert" ensures screen readers announce errors.
    - See MYDS-Develop-Overview.md and prinsip-reka-bentuk-mygovea.md (Principle 17: Error Prevention).
--}}
