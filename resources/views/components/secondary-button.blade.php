{{-- resources/views/components/secondary-button.blade.php --}}
@props(['icon' => null])

<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn-outline-secondary text-uppercase d-inline-flex align-items-center']) }}>
    @if($icon)<i class="bi {{ $icon }} me-1"></i>@endif
    {{ $slot }}
</button>
