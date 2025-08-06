{{-- resources/views/components/loan-transaction-status-badge.blade.php --}}
@props(['status', 'class' => ''])

@php
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '');
    $statusText = \App\Models\LoanTransaction::getStatusOptions()[$status] ?? Str::title(str_replace('_', ' ', $status));
@endphp

<span class="badge {{ $badgeClass }} {{ $class }}">
    {{ __($statusText) }}
</span>
