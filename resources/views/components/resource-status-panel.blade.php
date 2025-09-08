{{--
    resources/views/components/resource-status-panel.blade.php
    MYDS-compliant component: Resource status badge with icon, semantic color, and accessible label.
    Adheres to MyGOVEA principles:
        - Citizen-centric: clear, readable status.
        - Minimalist, clear UI: uses color, icon, and text for clarity.
        - Accessibility: ARIA roles, semantic labels, color+icon, tested contrast.
        - Consistent: uses MYDS tokens/classes for color, radius, spacing.
        - Structure/hierarchy: status icon, prefix, and label.
--}}

@php
    // Import Str helper for string operations
    use Illuminate\Support\Str;

    // Retrieve the status value in lowercase for consistency
    $statusValue = strtolower($resource->{$statusAttribute} ?? 'unknown');

    // Attempt to get a human-readable status label from accessor or property
    $statusLabelAccessorMethodName = 'get' . Str::studly($statusAttribute) . 'LabelAttribute'; // NOTE: PHP0413 'unknown class: Illuminate\Support\Str' is a static analyzer limitation; this works in Laravel Blade at runtime.
    $statusLabelPropertyName = Str::camel($statusAttribute) . 'Label'; // NOTE: PHP0413 'unknown class: Illuminate\Support\Str' is a static analyzer limitation; this works in Laravel Blade at runtime.

    if (method_exists($resource, $statusLabelAccessorMethodName)) {
        $formattedStatus = $resource->{$statusLabelAccessorMethodName}();
    } elseif (property_exists($resource, $statusLabelPropertyName) && !is_null($resource->{$statusLabelPropertyName})) {
        $formattedStatus = $resource->{$statusLabelPropertyName};
    } else {
        $formattedStatus = __(Str::title(str_replace('_', ' ', $statusValue))); // NOTE: PHP0413 'unknown class: Illuminate\Support\Str' is a static analyzer limitation; this works in Laravel Blade at runtime.
    }

    // Get MYDS-compliant status color class
    // Adjusted to always pass $type (default to $type or $statusValue if unset)
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue, $type);

    // Icon selection (semantic + status clarity, avoid color-only indicator)
    $statusIconClass = '';
    if ($showIcon === true) {
        switch ($statusValue) {
            case 'approved':
            case 'completed':
            case 'active':
            case 'available':
            case 'returned_good':
                $statusIconClass = 'bi-check-circle-fill text-success'; // Success status
                break;
            case 'pending':
            case 'pending_support':
            case 'pending_approval':
            case 'processing':
                $statusIconClass = 'bi-clock-history text-warning'; // Pending, in-process
                break;
            case 'rejected':
            case 'cancelled':
            case 'inactive':
                $statusIconClass = 'bi-x-circle-fill text-danger'; // Failure/cancelled
                break;
            case 'on_loan':
            case 'issued':
                $statusIconClass = 'bi-arrow-up-right-circle-fill text-info'; // Info/issued
                break;
            case 'returned_damaged':
            case 'damaged_needs_repair':
            case 'under_maintenance':
                $statusIconClass = 'bi-tools text-warning'; // Maintenance
                break;
            case 'overdue':
                $statusIconClass = 'bi-alarm-fill text-danger'; // Overdue
                break;
            default:
                $statusIconClass = 'bi-info-circle-fill text-secondary'; // Unknown/default
                break;
        }
    } elseif (is_string($showIcon) && Str::startsWith($showIcon, 'bi-')) { // NOTE: PHP0413 'unknown class: Illuminate\Support\Str' is a static analyzer limitation; this works in Laravel Blade at runtime.
        $statusIconClass = $showIcon;
    }
@endphp

<span
    {{ $attributes->merge([
        'class' => "badge rounded-pill {$statusClass} d-inline-flex align-items-center gap-1 px-3 py-1 myds-radius-m",
        'role' => 'status',
        'aria-label' => $formattedStatus,
        'tabindex' => 0, // Keyboard accessibility
    ]) }}
>
    {{-- Status Icon (always with color for accessibility) --}}
    @if($statusIconClass)
        <i class="bi {{ $statusIconClass }} me-1" aria-hidden="true"></i>
    @endif
    {{-- Optional prefix for context (e.g., "Status: ") --}}
    @if($statusTextPrefix)
        <span class="fw-medium text-muted me-1">{{ $statusTextPrefix }}</span>
    @endif
    {{-- Status Text --}}
    <span class="fw-semibold">{{ $formattedStatus }}</span>
</span>

{{--
    MYDS/Accessibility documentation:
    - Badge uses MYDS color tokens for status indication.
    - Icon is shown for semantic clarity; never rely on color alone.
    - ARIA role="status" and aria-label for screen readers.
    - Keyboard accessible via tabindex.
    - Uses .myds-radius-m for consistent border radius.
    - Minimalist, clear, and consistent per MyGOVEA and MYDS.
--}}
