{{-- components/alert.blade.php --}}
@props([
    'type' => 'info', // Can be: success, danger, warning, info
    'message' => null,
    'title' => null,
    'dismissible' => false // To make the alert dismissible
])

@php
    $alertClass = 'alert'; // Base Bootstrap alert class
    $iconClass = ''; // Icon class (e.g., Tabler Icons)

    // Determine Bootstrap alert type class and default icon/title
    switch ($type) {
        case 'success':
            $alertClass .= ' alert-success';
            $iconClass = 'ti ti-circle-check'; // Tabler Icon
            $defaultTitle = __('Success!');
            break;
        case 'danger':
            $alertClass .= ' alert-danger';
            $iconClass = 'ti ti-alert-circle'; // Tabler Icon
            $defaultTitle = __('Error!');
            break;
        case 'warning':
            $alertClass .= ' alert-warning';
            $iconClass = 'ti ti-alert-triangle'; // Tabler Icon
            $defaultTitle = __('Warning!');
            break;
        case 'info':
        default:
            $alertClass .= ' alert-info';
            $iconClass = 'ti ti-info-circle'; // Tabler Icon
            $defaultTitle = __('Information');
            break;
    }

    if ($dismissible) {
        $alertClass .= ' alert-dismissible fade show'; // Classes for dismissible alerts
    }

    $alertTitle = $title ?? $defaultTitle;
@endphp

@if ($message || !$slot->isEmpty())
    <div {{ $attributes->merge(['class' => $alertClass]) }} role="alert">
        <div class="d-flex align-items-start"> {{-- Bootstrap flex for icon and content alignment --}}
            @if($iconClass)
                <div class="flex-shrink-0 me-2"> {{-- Margin for spacing icon --}}
                    <i class="{{ $iconClass }} alert-icon"></i> {{-- Apply .alert-icon class for custom sizing if needed --}}
                </div>
            @endif
            <div class="flex-grow-1">
                @if($alertTitle)
                    <h5 class="alert-heading">{{ $alertTitle }}</h5> {{-- Bootstrap alert heading --}}
                @endif
                @if ($message)
                    <p class="mb-0">{{ $message }}</p>
                @endif
                {{ $slot }} {{-- Allows passing content into the component --}}
            </div>
            @if ($dismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Close') }}"></button>
            @endif
        </div>
    </div>
@endif
