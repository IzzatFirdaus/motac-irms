{{--
    resources/views/components/approval-status-badge.blade.php

    MYDS-compliant Approval Status Badge component.
    - Uses MYDS color tokens, rounded-pill style, accessible markup, and semantic labels.
    - Follows MyGOVEA principles: clarity, accessibility, minimalism, and status feedback.
    - Ensure all status display uses MYDS status color and typography tokens.
    - Status values and labels are mapped via model and helper.
--}}

@props(['status', 'class' => ''])

@php
    use Illuminate\Support\Str;
    // Map status to MYDS-compliant color token classes and semantic label
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '');

    // Status label: fallback to readable format if not found
    $statusText = \App\Models\Approval::$STATUSES_LABELS[$status] ?? Str::title(str_replace('_', ' ', $status));
@endphp

<span
    {{ $attributes->merge([
        'class' => "myds-status-badge badge rounded-pill {$badgeClass} {$class}",
        'role' => 'status',
        'aria-label' => "@lang('Status: ' . $statusText)"
    ]) }}
    tabindex="0"
>
    {{-- Use __() for localization and screen reader accessibility --}}
    @lang($statusText)
</span>

{{--
    MYDS documentation:
    - Status badge uses MYDS color tokens (see variables.css).
    - Rounded-pill shape (border-radius: 9999px) for clear visual status.
    - Accessible: role="status", aria-label for screen readers, and tabindex for keyboard focus.
    - All status badges in the application must use this component for consistency.
    - Principles applied: Citizen-centric (clear feedback), Minimalist UI, Accessibility, Error Prevention, UI/UX Component.
--}}
