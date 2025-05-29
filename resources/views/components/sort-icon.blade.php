@props(['field', 'sortField', 'sortDirection'])

@if ($sortField === $field)
    @if ($sortDirection === 'asc')
        <i class="bi bi-sort-alpha-down ms-1"></i>
    @else
        <i class="bi bi-sort-alpha-up-alt ms-1"></i>
    @endif
@else
    <i class="bi bi-filter text-muted ms-1"></i>
@endif
