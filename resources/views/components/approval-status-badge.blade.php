{{-- resources/views/components/approval-status-badge.blade.php --}}
@props(['status'])

@php
    $statusValue = strtolower($status ?? 'unknown');
    // Define the appropriate type for approval statuses
    $type = 'approval'; //

    // Call the helper with both status and type
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue, $type); //
    $statusText = __(ucfirst(str_replace('_', ' ', $status))); // Translated status text
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    {{ $statusText }}
</span>
