{{-- resources/views/components/equipment-status-badge.blade.php --}}
@props([
    'status' => '', // The equipment status key, e.g., 'available', 'on_loan'
])

@php
    $statusOptions = \App\Models\Equipment::getStatusOptions(); /* */
    $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));
    $badgeClass = '';

    switch ($status) {
        case \App\Models\Equipment::STATUS_AVAILABLE:
            $badgeClass = 'text-bg-success';
            break;
        case \App\Models\Equipment::STATUS_ON_LOAN:
            $badgeClass = 'text-bg-primary'; // Using primary for 'on_loan'
            break;
        case \App\Models\Equipment::STATUS_UNDER_MAINTENANCE:
            $badgeClass = 'text-bg-info';
            break;
        case \App\Models\Equipment::STATUS_DISPOSED:
        case \App\Models\Equipment::STATUS_LOST:
            $badgeClass = 'text-bg-danger';
            break;
        case \App\Models\Equipment::STATUS_DAMAGED_NEEDS_REPAIR:
            $badgeClass = 'text-bg-orange'; // Consistently use text-bg-orange as it's defined in theme
            break;
        case \App\Models\Equipment::STATUS_RETURNED_PENDING_INSPECTION: // As defined in Equipment.php model
            $badgeClass = 'text-bg-secondary'; // Or text-bg-info if more appropriate
            break;
        default:
            $badgeClass = 'text-bg-secondary';
            break;
    }
@endphp

@if ($status)
    <span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $badgeClass]) }}>
        {{ __($statusLabel) }}
    </span>
@else
    <span {{ $attributes->merge(['class' => 'badge rounded-pill text-bg-light']) }}>
        {{ __('Status Tidak Diketahui') }}
    </span>
@endif
