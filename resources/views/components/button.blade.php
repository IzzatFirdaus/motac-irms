{{-- resources/views/components/button.blade.php --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary text-uppercase']) }}>
  {{ $slot }}
</button>
