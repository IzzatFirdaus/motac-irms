@props([
    'type' => 'info', // success, danger, warning, info
    'message' => null,
    'title' => null,
    'dismissible' => false
])

@php
    $alertClass = 'alert';
    $iconClass = '';

    switch ($type) {
        case 'success':
            $alertClass .= ' alert-success';
            $iconClass = 'ti ti-circle-check';
            $defaultTitle = __('Success!');
            break;
        case 'danger':
            $alertClass .= ' alert-danger';
            $iconClass = 'ti ti-alert-circle';
            $defaultTitle = __('Error!');
            break;
        case 'warning':
            $alertClass .= ' alert-warning';
            $iconClass = 'ti ti-alert-triangle';
            $defaultTitle = __('Warning!');
            break;
        case 'info':
        default:
            $alertClass .= ' alert-info';
            $iconClass = 'ti ti-info-circle';
            $defaultTitle = __('Information');
            break;
    }

    if ($dismissible) {
        $alertClass .= ' alert-dismissible fade show';
    }

    $alertTitle = $title ?? $defaultTitle;
@endphp

@if ($message || !$slot->isEmpty())
    <div {{ $attributes->merge(['class' => $alertClass]) }} role="alert">
        <div class="d-flex align-items-start">
            @if($iconClass)
                <div class="flex-shrink-0 me-2">
                    <i class="{{ $iconClass }} alert-icon"></i> {{-- Ensure .alert-icon has appropriate size if needed --}}
                </div>
            @endif
            <div class="flex-grow-1">
                @if($alertTitle)
                    <h5 class="alert-heading">{{ $alertTitle }}</h5>
                @endif
                @if ($message)
                    <p class="mb-0">{{ $message }}</p>
                @endif
                {{ $slot }}
            </div>
            @if ($dismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            @endif
        </div>
    </div>
@endif
