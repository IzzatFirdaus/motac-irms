{{-- resources/views/components/confirmation-modal.blade.php --}}
@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
  <x-slot name="title">
      <div class="d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill text-warning me-2 fs-4"></i>
        {{ $title }}
      </div>
  </x-slot>

  <x-slot name="content">
    <div class="text-sm text-gray-600">
      {{ $content }}
    </div>
  </x-slot>

  <x-slot name="footer">
    {{ $footer }}
  </x-slot>
</x-modal>
