{{-- resources/views/components/sort-icon.blade.php --}}
@props(['field', 'sortField', 'sortDirection'])

@if ($sortField === $field)
    @if ($sortDirection === 'asc')
        <i class="bi bi-sort-alpha-down ms-1 text-primary"></i> {{-- Added text-primary for active sort --}}
    @else
        <i class="bi bi-sort-alpha-up-alt ms-1 text-primary"></i> {{-- Added text-primary for active sort --}}
    @endif
@else
    <i class="bi bi-filter text-muted ms-1"></i> {{-- General filter icon when not active sort field --}}
@endif
