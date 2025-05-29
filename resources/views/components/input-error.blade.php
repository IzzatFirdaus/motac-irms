@props(['for'])

@error($for)
  <span {{ $attributes->merge(['class' => 'invalid-feedback d-block']) }} role="alert"> {{-- Added d-block to ensure it shows --}}
    <span class="fw-medium">{{ $message }}</span>
  </span>
@enderror
