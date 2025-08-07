{{--
    resources/views/components/resource-status-panel.blade.php

    Displays a Bootstrap badge for a resource's status, with optional icon and prefix.

    Props:
    - $resource: The model instance for which to show the status
    - $statusAttribute: string (default: 'status') - The attribute to use for status
    - $type: string (for extensibility, not used for color)
    - $showIcon: bool|string - True to auto-select icon, or a string for a custom icon
    - $statusTextPrefix: string - Optional prefix before status text

    Usage:
    <x-resource-status-panel :resource="$equipment" statusAttribute="status" showIcon="bi-check-circle-fill" />
--}}
@props([
    'resource',
    'statusAttribute' => 'status',
    'type' => 'unknown',
    'showIcon' => false,
    'statusTextPrefix' => ''
])

@php
    // Determine status value and label
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

    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue);
    // Icon auto-selection logic
    $statusIconClass = '';
    if ($showIcon === true) {
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

<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    @if($statusIconClass)
        <i class="{{ $statusIconClass }} me-1"></i>
    @endif
    {{ $statusTextPrefix }}{{ $formattedStatus }}
</span>
