{{-- Component: resources/views/components/resource-status-panel.blade.php --}}
{{-- Displays a Bootstrap badge for a resource's status, with optional icon and prefix. --}}

@props([
    'resource',
    'statusAttribute' => 'status', // Defaults to 'status' if not provided
    'type' => 'unknown',
    'showIcon' => false,
    'statusTextPrefix' => ''
])

@php
    // Resolve the type (not used in status color class anymore, but kept for future extensibility)
    $resolvedType = $type;

    // Extract status value from the resource
    $statusValue = strtolower($resource->{$statusAttribute} ?? 'unknown');

    // Try to get the formatted status label from accessor or property
    $statusLabelAccessorMethodName = 'get' . Illuminate\Support\Str::studly($statusAttribute) . 'LabelAttribute';
    $statusLabelPropertyName = Illuminate\Support\Str::camel($statusAttribute) . 'Label';

    if (method_exists($resource, $statusLabelAccessorMethodName)) {
        $formattedStatus = $resource->{$statusLabelAccessorMethodName}();
    } elseif (property_exists($resource, $statusLabelPropertyName) && !is_null($resource->{$statusLabelPropertyName})) {
        $formattedStatus = $resource->{$statusLabelPropertyName};
    } else {
        $formattedStatus = __(Illuminate\Support\Str::title(str_replace('_', ' ', $statusValue)));
    }

    // Only pass one argument to getStatusColorClass, as defined in Helpers.php
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue);

    // Determine icon class based on status value or showIcon override
    $statusIconClass = '';
    if ($showIcon === true) { // Auto-select icon based on status value
        switch ($statusValue) {
            case 'approved':
            case 'completed':
            case 'active':
            case 'available':
            case 'returned_good':
                $statusIconClass = 'bi-check-circle-fill';
                break;
            case 'pending':
            case 'pending_support':
            case 'pending_approval':
            case 'processing':
                $statusIconClass = 'bi-clock-history';
                break;
            case 'rejected':
            case 'cancelled':
            case 'inactive':
                $statusIconClass = 'bi-x-circle-fill';
                break;
            case 'on_loan':
            case 'issued':
                $statusIconClass = 'bi-arrow-up-right-circle-fill';
                break;
            case 'returned_damaged':
            case 'damaged_needs_repair':
            case 'under_maintenance':
                $statusIconClass = 'bi-tools';
                break;
            case 'overdue':
                $statusIconClass = 'bi-alarm-fill';
                break;
            default:
                $statusIconClass = 'bi-info-circle-fill';
                break;
        }
    } elseif (is_string($showIcon) && Str::startsWith($showIcon, 'bi-')) {
        $statusIconClass = $showIcon;
    }
@endphp

{{-- Output: Bootstrap badge, with optional icon and prefix --}}
<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    @if($statusIconClass)
        <i class="{{ $statusIconClass }} me-1"></i>
    @endif
    {{ $statusTextPrefix }}{{ $formattedStatus }}
</span>
