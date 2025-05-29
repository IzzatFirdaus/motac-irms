<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-danger text-uppercase']) }}> {{-- Removed text-white as Bootstrap's .btn-danger usually handles text color contrast --}}
  {{ $slot }}
</button>
