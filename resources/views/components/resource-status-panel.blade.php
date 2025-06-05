@php
    // Define a resolved type, defaulting to 'unknown' if $type is not passed or null.
    // The 'type' prop itself should be null if defined in @props and not passed.
    // This handles the "Undefined variable" case defensively and also if $type is null.
    $resolvedType = $type ?? 'unknown';

    $statusValue = strtolower($resource->{$statusAttribute} ?? 'unknown');
    $statusTextPrefix = $statusTextPrefix ?? '';

    $statusLabelAccessorMethodName = 'get' . Illuminate\Support\Str::studly($statusAttribute) . 'LabelAttribute';
    $statusLabelPropertyName = Illuminate\Support\Str::camel($statusAttribute) . 'Label';

    if (method_exists($resource, $statusLabelAccessorMethodName)) {
        $formattedStatus = $resource->{$statusLabelAccessorMethodName}();
    } elseif (property_exists($resource, $statusLabelPropertyName) && !is_null($resource->{$statusLabelPropertyName})) {
        $formattedStatus = $resource->{$statusLabelPropertyName};
    } else {
        $formattedStatus = __(Illuminate\Support\Str::title(str_replace('_', ' ', $statusValue)));
    }

    // Use the $resolvedType which now has a fallback value.
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue, $resolvedType);

    $statusIconClass = '';
    if ($showIcon === true) { // Auto-select icon based on status value
        switch ($statusValue) {
            case 'approved':
            case 'completed':
            case 'active':
            case 'available':
                $statusIconClass = 'bi-check-circle-fill';
                break;
            case 'pending':
            case 'pending_support':
            case 'pending_approval': // Corrected from pending_admin to match typical status values if needed
            case 'processing':
                $statusIconClass = 'bi-clock-history';
                break;
            case 'rejected':
            case 'cancelled':
            case 'inactive':
                $statusIconClass = 'bi-x-circle-fill';
                break;
            case 'on_loan':
                $statusIconClass = 'bi-arrow-up-right-circle-fill';
                break;
            case 'damaged_needs_repair':
            case 'under_maintenance':
                $statusIconClass = 'bi-tools';
                break;
            default:
                $statusIconClass = 'bi-info-circle-fill';
                break;
        }
    } elseif (is_string($showIcon) && Str::startsWith($showIcon, 'bi-')) { // Allow passing a specific Bootstrap Icon class
        $statusIconClass = $showIcon;
    }

@endphp
