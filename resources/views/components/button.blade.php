{{-- resources/views/components/button.blade.php --}}
{{-- This primary button will inherit MOTAC Blue from the themed Bootstrap .btn-primary class --}}
<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary text-uppercase']) }}>
  {{ $slot }}
</button>
