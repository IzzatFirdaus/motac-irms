{{--
    resources/views/components/action-message.blade.php

    A notification component that displays success messages with auto-dismiss functionality.
    Uses Alpine.js for reactive behavior and automatically hides after 3 seconds.

    Props:
    - $on: string - The event name to listen for (required)

    Usage:
    <x-action-message on="saved">
        {{ __('Data has been saved successfully!') }}
    </x-action-message>

    Dependencies: Alpine.js, Bootstrap 5, Livewire
--}}
@props(['on'])

<div {{ $attributes->merge(['class' => 'alert alert-success small py-2']) }}
    role="alert"
    x-data="{ shown: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => {
        clearTimeout(timeout);
        shown = true;
        timeout = setTimeout(() => { shown = false }, 3000);
    })"
    x-show.transition.out.opacity.duration.1500ms="shown"
    x-transition:leave.opacity.duration.1500ms
    style="display: none;">
    <div class="alert-body d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2"></i>
        <span>{{ $slot->isEmpty() ? __('Berjaya disimpan.') : $slot }}</span>
    </div>
</div>
