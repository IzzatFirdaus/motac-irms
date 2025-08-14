{{-- resources/views/components/input-error.blade.php --}}
@props(['for'])

@error($for)
  <span {{ $attributes->merge(['class' => 'invalid-feedback d-block small']) }} role="alert"> {{-- Added small class --}}
    <span class="fw-medium">{{ $message }}</span>
  </span>
@enderror
