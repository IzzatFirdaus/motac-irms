{{--
    Compatibility view for legacy <x-alert-bootstrap /> usages.
    Delegates to the canonical <x-alert> component so existing templates keep working.
--}}
@props([
    'type' => 'info',
    'message' => null,
    'title' => null,
    'dismissible' => null,
    'icon' => null,
    'errors' => null,
])

<x-alert :type="$type" :message="$message" :title="$title" :dismissible="$dismissible" :icon="$icon" :errors="$errors">
    {{ $slot }}
</x-alert>
