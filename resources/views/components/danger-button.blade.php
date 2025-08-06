{{-- resources/views/components/danger-button.blade.php --}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-danger text-uppercase']) }}>
  <i class="bi bi-exclamation-triangle-fill me-1"></i>
  {{ $slot }}
</button>
