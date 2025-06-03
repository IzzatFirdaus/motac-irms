{{-- resources/views/components/alert.blade.php --}}
@props([
    'type' => 'info', // Can be: success, danger, warning, info, primary, secondary, light, dark
    'message' => null,
    'title' => null,
    'dismissible' => false,
    'icon' => null // Allow passing custom icon class
])

@php
    $alertClass = 'alert'; // Base Bootstrap alert class
    $iconClassProvided = $icon;
    $defaultIconClass = '';
    $defaultTitle = '';

    switch ($type) {
        case 'success':
            $alertClass .= ' alert-success';
            $defaultIconClass = 'bi-check-circle-fill'; // Bootstrap Icon
            $defaultTitle = __('Berjaya!');
            break;
        case 'danger':
            $alertClass .= ' alert-danger';
            $defaultIconClass = 'bi-exclamation-triangle-fill'; // Bootstrap Icon
            $defaultTitle = __('Ralat!');
            break;
        case 'warning':
            $alertClass .= ' alert-warning';
            $defaultIconClass = 'bi-exclamation-triangle-fill'; // Bootstrap Icon (often same as danger)
            $defaultTitle = __('Amaran!');
            break;
        case 'info':
        default: // Default to info
            $alertClass .= ' alert-info';
            $defaultIconClass = 'bi-info-circle-fill'; // Bootstrap Icon
            $defaultTitle = __('Makluman');
            $type = 'info'; // Ensure type is set for default case
            break;
        // You can add more cases for other Bootstrap alert types if needed
        // case 'primary': $alertClass .= ' alert-primary'; $defaultIconClass = 'bi-bell-fill'; $defaultTitle = __('Notis Utama'); break;
        // case 'secondary': $alertClass .= ' alert-secondary'; $defaultIconClass = 'bi-gear-fill'; $defaultTitle = __('Notis Sekunder'); break;
    }

    if ($dismissible) {
        $alertClass .= ' alert-dismissible fade show';
    }

    $alertTitle = $title ?? $defaultTitle;
    $currentIconClass = $iconClassProvided ?? $defaultIconClass;
@endphp

@if ($message || !$slot->isEmpty())
    <div {{ $attributes->merge(['class' => $alertClass]) }} role="alert">
        <div class="d-flex align-items-start">
            @if($currentIconClass)
                <div class="flex-shrink-0 me-2">
                    {{-- Icon size can be controlled with fs-* classes if needed --}}
                    <i class="bi {{ $currentIconClass }} fs-5"></i> {{-- Using fs-5 for slightly larger icon --}}
                </div>
            @endif
            <div class="flex-grow-1">
                @if($alertTitle)
                    <h5 class="alert-heading h6 fw-semibold">{{ $alertTitle }}</h5> {{-- Using h6 for alert title --}}
                @endif
                @if ($message)
                    <div class="small">{{ $message }}</div> {{-- Wrapped message in a div with small class --}}
                @endif
                {{ $slot }}
            </div>
            @if ($dismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            @endif
        </div>
    </div>
@endif
