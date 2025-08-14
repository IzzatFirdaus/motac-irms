{{--
    MYDS-compliant: Success notification component with auto-dismiss and accessibility.
    Implements MYDS alert anatomy, color tokens, motion, and a11y roles.
    Prinsip MyGOVEA: Clear feedback (7), error prevention (17), minimal UI (5), accessible (1, 6, 16)
    Usage:
    <x-action-message on="saved">
        {{ __('Data has been saved successfully!') }}
    </x-action-message>
    Dependencies: Alpine.js, Livewire
--}}
@props(['on'])

<div
    {{ $attributes->merge(['class' => 'myds-callout myds-callout--success myds-shadow-card myds-radius-m px-4 py-2 mb-3']) }}
    role="alert"
    aria-live="polite"
    x-data="{ shown: false, timeout: null }"
    x-init="@this.on('{{ $on }}', () => {
        clearTimeout(timeout);
        shown = true;
        timeout = setTimeout(() => { shown = false }, 3000);
    })"
    x-show.transition.out.opacity.duration.400ms="shown"
    x-transition:leave.opacity.duration.400ms
    style="display: none;"
>
    <div class="d-flex align-items-center gap-2">
        {{-- Success icon using MYDS colour --}}
        <i class="bi bi-check-circle-fill myds-txt-success" aria-hidden="true"></i>
        <span class="myds-txt-success-emphasis fw-semibold small">
            {{ $slot->isEmpty() ? 'Berjaya disimpan.' : $slot }}
        </span>
    </div>
</div>
