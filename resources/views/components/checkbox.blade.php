{{--
    resources/views/components/checkbox.blade.php

    MYDS-compliant Checkbox component.
    - Applies MYDS design system: accessible, semantic, focus ring, and spacing tokens.
    - Uses Inter font for labels, proper ARIA, and a large trigger area for accessibility.
    - Follows MyGOVEA Principles: minimal, clear control, error prevention, accessibility, and documentation.

    Usage:
    <x-checkbox id="agree" name="terms" value="1" />
    <x-checkbox id="active" name="is_active" checked />

    Dependencies: MYDS CSS variables (spacing, color, radius), ARIA
--}}

@props([
    'checked' => false,
    'disabled' => false,
    'indeterminate' => false, // For partial selection state
    // Use slot for label text
])

<label class="myds-checkbox-label d-flex align-items-center gap-2" style="cursor: pointer; font-family: 'Inter', Arial, sans-serif;">
    {{-- Checkbox Input (hidden visually, shown by custom styles) --}}
    <input
        type="checkbox"
        {{ $checked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        aria-checked="{{ $indeterminate ? 'mixed' : ($checked ? 'true' : 'false') }}"
        aria-disabled="{{ $disabled ? 'true' : 'false' }}"
        {!! $attributes->merge([
            'class' => 'myds-checkbox-input',
            // Use MYDS spacing tokens for trigger area
            'style' => 'width: 24px; height: 24px; margin:0;'
        ]) !!}
        @if($indeterminate) data-indeterminate="true" @endif
    >

    {{-- Custom checkbox visual (for MYDS style) --}}
    <span class="myds-checkbox-custom"
        style="
            display: inline-block;
            width: 24px;
            height: 24px;
            background: var(--myds-bg-white, #fff);
            border: 2px solid var(--myds-otl-gray-300, #D4D4D8);
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
            transition: border-color 0.2s, box-shadow 0.2s;
            position: relative;
        ">
        {{-- Checked state --}}
        @if($checked)
            <svg width="20" height="20" viewBox="0 0 20 20" style="position:absolute;top:2px;left:2px;" aria-hidden="true" focusable="false">
                <polyline points="5 10 9 14 15 6"
                    style="fill:none;stroke:var(--myds-primary-600,#2563EB);stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;" />
            </svg>
        @elseif($indeterminate)
            {{-- Indeterminate dash --}}
            <svg width="20" height="20" viewBox="0 0 20 20" style="position:absolute;top:7px;left:4px;" aria-hidden="true" focusable="false">
                <rect x="3" y="9" width="14" height="2.5"
                    style="fill:var(--myds-primary-600,#2563EB);" />
            </svg>
        @endif
        {{-- Focus ring shown via CSS (see below) --}}
    </span>

    {{-- Label text from slot --}}
    <span class="myds-checkbox-text" style="font-size: 1em; color: var(--myds-txt-black-900, #18181B);">
        {{ $slot }}
    </span>
</label>

{{-- MYDS Checkbox: Focus ring and states --}}
<style>
.myds-checkbox-input:focus + .myds-checkbox-custom {
    box-shadow: 0 0 0 2px var(--myds-fr-primary, #96B7FF66);
    border-color: var(--myds-primary-400, #6394FF);
}
.myds-checkbox-input:disabled + .myds-checkbox-custom {
    background: var(--myds-bg-gray-100, #F4F4F5);
    border-color: var(--myds-otl-divider, #F4F4F5);
    cursor: not-allowed;
}
.myds-checkbox-label {
    min-height: 32px; /* Ensures 48x48px touch target with padding */
    padding: 8px 0;
    user-select: none;
}
</style>

{{--
    MYDS Compliance Notes:
    - 24px visual size for checkboxes, 48px total touch area with label.
    - Focus ring uses MYDS tokens for accessibility.
    - Checked and indeterminate states use SVG for clarity (not color-only).
    - Label is always present for screen readers.
    - ARIA attributes for mixed/disabled states.
    - Minimalist, clear, and consistent control as per MyGOVEA.
--}}
