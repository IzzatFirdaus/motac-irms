{{--
    resources/views/components/email-application-status-badge.blade.php

    Displays email application status as a colored badge.
    Supports both direct status values and EmailApplication model instances.

    Props:
    - $status: string - The status value (optional if application provided)
    - $application: EmailApplication - Model instance with status (optional)

    Usage:
    <x-email-application-status-badge :status="'pending_support'" />
    <x-email-application-status-badge :application="$emailApplication" />

    Supported Statuses:
    - draft, pending_support, pending_admin, approved, processing, completed
    - rejected, provision_failed, cancelled

    Dependencies: App\Models\EmailApplication
--}}
@props([
    'status' => '',
    'application' => null,
])

@php
    $badgeClass = '';
    $statusLabel = '';

    if ($application) {
        // Use application model properties if available
        $badgeClass = 'text-bg-' . $application->status_color;
        $statusLabel = $application->status_label;
    } else {
        // Map status to color and label
        $statusOptions = \App\Models\EmailApplication::getStatusOptions();
        $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));
        $color = 'dark'; // Default color

        switch ($status) {
            case \App\Models\EmailApplication::STATUS_DRAFT:
                $color = 'secondary';
                break;
            case \App\Models\EmailApplication::STATUS_PENDING_SUPPORT:
            case \App\Models\EmailApplication::STATUS_PENDING_ADMIN:
                $color = 'warning';
                break;
            case \App\Models\EmailApplication::STATUS_APPROVED:
                $color = 'primary';
                break;
            case \App\Models\EmailApplication::STATUS_PROCESSING:
                $color = 'info';
                break;
            case \App\Models\EmailApplication::STATUS_COMPLETED:
                $color = 'success';
                break;
            case \App\Models\EmailApplication::STATUS_REJECTED:
            case \App\Models\EmailApplication::STATUS_PROVISION_FAILED:
            case \App\Models\EmailApplication::STATUS_CANCELLED:
                $color = 'danger';
                break;
        }
        $badgeClass = 'text-bg-' . $color;
    }
@endphp

@if ($status || $application)
    <span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $badgeClass]) }}>
        {{ __($statusLabel) }}
    </span>
@else
    <span {{ $attributes->merge(['class' => 'badge rounded-pill text-bg-light']) }}>
        {{ __('Status Tidak Diketahui') }}
    </span>
@endif
