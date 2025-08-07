{{--
    resources/views/components/sort-icon.blade.php

    Shows the current sort direction for a column, or a neutral filter icon if unsorted.

    Props:
    - $field: string - The field/column this icon is for
    - $sortField: string - The currently sorted field
    - $sortDirection: string - 'asc' or 'desc'

    Usage:
    <x-sort-icon field="name" :sortField="$sortField" :sortDirection="$sortDirection" />
--}}

@props(['field', 'sortField', 'sortDirection'])

@if ($sortField === $field)
    @if ($sortDirection === 'asc')
        <i class="bi bi-sort-alpha-down ms-1 text-primary"></i>
    @else
        <i class="bi bi-sort-alpha-up-alt ms-1 text-primary"></i>
    @endif
@else
    <i class="bi bi-filter text-muted ms-1"></i>
@endif
