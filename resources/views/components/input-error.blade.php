{{-- resources/views/components/input-error.blade.php --}}
@props(['for'])

@error($for)
  <span {{ $attributes->merge(['class' => 'invalid-feedback d-block small']) }} role="alert">
    <span class="fw-medium">{{ $message }}</span>
  </span>
@enderror
