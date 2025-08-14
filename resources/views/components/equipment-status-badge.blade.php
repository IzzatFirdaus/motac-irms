{{--
    resources/views/components/equipment-status-badge.blade.php

    MYDS-compliant equipment status badge.
    Displays equipment operational status as a colored badge.
    Applies MYDS design system: color tokens, semantic status, accessibility, and 18 MyGOVEA principles.

    Props:
    - $status: string - The equipment status value (required)

    Usage:
    <x-equipment-status-badge :status="$equipment->status" />
    <x-equipment-status-badge status="available" />

    Supported Statuses:
    - available (green), on_loan (blue), under_maintenance (info)
    - disposed/lost (danger), damaged_needs_repair (warning)

    Dependencies: App\Models\Equipment
--}}
@props([
    'status' => '',
])

@php
    // Get status label (localized, clear, citizen-centric)
    $statusOptions = \App\Models\Equipment::getStatusOptions();
    $statusLabel = $statusOptions[$status] ?? Illuminate\Support\Str::title(str_replace('_', ' ', $status));

    // Map status to MYDS badge color class (seragam, semantic, accessible)
    // Reference: MYDS-Colour-Reference.md
    switch ($status) {
        case \App\Models\Equipment::STATUS_AVAILABLE:
            // Success: Green, clear "available" feedback
            $badgeClass = 'myds-badge bg-success text-white';
            break;
        case \App\Models\Equipment::STATUS_ON_LOAN:
            // Primary: Blue, clear "on loan" feedback
            $badgeClass = 'myds-badge bg-primary text-white';
            break;
        case \App\Models\Equipment::STATUS_UNDER_MAINTENANCE:
            // Info: Cyan, for "maintenance" clarity
            $badgeClass = 'myds-badge bg-info text-white';
            break;
        case \App\Models\Equipment::STATUS_DISPOSED:
        case \App\Models\Equipment::STATUS_LOST:
            // Danger: Red, for "lost/disposed" warning
            $badgeClass = 'myds-badge bg-danger text-white';
            break;
        case \App\Models\Equipment::STATUS_DAMAGED_NEEDS_REPAIR:
            // Warning: Yellow, for "needs repair" caution
            $badgeClass = 'myds-badge bg-warning text-dark';
            break;
        default:
            // Secondary: Neutral for unknown/other statuses
            $badgeClass = 'myds-badge bg-secondary text-white';
            break;
    }
@endphp

@if ($status)
    <span {{ $attributes->merge([
        'class' => $badgeClass . ' rounded-pill px-3 py-1 fw-semibold align-middle',
        'role' => 'status',
        'aria-label' => __('Status Peralatan: ') . __($statusLabel),
    ]) }}>
        {{-- Icon for status (cognitive feedback, accessibility, clear status) --}}
        @switch($status)
            @case(\App\Models\Equipment::STATUS_AVAILABLE)
                <i class="bi bi-check-circle-fill me-1" aria-hidden="true"></i>
                @break
            @case(\App\Models\Equipment::STATUS_ON_LOAN)
                <i class="bi bi-arrow-up-right-circle-fill me-1" aria-hidden="true"></i>
                @break
            @case(\App\Models\Equipment::STATUS_UNDER_MAINTENANCE)
                <i class="bi bi-tools me-1" aria-hidden="true"></i>
                @break
            @case(\App\Models\Equipment::STATUS_DISPOSED)
            @case(\App\Models\Equipment::STATUS_LOST)
                <i class="bi bi-trash-fill me-1" aria-hidden="true"></i>
                @break
            @case(\App\Models\Equipment::STATUS_DAMAGED_NEEDS_REPAIR)
                <i class="bi bi-exclamation-triangle-fill me-1" aria-hidden="true"></i>
                @break
            @default
                <i class="bi bi-info-circle-fill me-1" aria-hidden="true"></i>
        @endswitch
        {{ __($statusLabel) }}
    </span>
@else
    <span {{ $attributes->merge([
        'class' => 'myds-badge bg-secondary text-white rounded-pill px-3 py-1 fw-semibold align-middle',
        'role' => 'status',
        'aria-label' => __('Status Tidak Diketahui'),
    ]) }}>
        <i class="bi bi-question-circle-fill me-1" aria-hidden="true"></i>
        {{ __('Status Tidak Diketahui') }}
    </span>
@endif

{{--
    Documentation:
    - Applies MYDS badge anatomy: rounded-pill, color tokens, semantic icons, accessible text.
    - Uses ARIA for accessibility (Principle 6, 7, 16).
    - Status feedback is clear, seragam, and minimalis (Principle 5, 7).
    - Status label and color map to MYDS tokens (see MYDS-Colour-Reference.md).
    - Cognitive feedback via icon (Principle 9).
    - Responsive and accessible by default.
--}}
