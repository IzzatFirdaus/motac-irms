{{-- resources/views/components/danger-button.blade.php --}}
{{-- This danger button will inherit MOTAC Critical Red from the themed Bootstrap .btn-danger class --}}
<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-danger text-uppercase']) }}>
  <i class="bi bi-exclamation-triangle-fill me-1"></i> {{-- Example: Added an icon --}}
  {{ $slot }}
</button>
