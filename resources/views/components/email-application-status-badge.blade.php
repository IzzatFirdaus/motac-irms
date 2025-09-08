{{--
    resources/views/components/email-application-status-badge.blade.php

    MYDS-compliant badge component for displaying email application status.
    Uses status color tokens, semantic labels, and accessibility features.
    Adheres to MyGOVEA principles: status clarity, minimal UI, accessibility.

    Props:
    - $status: string - Status value (optional if $application provided)
    - $application: EmailApplication model instance (optional)

    Usage:
    <x-email-application-status-badge :status="'pending_support'" />
    <x-email-application-status-badge :application="$emailApplication" />

    Supported Statuses:
    - draft, pending_support, pending_admin, approved, processing, completed
    - rejected, provision_failed, cancelled

    Dependencies: App\Models\EmailApplication, MYDS color tokens
--}}
@props([
    'status' => '',
    'application' => null,
])

@php
    // Import Str helper for string manipulation
    use Illuminate\Support\Str;

    // Helper to localize strings, with fallback to raw string if __() is unavailable
    if (!function_exists('__')) {
        function __($string) { return $string; }
    }

    // Determine badge class and label according to MYDS standards
    $badgeClass = '';
    $statusLabel = '';

    // Use model instance if available
    if ($application) {
        // Model provides color and label per status (ensure model returns MYDS-compliant values)
        $badgeClass = 'myds-badge myds-badge--' . ($application->status_color ?? 'secondary');
        $statusLabel = $application->status_label ?? 'Status Tidak Diketahui';
    } else {
        // Fallback: Map status to MYDS color tokens and semantic label
        $statusOptions = \App\Models\EmailApplication::getStatusOptions();
    $statusLabel = isset($statusOptions[$status]) ? $statusOptions[$status] : Str::title(str_replace('_', ' ', $status));
    // NOTE: PHP0413 'unknown class: Illuminate\\Support\\Str' is a static analyzer limitation; this works in Laravel Blade at runtime.
        // Map status to MYDS badge color token
        switch ($status) {
            case \App\Models\EmailApplication::STATUS_DRAFT:
                $badgeClass = 'myds-badge myds-badge--secondary';
                break;
            case \App\Models\EmailApplication::STATUS_PENDING_SUPPORT:
            case \App\Models\EmailApplication::STATUS_PENDING_ADMIN:
                $badgeClass = 'myds-badge myds-badge--warning';
                break;
            case \App\Models\EmailApplication::STATUS_APPROVED:
                $badgeClass = 'myds-badge myds-badge--primary';
                break;
            case \App\Models\EmailApplication::STATUS_PROCESSING:
                $badgeClass = 'myds-badge myds-badge--info';
                break;
            case \App\Models\EmailApplication::STATUS_COMPLETED:
                $badgeClass = 'myds-badge myds-badge--success';
                break;
            case \App\Models\EmailApplication::STATUS_REJECTED:
            case \App\Models\EmailApplication::STATUS_PROVISION_FAILED:
            case \App\Models\EmailApplication::STATUS_CANCELLED:
                $badgeClass = 'myds-badge myds-badge--danger';
                break;
            default:
                $badgeClass = 'myds-badge myds-badge--secondary';
                break;
        }
    }
@endphp

@if ($status || $application)
    {{-- MYDS badge: rounded, semantic color, accessible label --}}
    <span
        {{ $attributes->merge([
            'class' => $badgeClass . ' myds-badge--pill',
            'role' => 'status',
            'aria-label' => __($statusLabel),
        ]) }}>
        {{-- Status label, localized --}}
        {{ __($statusLabel) }}
    </span>
@else
    {{-- Fallback for unknown status --}}
    <span
        {{ $attributes->merge([
            'class' => 'myds-badge myds-badge--pill myds-badge--light',
            'role' => 'status',
            'aria-label' => __('Status Tidak Diketahui'),
        ]) }}>
        {{ __('Status Tidak Diketahui') }}
    </span>
@endif

{{--
    MYDS Principles Applied:
    - Semantic color tokens for clarity and accessibility.
    - Minimal, consistent badge anatomy.
    - ARIA role for screen reader support.
    - Status label localized for rakyat-centric design.
    - Responsive and adaptable for accessibility and device compatibility.
--}}
