{{--
    resources/views/components/loan-transaction-status-badge.blade.php

    MYDS-compliant badge component to display loan transaction status with semantic colors, clear label, and accessibility features.
    Adheres to MYDS design tokens (color, spacing, radius) and MyGOVEA principles (clarity, accessibility, feedback).

    Props:
    - $status: string - The transaction status value (required)
    - $class: string - Additional CSS classes (optional)

    Usage:
    <x-loan-transaction-status-badge :status="$transaction->status" />
    <x-loan-transaction-status-badge status="completed" class="fs-6" />

    Dependencies: App\Helpers\Helpers, App\Models\LoanTransaction, MYDS tokens
--}}
@props(['status', 'class' => ''])

@php
    // Get MYDS-compliant badge color class from helper
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '', 'loan'); // Explicitly pass type argument to fix PHP0423

    // Get localized status text, fallback to readable title
    $statusText = \App\Models\LoanTransaction::getStatusOptions()[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));
    // NOTE: PHP0413 'unknown class: Illuminate\Support\Str' is a static analyzer limitation; this works in Laravel Blade at runtime.

    // Map MYDS semantic icons for accessibility (color+icon, not color-only)
    $iconMap = [
        'approved'     => 'bi-check-circle-fill',
        'completed'    => 'bi-check-circle-fill',
        'pending'      => 'bi-clock-history',
        'rejected'     => 'bi-x-circle-fill',
        'cancelled'    => 'bi-x-circle-fill',
        'overdue'      => 'bi-alarm-fill',
        'issued'       => 'bi-arrow-up-right-circle-fill',
        'processing'   => 'bi-arrow-repeat',
        'returned'     => 'bi-arrow-return-left',
        'draft'        => 'bi-pencil',
    ];
    $iconClass = $iconMap[$status] ?? 'bi-info-circle-fill';
@endphp

<span
    class="badge rounded-pill {{ $badgeClass }} {{ $class }} d-inline-flex align-items-center"
    role="status"
    aria-label="{{ __('Status transaksi:') }} {{ __($statusText) }}"
>
    {{-- Icon for color-blind accessibility (do not rely on color only) --}}
    <i class="bi {{ $iconClass }} me-1" aria-hidden="true"></i>
    {{ __($statusText) }}
</span>
