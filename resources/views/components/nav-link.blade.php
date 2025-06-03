{{-- resources/views/components/nav-link.blade.php --}}
@props(['active' => false, 'icon' => null]) {{-- Added optional icon prop --}}

@php
$classes = ($active ?? false)
            ? 'nav-link active fw-semibold d-flex align-items-center' /* Changed to fw-semibold for stronger active state */
            : 'nav-link d-flex align-items-center';
@endphp

<li class="nav-item">
  <a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)<i class="bi {{ $icon }} me-2"></i>@endif
    {{ $slot }}
  </a>
</li>
