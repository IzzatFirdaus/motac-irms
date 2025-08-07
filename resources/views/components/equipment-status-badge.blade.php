{{--
    resources/views/components/equipment-status-badge.blade.php

    Displays equipment operational status as a colored badge.
    Maps equipment status constants to appropriate colors and labels.

    Props:
    - $status: string - The equipment status value (required)

    Usage:
    <x-equipment-status-badge :status="$equipment->status" />
    <x-equipment-status-badge status="available" />

    Supported Statuses:
    - available (green), on_loan (blue), under_maintenance (info)
    - disposed/lost (red), damaged_needs_repair (orange)

    Dependencies: App\Models\Equipment
--}}
@props([
    'status' => '',
])

@php
    // Get status options and label
    $statusOptions = \App\Models\Equipment::getStatusOptions();
    $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));
    $badgeClass = '';

    // Map status to appropriate badge color
    switch ($status) {
        case \App\Models\Equipment::STATUS_AVAILABLE:
            $badgeClass = 'text-bg-success';
            break;
        case \App\Models\Equipment::STATUS_ON_LOAN:
            $badgeClass = 'text-bg-primary';
            break;
        case \App\Models\Equipment::STATUS_UNDER_MAINTENANCE:
            $badgeClass = 'text-bg-info';
            break;
        case \App\Models\Equipment::STATUS_DISPOSED:
        case \App\Models\Equipment::STATUS_LOST:
            $badgeClass = 'text-bg-danger';
            break;
        case \App\Models\Equipment::STATUS_DAMAGED_NEEDS_REPAIR:
            $badgeClass = 'text-bg-orange';
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
