{{--
    MYDS-compliant responsive navigation link component
    - Adapts for mobile and sidebar navigation
    - Highlights active state, includes leading icon, and ensures accessibility
    - Implements principles: Minimalist UI, clear navigation, accessibility, semantic markup, and visual hierarchy
    - References MYDS typography, color tokens, spacing, and grid standards
    - See: MYDS-Design-Overview.md, prinsip-reka-bentuk-mygovea.md
--}}

@props(['active' => false, 'icon' => null])

@php
    // MYDS-compliant classes
    $classes = $active
        ? 'myds-nav-link-active d-block w-100 ps-3 pe-2 py-2 border-start border-primary text-start fw-semibold myds-txt-primary myds-bg-primary-50'
        : 'myds-nav-link d-block w-100 ps-3 pe-2 py-2 border-start border-transparent text-start fw-medium myds-txt-black-700';

    // ARIA attributes for accessibility
    $ariaCurrent = $active ? 'page' : null;
@endphp

<a {{ $attributes->merge(['class' => $classes, 'aria-current' => $ariaCurrent]) }}>
    {{-- Leading icon for navigation clarity --}}
    @if($icon)
        <i class="bi {{ $icon }} me-2" aria-hidden="true"></i>
    @endif
    <span class="myds-nav-link-label">{{ $slot }}</span>
</a>

{{--
    MYDS Principles applied:
    - Minimalist and clear navigation (Principle 5, 7, 13)
    - Visual hierarchy via font weight and color (Principle 12, 14)
    - Accessibility: ARIA attributes, semantic markup, large touch target (Principle 1, 8, 15, 16)
    - Consistent, flexible design for mobile/desktop (Principle 10, 6)
    - Documented for maintainers (Principle 18)
--}}
