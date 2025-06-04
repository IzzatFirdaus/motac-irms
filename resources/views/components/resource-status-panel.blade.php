{{-- resources/views/components/resource-status-panel.blade.php --}}
@props([
    'resource',
    'statusAttribute' => 'status',
    'statusTextPrefix' => '',
    'type', // ADD THIS LINE: Define the 'type' prop
    'icon' => null, // Optional: pass 'true' to auto-select based on status, or a specific BI class
    'showIcon' => false // Simpler prop to control icon visibility
])

@php
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

    // UPDATED LINE: Pass both $statusValue and $type
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue, $type);

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

<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass . ' d-inline-flex align-items-center']) }}>
    @if($statusIconClass)
        <i class="bi {{ $statusIconClass }} me-1"></i>
    @endif
    {{ $statusTextPrefix . $formattedStatus }}
</span>
