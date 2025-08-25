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

    // Local status labels (use translations where appropriate)
    $statusOptions = [
        'draft' => __('Draft'),
        'pending_support' => __('Pending Sokongan'),
        'pending_admin' => __('Pending Admin'),
        'approved' => __('Diluluskan'),
        'processing' => __('Diproses'),
        'completed' => __('Selesai'),
        'rejected' => __('Ditolak'),
        'provision_failed' => __('Gagal Penyediaan'),
        'cancelled' => __('Dibatalkan'),
    ];

    $determineColor = function (?string $s) {
        $color = 'dark';
        switch ($s) {
            case 'draft':
                $color = 'secondary';
                break;
            case 'pending_support':
            case 'pending_admin':
                $color = 'warning';
                break;
            case 'approved':
                $color = 'primary';
                break;
            case 'processing':
                $color = 'info';
                break;
            case 'completed':
                $color = 'success';
                break;
            case 'rejected':
            case 'provision_failed':
            case 'cancelled':
                $color = 'danger';
                break;
        }
        return $color;
    };

    if ($application) {
        // If the passed object provides explicit label/color use them, otherwise fall back to status string
        if (isset($application->status_label) && isset($application->status_color)) {
            $badgeClass = 'text-bg-' . $application->status_color;
            $statusLabel = $application->status_label;
        } elseif (isset($application->status)) {
            $s = $application->status;
            $statusLabel = $statusOptions[$s] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $s));
            $badgeClass = 'text-bg-' . $determineColor($s);
        }
    } else {
        // Map provided status string to label and color
        $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));
        $badgeClass = 'text-bg-' . $determineColor($status);
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
