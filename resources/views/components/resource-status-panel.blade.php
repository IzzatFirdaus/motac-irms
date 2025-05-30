{{-- resources/views/components/resource-status-panel.blade.php --}}
@props([
    'resource',
    'statusAttribute' => 'status', // The attribute on the $resource model that holds the status value
    'statusTextPrefix' => '', // Optional prefix for the displayed status text
])

@php
    $statusValue = strtolower($resource->{$statusAttribute} ?? 'unknown');
    $statusTextPrefix = $statusTextPrefix ?? ''; // Ensure prefix is initialized

    // Attempt to use a translated status label if available from the model
    // Checks for accessors like getStatusLabelAttribute() or a public property like statusLabel
    // e.g., for 'status' attribute, it checks for getStatusLabelAttribute() or statusLabel property
    // e.g., for 'condition_status' attribute, it checks for getConditionStatusLabelAttribute() or conditionStatusLabel property
    $statusLabelAccessorMethodName = 'get' . Illuminate\Support\Str::studly($statusAttribute) . 'LabelAttribute'; // e.g. getStatusLabelAttribute
    $statusLabelPropertyName = Illuminate\Support\Str::camel($statusAttribute) . 'Label'; // e.g. statusLabel

    if (method_exists($resource, $statusLabelAccessorMethodName)) {
        // If an accessor like getStatusLabelAttribute() exists, call it
        $formattedStatus = $resource->{$statusLabelAccessorMethodName}();
    } elseif (property_exists($resource, $statusLabelPropertyName) && !is_null($resource->{$statusLabelPropertyName})) {
        // If a public property like ->statusLabel exists, use it
        $formattedStatus = $resource->{$statusLabelPropertyName};
    } else {
        // Fallback: Convert 'pending_approval' to 'Pending Approval' and translate
        // This also handles the case where no specific accessor or property is found.
        $formattedStatus = __(Illuminate\Support\Str::title(str_replace('_', ' ', $statusValue)));
    }

    // Rely on the Helper to get the appropriate CSS class for the status badge
    // This helper is expected to return a class string like 'bg-label-success', 'text-bg-danger', etc.
    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue);
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    {{ $statusTextPrefix . $formattedStatus }}
</span>
