{{--
    resources/views/components/input.blade.php

    Bootstrap-styled input component with consistent sizing and styling.
    Supports various input types and disabled state.

    Props:
    - $disabled: bool - Whether input is disabled (default: false)
    - $type: string - Input type (default: 'text')

    Usage:
    <x-input type="email" name="email" placeholder="Enter email" />
    <x-input type="password" name="password" :disabled="true" />

    Dependencies: Bootstrap 5
--}}
@props(['disabled' => false, 'type' => 'text'])

<input type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-control form-control-sm']) !!}>
