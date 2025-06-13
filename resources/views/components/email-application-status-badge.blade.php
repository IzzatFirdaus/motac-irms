{{-- resources/views/components/email-application-status-badge.blade.php --}}
@props([
    'status' => '', // The email application status key
    'application' => null, // Pass the model to use the accessor
])

@php
    $badgeClass = '';
    $statusLabel = '';

    if ($application) {
        $badgeClass = 'text-bg-' . $application->status_color;
        $statusLabel = $application->status_label;
    } else {
        $statusOptions = \App\Models\EmailApplication::getStatusOptions();
        $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));
        $color = 'dark'; // default color

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
