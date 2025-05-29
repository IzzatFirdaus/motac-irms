@props(['id' => null, 'maxWidth' => null, 'title', 'content', 'footer'])

{{-- Assuming x-modal is a base Bootstrap modal component like modal-motac-generic.blade.php --}}
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
