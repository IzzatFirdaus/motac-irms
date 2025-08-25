{{--
    resources/views/components/label.blade.php

    Bootstrap-styled form label component.
    Provides consistent label styling across forms.

    Props:
    - $value: string - Label text (optional, can use slot instead)

    Usage:
    <x-label for="email" value="{{ __('Email Address') }}" />
    <x-label for="name">{{ __('Full Name') }}</x-label>

    Dependencies: Bootstrap 5
--}}
@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label fw-medium']) }}>
  {{ $value ?? $slot }}
</label>
