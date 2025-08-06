{{-- resources/views/components/approval-status-badge.blade.php --}}
@props(['status', 'class' => ''])

@php
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '');
    $statusText = \App\Models\Approval::$STATUSES_LABELS[$status] ?? Str::title(str_replace('_', ' ', $status));
@endphp

<span class="badge rounded-pill {{ $badgeClass }} {{ $class }}">
    {{ __($statusText) }}
</span>
