{{--
    resources/views/components/checkbox.blade.php

    Bootstrap-styled checkbox input component.
    Simple wrapper for consistent checkbox styling.

    Usage:
    <x-checkbox id="agree" name="terms" value="1" />
    <x-checkbox id="active" name="is_active" checked />

    Dependencies: Bootstrap 5
--}}
<input type="checkbox" {!! $attributes->merge(['class' => 'form-check-input']) !!}>
