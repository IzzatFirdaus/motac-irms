{{--
    MYDS-compliant: Outlined secondary button for non-destructive actions.
    - Follows MYDS button anatomy, spacing, colour tokens, radius, and focus ring.
    - Accessibility: ARIA attributes, focus ring, keyboard operable.
    - MyGOVEA principles: Kawalan Pengguna (clear/consistent), Minimalis, Tipografi, Seragam.

    Props:
    - $icon: string|null - Bootstrap/MYDS icon class to show before text

    Example usage:
    <x-secondary-button>Cancel</x-secondary-button>
    <x-secondary-button icon="bi-arrow-left">Back</x-secondary-button>
--}}
@props(['icon' => null])

<button
    {{ $attributes->merge([
        'type' => 'button',
        // MYDS: outline button, uppercase, flex alignment, spacing, radius, focus ring
        'class' => '
            myds-btn myds-btn--secondary
            btn
            text-uppercase
            d-inline-flex align-items-center
            px-4 py-2
            fw-semibold
            rounded-2
            border
            border-otl-gray-300
            bg-white
            text-primary
            transition-colors
            focus:outline-none
            focus:ring-2
            focus:ring-fr-primary
            focus:ring-offset-2
        ',
        'aria-label' => $icon ? __('Tindakan Sekunder: ') . trim($slot) : trim($slot),
    ]) }}
>
    {{-- Leading Icon (optional, MYDS: 20x20 icon, semantically placed) --}}
    @if($icon)
        <i class="bi {{ $icon }} me-2" aria-hidden="true"></i>
    @endif
    {{-- Button Text --}}
    <span class="myds-btn-text">{{ $slot }}</span>
</button>
