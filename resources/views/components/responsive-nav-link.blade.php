@props(['active'])

@php
$classes = ($active ?? false)
            ? 'd-block w-100 ps-3 pe-2 py-2 border-start border-primary text-start fw-medium text-primary bg-primary-light'  // Adjusted ps, text-start, bg
            : 'd-block w-100 ps-3 pe-2 py-2 border-start border-transparent text-start fw-medium text-body'; // Adjusted ps, text-start
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
{{-- Note: 'bg-primary-light' would be a custom class you define, e.g., a lighter shade of your primary color.
    Bootstrap alternatives could be 'list-group-item-action list-group-item-primary' if used in a list-group.
    For simplicity, if 'bg-primary-light' is not defined, you might use 'active' class and style it, or a subtle 'bg-light'.
--}}
