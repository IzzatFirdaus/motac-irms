{{-- resources/views/components/user-status-badge.blade.php --}}
@props([
    'status' => '', // The user status key, e.g., 'active', 'inactive'
])

@php
    $statusOptions = \App\Models\User::getStatusOptions();
    $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));
    $badgeClass = '';

    switch ($status) {
        case \App\Models\User::STATUS_ACTIVE:
            $badgeClass = 'text-bg-success';
            break;
        case \App\Models\User::STATUS_INACTIVE:
            $badgeClass = 'text-bg-danger';
            break;
        case \App\Models\User::STATUS_PENDING:
            $badgeClass = 'text-bg-warning';
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
