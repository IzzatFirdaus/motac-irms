{{-- resources/views/components/resource-status-panel.blade.php --}}
@props([
    'resource',
    'statusAttribute' => 'status', // The attribute on the $resource model that holds the status value
    'statusTextPrefix' => '', // Optional prefix for the displayed status text
])

@php
    $statusValue = strtolower($resource->{$statusAttribute} ?? 'unknown');

    // Attempt to use a translated status label if available from the model
    // Checks for accessors like getStatusLabelAttribute() or a public property like statusLabel
    $statusLabelAccessor = 'get' . Str::studly($statusAttribute) . 'LabelAttribute';
    $statusDisplayAccessor = Str::camel($statusAttribute) . 'Label'; // E.g., statusLabel, conditionStatusLabel

    if (method_exists($resource, $statusLabelAccessor)) {
        $formattedStatus = $resource->{$statusLabelAccessor}();
    } elseif (property_exists($resource, $statusDisplayAccessor) && !is_null($resource->{$statusDisplayAccessor})) {
        $formattedStatus = $resource->{$statusDisplayAccessor};
    } else {
        // Fallback: Convert 'pending_approval' to 'Pending Approval'
        $formattedStatus = __(Str::title(str_replace('_', ' ', $statusValue)));
    }

    // Map status values to Bootstrap label color suffixes (e.g., 'success', 'danger', 'warning')
    // This aligns with the typical output of a helper like Helpers::getStatusColorClass()
    // This map should be comprehensive for all statuses in your system design.
    $statusColorMap = [
        // General Positive Statuses
        'available' => 'success',
        'completed' => 'success',
        'approved' => 'success',
        'returned' => 'success',
        'returned_good' => 'success',
        'active' => 'success',
        'new' => 'success', // For equipment condition

        // General Neutral/In-Progress Statuses
        'pending' => 'warning',
        'pending_support' => 'warning',
        'pending_admin' => 'warning',
        'pending_hod_review' => 'warning',
        'pending_bpm_review' => 'warning',
        'processing' => 'info',
        'issued' => 'info',
        'partially_issued' => 'info',
        'on_loan' => 'primary', // Or info, depending on theme
        'returned_pending_inspection' => 'info',
        'fair' => 'info', // For equipment condition

        // General Negative/Attention Statuses
        'under_maintenance' => 'warning',
        'damaged_needs_repair' => 'warning',
        'returned_damaged' => 'warning',
        'returned_minor_damage' => 'warning',
        'minor_damage' => 'warning', // For equipment condition
        'overdue' => 'danger',
        'rejected' => 'danger',
        'cancelled' => 'danger',
        'provision_failed' => 'danger',
        'inactive' => 'danger',
        'disposed' => 'danger',
        'lost' => 'danger',
        'reported_lost' => 'danger',
        'items_reported_lost' => 'danger',
        'major_damage' => 'danger', // For equipment condition
        'unserviceable' => 'danger',
        'unserviceable_on_return' => 'danger',
        'returned_major_damage' => 'danger',

        // Draft/Secondary Statuses
        'draft' => 'secondary',
        'partially_returned' => 'secondary', // Could also be info or warning

        // Default/Unknown
        'unknown' => 'secondary',
    ];

    // Determine the Bootstrap label color class suffix
    $bootstrapColorSuffix = $statusColorMap[$statusValue] ?? 'secondary'; // Default to secondary if status not in map

    $badgeClass = "badge rounded-pill bg-label-{$bootstrapColorSuffix}";

    $statusClass = \App\Helpers\Helpers::getStatusColorClass($statusValue);
    $statusText = ucfirst(str_replace('_', ' ', $statusValue));
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill ' . $statusClass]) }}>
    {{ $statusText }}
</span>
