{{-- resources/views/components/approval-status-badge.blade.php --}}
@props(['status'])

@php
    // This helper function is key. It must return Bootstrap 5 badge classes
    // (e.g., "bg-success-subtle text-success-emphasis", "bg-warning-subtle text-warning-emphasis", "bg-danger-subtle text-danger-emphasis")
    // that align with your MOTAC themed semantic colors.
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($status); // Example: Might pass 'badge' type: getStatusColorClass($status, 'badge')
    $statusText = __(ucfirst(str_replace('_', ' ', $status))); // Translated status text
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    {{ $statusText }}
</span>
