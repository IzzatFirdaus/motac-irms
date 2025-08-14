{{-- resources/views/components/dialog-modal.blade.php --}}
@props(['id' => null, 'maxWidth' => null]) {{-- Removed title, content, footer as they come from slots --}}

{{-- This component is a general dialog, assuming x-modal provides the
     outer Bootstrap modal shell and standard named slots.
     Ensure x-modal is styled according to MOTAC theme. --}}
<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <x-slot name="title">
        {{ $title }} {{-- Slot for title --}}
    </x-slot>

    <x-slot name="content">
        {{ $content }} {{-- Slot for content --}}
    </x-slot>

    <x-slot name="footer">
        {{ $footer }} {{-- Slot for footer buttons --}}
    </x-slot>
</x-modal>
