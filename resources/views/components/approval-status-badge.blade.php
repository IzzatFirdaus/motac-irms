{{--
    resources/views/components/approval-status-badge.blade.php

    Displays approval status as a colored badge with proper localization.
    Uses helper methods for consistent color coding across the application.

    Props:
    - $status: string - The approval status value (required)
    - $class: string - Additional CSS classes (optional)

    Usage:
    <x-approval-status-badge :status="$approval->status" />
    <x-approval-status-badge :status="'approved'" class="fs-6" />

    Dependencies: App\Helpers\Helpers, App\Models\Approval
--}}
@props(['status', 'class' => ''])

@php
    // Get status color class from helper
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '');

    // Get localized status text
    $statusText = \App\Models\Approval::$STATUSES_LABELS[$status] ?? Str::title(str_replace('_', ' ', $status));
@endphp

<span class="badge rounded-pill {{ $badgeClass }} {{ $class }}">
    {{ __($statusText) }}
</span>
