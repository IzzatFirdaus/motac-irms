{{-- resources/views/components/loan-application-status-badge.blade.php --}}
@props([
    'status' => '', // The loan application status key, e.g., 'pending_support'
    'application' => null, // Pass the entire model to use the accessor
])

@php
    $badgeClass = '';
    $statusLabel = '';

    if ($application) {
        // Use the accessor from the model if the object is provided
        $badgeClass = $application->status_color_class;
        $statusLabel = $application->status_label;
    } else {
        // Fallback to switch statement if only status string is provided
        $statusOptions = \App\Models\LoanApplication::getStatusOptions();
        $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));

        switch ($status) {
            case \App\Models\LoanApplication::STATUS_DRAFT:
                $badgeClass = 'text-bg-secondary';
                break;
            case \App\Models\LoanApplication::STATUS_PENDING_SUPPORT:
            case \App\Models\LoanApplication::STATUS_PENDING_APPROVER_REVIEW:
            case \App\Models\LoanApplication::STATUS_PENDING_BPM_REVIEW:
                $badgeClass = 'text-bg-warning';
                break;
            case \App\Models\LoanApplication::STATUS_APPROVED:
            case \App\Models\LoanApplication::STATUS_PARTIALLY_ISSUED:
            case \App\Models\LoanApplication::STATUS_ISSUED:
                $badgeClass = 'text-bg-info';
                break;
            case \App\Models\LoanApplication::STATUS_REJECTED:
            case \App\Models\LoanApplication::STATUS_CANCELLED:
            case \App\Models\LoanApplication::STATUS_OVERDUE:
                $badgeClass = 'text-bg-danger';
                break;
            case \App\Models\LoanApplication::STATUS_RETURNED:
            case \App\Models\LoanApplication::STATUS_COMPLETED:
                $badgeClass = 'text-bg-success';
                break;
            case \App\Models\LoanApplication::STATUS_PARTIALLY_RETURNED_PENDING_INSPECTION:
                $badgeClass = 'text-bg-primary';
                break;
            default:
                $badgeClass = 'text-bg-dark';
                break;
        }
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
