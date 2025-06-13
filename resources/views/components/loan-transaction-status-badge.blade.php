{{-- resources/views/components/loan-transaction-status-badge.blade.php --}}
@props(['status', 'class' => ''])

@php
    // Get the CSS class from the centralized helper function
    $badgeClass = \App\Helpers\Helpers::getStatusColorClass($status ?? '', 'loan_transaction');

    // Get the display text from the model's static array for consistency and translation
    $statusText = \App\Models\LoanTransaction::getStatusOptions()[$status] ?? Str::title(str_replace('_', ' ', $status));
@endphp

<span class="badge {{ $badgeClass }} {{ $class }}">
    {{ __($statusText) }}
</span>
