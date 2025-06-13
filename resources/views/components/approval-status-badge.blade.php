{{-- resources/views/components/approval-status-badge.blade.php --}}
@props(['status', 'class' => ''])

@php
    // 1. Get the CSS class from the centralized helper function.
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '', 'approval');

    // 2. Get the specific display text from the Approval model's static options.
    // This uses the $STATUSES_LABELS array you've defined.
    $statusText = \App\Models\Approval::getStatusOptions()[$status] ?? Str::title(str_replace('_', ' ', $status));
@endphp

<span class="badge rounded-pill {{ $badgeClass }} {{ $class }}">
    {{-- The __() helper handles translation --}}
    {{ __($statusText) }}
</span>
