{{-- resources/views/components/responsive-nav-link.blade.php --}}
@props(['active' => false, 'icon' => null]) {{-- Added icon prop --}}

@php
// Ensure bg-primary-light is defined in your MOTAC theme CSS
// e.g., .bg-primary-light { background-color: rgba(var(--bs-primary-rgb), 0.1) !important; }
$classes = ($active ?? false)
            ? 'd-block w-100 ps-3 pe-2 py-2 border-start border-primary text-start fw-semibold text-primary bg-primary-light' // Changed to fw-semibold
            : 'd-block w-100 ps-3 pe-2 py-2 border-start border-transparent text-start fw-medium text-body-secondary'; // Changed to text-body-secondary for inactive
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<i class="bi {{ $icon }} me-2"></i>@endif
    {{ $slot }}
</a>
