{{--
    resources/views/components/input-error.blade.php

    Displays validation errors for specific form fields.
    Only shows when the specified field has validation errors.

    Props:
    - $for: string - The field name to check for errors (required)

    Usage:
    <x-input-error for="email" class="mt-2" />
    <x-input-error for="password" />

    Dependencies: Bootstrap 5, Laravel validation
--}}
@props(['for'])

@error($for)
  <span {{ $attributes->merge(['class' => 'invalid-feedback d-block small']) }} role="alert">
    <span class="fw-medium">{{ $message }}</span>
  </span>
@enderror
