<<<<<<< HEAD
{{--
    resources/views/components/approval-status-badge.blade.php

    MYDS-compliant Approval Status Badge component.
    - Uses MYDS color tokens, rounded-pill style, accessible markup, and semantic labels.
    - Follows MyGOVEA principles: clarity, accessibility, minimalism, and status feedback.
    - Ensure all status display uses MYDS status color and typography tokens.
    - Status values and labels are mapped via model and helper.

    NOTE: Adjustments applied:
    - Explicitly pass both arguments to getStatusColorClass($status, 'approval') to fix PHP0423.
    - Use PHP's built-in Str helper (Illuminate\Support\Str) only if available; otherwise, fallback to regular PHP string functions for fallback label.
--}}

@props(['status', 'class' => ''])


@php
    // Import Str helper if available
        // No import needed; use fully qualified class name for Str helper

    // Map status to MYDS-compliant color token classes and semantic label
    // Explicitly pass the type argument ('approval') to getStatusColorClass
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '', 'approval');

    // Status label: fallback to readable format if not found
    // Use model label if exists, otherwise convert status to readable string
    if (isset(\App\Models\Approval::$STATUSES_LABELS[$status])) {
        $statusText = \App\Models\Approval::$STATUSES_LABELS[$status];
    } else {
        // Fallback: use Str helper if available, otherwise use PHP functions
        if (class_exists('Illuminate\\Support\\Str')) {
            $statusText = \Illuminate\Support\Str::title(str_replace('_', ' ', $status));
        } else {
            $statusText = ucwords(str_replace('_', ' ', $status));
        }
    }
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
=======
{{-- resources/views/components/approval-status-badge.blade.php --}}
@props(['status'])

@php
    $statusValue = strtolower($status ?? 'unknown');
    // Define the appropriate type for approval statuses
    $type = 'approval'; //

    // Call the helper with both status and type
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue, $type); //
    $statusText = __(ucfirst(str_replace('_', ' ', $status))); // Translated status text
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    {{ $statusText }}
</span>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
