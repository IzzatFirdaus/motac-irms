{{--
    resources/views/components/loan-application-status-badge.blade.php

    Displays loan application status as a MYDS-compliant colored badge.
    Applies MYDS color tokens, semantic labeling, and accessibility.
    Updated for MYDS standards and MyGOVEA principles:
    - Consistent semantic colors (MYDS tokens)
    - Accessible markup (ARIA, clear status wording)
    - Minimal, clear, responsive, and citizen-centric UI

    Props:
    - $status: string - The status value (optional if application provided)
    - $application: LoanApplication - Model instance with status (optional)

    Usage:
    <x-loan-application-status-badge :status="'pending_support'" />
    <x-loan-application-status-badge :application="$loanApplication" />

    Supported Statuses:
    - draft, pending_*, approved, issued, returned, completed
    - rejected, cancelled, overdue

    Dependencies: App\Models\LoanApplication, MYDS variables.css
--}}
@props([
    'status' => '',
    'application' => null,
])

@php
    // Import Str helper only if needed (for fallback)
    if (!function_exists('str_title')) {
        function str_title($value) {
            // Simple title case fallback for environments where Illuminate\Support\Str isn't available
            return ucwords(str_replace('_', ' ', $value));
        }
    }

    // Map status to MYDS color tokens and semantic labels
    $badgeClass = '';
    $statusLabel = '';

    if ($application) {
        // Use application model properties if available
        $badgeClass = $application->status_color_class;
        $statusLabel = $application->status_label;
    } else {
        // Map status to MYDS color and label
        $statusOptions = \App\Models\LoanApplication::getStatusOptions();
        $statusLabel = $statusOptions[$status] ?? str_title($status);

        // MYDS color token mapping for badge
        switch ($status) {
            case \App\Models\LoanApplication::STATUS_DRAFT:
                $badgeClass = 'myds-badge myds-badge--secondary';
                break;
            case \App\Models\LoanApplication::STATUS_PENDING_SUPPORT:
            case \App\Models\LoanApplication::STATUS_PENDING_APPROVER_REVIEW:
            case \App\Models\LoanApplication::STATUS_PENDING_BPM_REVIEW:
                $badgeClass = 'myds-badge myds-badge--warning';
                break;
            case \App\Models\LoanApplication::STATUS_APPROVED:
            case \App\Models\LoanApplication::STATUS_PARTIALLY_ISSUED:
            case \App\Models\LoanApplication::STATUS_ISSUED:
                $badgeClass = 'myds-badge myds-badge--info';
                break;
            case \App\Models\LoanApplication::STATUS_REJECTED:
            case \App\Models\LoanApplication::STATUS_CANCELLED:
            case \App\Models\LoanApplication::STATUS_OVERDUE:
                $badgeClass = 'myds-badge myds-badge--danger';
                break;
            case \App\Models\LoanApplication::STATUS_RETURNED:
            case \App\Models\LoanApplication::STATUS_COMPLETED:
                $badgeClass = 'myds-badge myds-badge--success';
                break;
            case \App\Models\LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION:
                $badgeClass = 'myds-badge myds-badge--primary';
                break;
            default:
                $badgeClass = 'myds-badge myds-badge--dark';
                break;
        }
    }
@endphp

@if ($status || $application)
    <span
        {{ $attributes->merge([
            'class' => $badgeClass . ' badge rounded-pill align-middle',
            'role' => 'status',
            'aria-label' => __($statusLabel),
        ]) }}
    >
        {{-- Status text, always visible for accessibility and clarity --}}
        {{ __($statusLabel) }}
    </span>
@else
    <span
        {{ $attributes->merge([
            'class' => 'myds-badge myds-badge--secondary badge rounded-pill align-middle',
            'role' => 'status',
            'aria-label' => __('Status Tidak Diketahui'),
        ]) }}
    >
        {{ __('Status Tidak Diketahui') }}
    </span>
@endif

{{--
    === MYDS & MyGOVEA Documentation ===
    - Uses standardized MYDS badge classes for color and size.
    - Ensures semantic clarity and accessibility (role="status", aria-label).
    - Responsive and minimal markup for clear, citizen-centric feedback.
    - All status values are mapped to MYDS tokens for consistent UI.
    - Complies with Principle 1 (Citizen-Centric), Principle 5 (Minimal Interface), Principle 7 (Clear Status), Principle 14 (Typography), Principle 17 (Error Prevention).
--}}
