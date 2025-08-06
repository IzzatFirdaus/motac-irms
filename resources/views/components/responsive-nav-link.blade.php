{{-- resources/views/components/responsive-nav-link.blade.php --}}
@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
            ? 'd-block w-100 ps-3 pe-2 py-2 border-start border-primary text-start fw-semibold text-primary bg-primary-light'
            : 'd-block w-100 ps-3 pe-2 py-2 border-start border-transparent text-start fw-medium text-body-secondary';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<i class="bi {{ $icon }} me-2"></i>@endif
    {{ $slot }}
</a>
