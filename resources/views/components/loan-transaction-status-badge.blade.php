{{--
    resources/views/components/loan-transaction-status-badge.blade.php

    Displays loan transaction status as a colored badge.
    Uses helper methods for consistent color coding.

    Props:
    - $status: string - The transaction status value (required)
    - $class: string - Additional CSS classes (optional)

    Usage:
    <x-loan-transaction-status-badge :status="$transaction->status" />
    <x-loan-transaction-status-badge status="completed" class="fs-6" />

    Dependencies: App\Helpers\Helpers, App\Models\LoanTransaction
--}}
@props(['status', 'class' => ''])

@php
    // Get status color class from helper
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '');

    // Get localized status text
    $statusText = \App\Models\LoanTransaction::getStatusOptions()[$status] ?? Str::title(str_replace('_', ' ', $status));
@endphp

<span class="badge {{ $badgeClass }} {{ $class }}">
    {{ __($statusText) }}
</span>
