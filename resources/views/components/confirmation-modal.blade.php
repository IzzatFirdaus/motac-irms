{{-- resources/views/components/confirmation-modal.blade.php --}}
@props(['id' => null, 'maxWidth' => null]) {{-- Removed title, content, footer as they come from slots --}}

{{-- This component defines the content structure for a confirmation-style dialog,
     assuming x-modal provides the outer Bootstrap modal shell and standard slots.
     Ensure x-modal is styled according to MOTAC theme. --}}
<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
  <x-slot name="title">
      <div class="d-flex align-items-center">
        {{-- Example: Add a default warning icon for confirmation modals if not overridden --}}
        <i class="bi bi-exclamation-triangle-fill text-warning me-2 fs-4"></i>
        {{ $title }} {{-- Slot for title --}}
      </div>
  </x-slot>

  <x-slot name="content">
    <div class="text-sm text-gray-600"> {{-- Generic styling, ensure it matches MOTAC theme's muted text --}}
      {{ $content }} {{-- Slot for content --}}
    </div>
  </x-slot>

  <x-slot name="footer">
    {{ $footer }} {{-- Slot for footer buttons --}}
  </x-slot>
</x-modal>
