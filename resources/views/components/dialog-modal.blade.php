{{--
    resources/views/components/dialog-modal.blade.php

    Standard dialog modal component that extends the base modal.
    Provides consistent structure for dialog-style modals.

    Props:
    - $id: string - Modal ID (optional)
    - $maxWidth: string - Modal size (optional)

    Slots:
    - $title: Modal title
    - $content: Main content area
    - $footer: Footer with action buttons

    Usage:
    <x-dialog-modal>
        <x-slot name="title">
            {{ __('Edit User') }}
        </x-slot>

        <x-slot name="content">
            <!-- Form fields here -->
        </x-slot>

        <x-slot name="footer">
            <button type="button" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </x-slot>
    </x-dialog-modal>

    Dependencies: x-modal component
--}}
@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <x-slot name="title">
        {{ $title }}
    </x-slot>
    <x-slot name="content">
        {{ $content }}
    </x-slot>
    <x-slot name="footer">
        {{ $footer }}
    </x-slot>
</x-modal>
