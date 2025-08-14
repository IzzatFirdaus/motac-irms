{{-- ++ ADDED: @props directive to define the component's API and set default values ++ --}}
@props([
    'resource',
    'statusAttribute' => 'status', // Defaults to 'status' if not provided
    'type' => 'unknown',
    'showIcon' => false,
    'statusTextPrefix' => ''
])

@php
    // The 'type' prop is already defined with a default value above.
    $resolvedType = $type;

    $statusValue = strtolower($resource->{$statusAttribute} ?? 'unknown');

    $statusLabelAccessorMethodName = 'get' . Illuminate\Support\Str::studly($statusAttribute) . 'LabelAttribute';
    $statusLabelPropertyName = Illuminate\Support\Str::camel($statusAttribute) . 'Label';

    if (method_exists($resource, $statusLabelAccessorMethodName)) {
        $formattedStatus = $resource->{$statusLabelAccessorMethodName}();
    } elseif (property_exists($resource, $statusLabelPropertyName) && !is_null($resource->{$statusLabelPropertyName})) {
        $formattedStatus = $resource->{$statusLabelPropertyName};
    } else {
        $formattedStatus = __(Illuminate\Support\Str::title(str_replace('_', ' ', $statusValue)));
    }

    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue, $resolvedType);

    $statusIconClass = '';
    if ($showIcon === true) { // Auto-select icon based on status value
        switch ($statusValue) {
            case 'approved':
            case 'completed':
            case 'active':
            case 'available':
            case 'returned_good': // Added for transaction statuses
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
            case 'issued': // Added for transaction statuses
                $statusIconClass = 'bi-arrow-up-right-circle-fill';
                break;
            case 'returned_damaged': // Added for transaction statuses
            case 'damaged_needs_repair':
            case 'under_maintenance':
                $statusIconClass = 'bi-tools';
                break;
             case 'overdue': // Added for application status
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

{{-- This is the actual HTML output of the component. It should be present in your file. --}}
{{-- If your file is empty besides the PHP block, you should add this part. --}}
<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    @if($statusIconClass)
        <i class="{{ $statusIconClass }} me-1"></i>
    @endif
    {{ $statusTextPrefix }}{{ $formattedStatus }}
</span>
