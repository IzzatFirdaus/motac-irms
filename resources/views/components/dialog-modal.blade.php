{{-- resources/views/components/dialog-modal.blade.php --}}
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
