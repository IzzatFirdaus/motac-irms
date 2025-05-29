@if ($errors->any())
  <div {!! $attributes->merge(['class' => 'alert alert-danger small']) !!} role="alert"> {{-- Added 'small' for consistency if desired, can be removed --}}
    {{-- <div class="alert-body"> --}} {{-- alert-body is not a standard Bootstrap class, content can be direct --}}
      <div class="fw-bold mb-1">{{ __('Amaran! Terdapat ralat pada input anda.') }}</div> {{-- Made translatable and more specific --}}

      <ul class="mb-0 ps-3"> {{-- Added ps-3 for list indentation --}}
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    {{-- </div> --}}
  </div>
@endif
