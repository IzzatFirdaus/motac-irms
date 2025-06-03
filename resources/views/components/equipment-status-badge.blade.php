{{-- resources/views/components/equipment-status-badge.blade.php --}}
@props([
    'status' => '', // The equipment status key, e.g., 'available', 'on_loan'
])

@php
    // Fetches status labels from the Equipment model [cite: 1]
    // (Assuming App\Models\Equipment::getStatusOptions() returns an array like ['available' => 'Tersedia', ...])
    $statusOptions = \App\Models\Equipment::getStatusOptions();
    $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));
    $badgeClass = '';

    // Determine badge class based on status, using Bootstrap 5.3+ text-bg-* classes
    // Status constants are defined in App\Models\Equipment [cite: 1]
    switch ($status) {
        case \App\Models\Equipment::STATUS_AVAILABLE:
            $badgeClass = 'text-bg-success';
            break;
        case \App\Models\Equipment::STATUS_ON_LOAN:
            $badgeClass = 'text-bg-warning';
            break;
        case \App\Models\Equipment::STATUS_UNDER_MAINTENANCE:
            $badgeClass = 'text-bg-info';
            break;
        case \App\Models\Equipment::STATUS_DISPOSED:
        case \App\Models\Equipment::STATUS_LOST:
            $badgeClass = 'text-bg-danger';
            break;
        case \App\Models\Equipment::STATUS_DAMAGED_NEEDS_REPAIR:
            // You might want a specific color for this, e.g., using a custom class or another Bootstrap color.
            // Using 'text-bg-orange' as a placeholder; ensure this class is defined in your CSS if it's custom.
        // For standard Bootstrap, you might reuse 'text-bg-warning' or 'text-bg-primary'.
        $badgeClass = 'text-bg-orange'; // Example: replace with actual Bootstrap class like 'text-bg-warning' or a custom one
        break;
    default:
        $badgeClass = 'text-bg-secondary'; // Fallback for any other statuses
            break;
    }
@endphp

@if ($status)
    <span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $badgeClass]) }}>
        {{ __($statusLabel) }}
    </span>
@else
    {{-- Fallback if no status is provided --}}
    <span {{ $attributes->merge(['class' => 'badge rounded-pill text-bg-light']) }}>
        {{ __('Status Tidak Diketahui') }}
    </span>
@endif
