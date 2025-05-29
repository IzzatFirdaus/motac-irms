{{-- resources/views/components/approval-status-badge.blade.php --}}
@props(['status'])

@php
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($status);
    $statusText = ucfirst(str_replace('_', ' ', $status));
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    {{ $statusText }}
</span>
