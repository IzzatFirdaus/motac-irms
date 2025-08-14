{{--
    resources/views/components/sort-icon.blade.php

    MYDS-compliant sort icon component for indicating sort direction in tables.
    - Uses semantic icon, colour tokens, and accessible markup.
    - Updated to meet MYDS standards and MyGOVEA principles:
      - Minimalist, clear, accessible (WCAG-compliant).
      - ARIA label for screen readers.
      - Icon uses MYDS colour tokens for clarity.
      - Ensures keyboard and screen reader users can understand sort state.
--}}

@props(['field', 'sortField', 'sortDirection'])

@php
    // Use MYDS colour tokens for icons
    $activeColor = 'text-primary-600'; // MYDS primary color
    $inactiveColor = 'text-gray-400';  // MYDS gray for unsorted
    $iconSize = 'fs-6'; // MYDS recommended size for table icons
    $ariaLabel = __('Sort Direction');
@endphp

<span aria-label="{{ $ariaLabel }}" role="img">
    @if ($sortField === $field)
        @if ($sortDirection === 'asc')
            {{-- Ascending sort --}}
            <i class="bi bi-sort-alpha-down {{ $activeColor }} {{ $iconSize }} ms-1" title="{{ __('Susunan menaik') }}"></i>
        @else
            {{-- Descending sort --}}
            <i class="bi bi-sort-alpha-up-alt {{ $activeColor }} {{ $iconSize }} ms-1" title="{{ __('Susunan menurun') }}"></i>
        @endif
    @else
        {{-- Not sorted: neutral filter icon --}}
        <i class="bi bi-filter {{ $inactiveColor }} {{ $iconSize }} ms-1" title="{{ __('Belum disusun') }}"></i>
    @endif
</span>
