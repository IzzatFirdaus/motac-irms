{{--
    resources/views/components/confirmation-modal.blade.php

    Confirmation dialog modal with warning styling and icon.
    Extends the base modal component with confirmation-specific styling.

    Props:
    - $id: string - Modal ID (optional)
    - $maxWidth: string - Modal size: 'sm', 'lg', 'xl', 'fullscreen' (optional)

    Slots:
    - $title: Modal title
    - $content: Main content area
    - $footer: Footer with action buttons

    Usage:
    <x-confirmation-modal>
        <x-slot name="title">
            {{ __('Confirm Deletion') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete this item?') }}
        </x-slot>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary">Cancel</button>
            <button type="button" class="btn btn-danger">Delete</button>
        </x-slot>
    </x-confirmation-modal>

    Dependencies: Bootstrap 5, Bootstrap Icons, x-modal component
--}}
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
