@props(['status', 'class' => ''])

@php
    $badgeClass = 'badge ';
    $statusText = '';

    switch ($status) {
        case \App\Models\LoanTransaction::STATUS_PENDING:
            $badgeClass .= 'bg-label-info';
            $statusText = __('Pending');
            break;
        case \App\Models\LoanTransaction::STATUS_ISSUED:
            $badgeClass .= 'bg-label-success';
            $statusText = __('Issued');
            break;
        case \App\Models\LoanTransaction::STATUS_RETURNED_PENDING_INSPECTION:
            $badgeClass .= 'bg-label-warning';
            $statusText = __('Returned (Pending Inspection)');
            break;
        case \App\Models\LoanTransaction::STATUS_RETURNED_GOOD:
            $badgeClass .= 'bg-label-primary';
            $statusText = __('Returned (Good Condition)');
            break;
        case \App\Models\LoanTransaction::STATUS_RETURNED_DAMAGED:
            $badgeClass .= 'bg-label-danger';
            $statusText = __('Returned (Damaged)');
            break;
        case \App\Models\LoanTransaction::STATUS_ITEMS_REPORTED_LOST:
            $badgeClass .= 'bg-label-dark';
            $statusText = __('Items Reported Lost');
            break;
        case \App\Models\LoanTransaction::STATUS_COMPLETED:
            $badgeClass .= 'bg-label-success';
            $statusText = __('Completed');
            break;
        case \App\Models\LoanTransaction::STATUS_CANCELLED:
            $badgeClass .= 'bg-label-secondary';
            $statusText = __('Cancelled');
            break;
        default:
            $badgeClass .= 'bg-label-secondary';
            $statusText = Str::title(str_replace('_', ' ', $status)); // Fallback for unknown status
            break;
    }
@endphp

<span class="{{ $badgeClass }} {{ $class }}">{{ $statusText }}</span>
