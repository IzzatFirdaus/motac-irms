{{-- resources/views/components/alert.blade.php --}}
@props([
    'type' => 'info',
    'message' => null,
    'title' => null,
    'dismissible' => null,
    'icon' => null,
    'errors' => null // Crucial: This must be present
])

@php
    $alertClass = 'alert';
    $iconClassProvided = $icon;
    $defaultIconClass = '';
    $defaultTitle = '';
    $isDismissible = $dismissible;

    switch ($type) {
        case 'success':
            $alertClass .= ' alert-success';
            $defaultIconClass = 'bi-check-circle-fill';
            $defaultTitle = __('Berjaya!');
            if (is_null($isDismissible)) { $isDismissible = true; }
            break;
        case 'danger':
            $alertClass .= ' alert-danger';
            $defaultIconClass = 'bi-exclamation-triangle-fill';
            $defaultTitle = __('Ralat!');
            if (is_null($isDismissible)) { $isDismissible = true; }
            break;
        case 'warning':
            $alertClass .= ' alert-warning';
            $defaultIconClass = 'bi-exclamation-triangle-fill';
            $defaultTitle = __('Amaran!');
            if (is_null($isDismissible)) { $isDismissible = true; }
            break;
        case 'info':
        default:
            $alertClass .= ' alert-info';
            $defaultIconClass = 'bi-info-circle-fill';
            $defaultTitle = __('Makluman');
            $type = 'info';
            if (is_null($isDismissible)) { $isDismissible = false; }
            break;
    }

    if ($isDismissible) {
        $alertClass .= ' alert-dismissible fade show';
    }

    $alertTitle = $title ?? $defaultTitle;
    $currentIconClass = $iconClassProvided ?? $defaultIconClass;

    $hasContent = $message || !$slot->isEmpty() || ($errors && $errors->any()); // Crucial: This must check $errors
@endphp

@if ($hasContent)
    <div {{ $attributes->merge(['class' => $alertClass]) }} role="alert">
        <div class="d-flex align-items-start">
            @if($currentIconClass)
                <div class="flex-shrink-0 me-2">
                    <i class="bi {{ $currentIconClass }} fs-5"></i>
                </div>
            @endif
            <div class="flex-grow-1">
                @if($alertTitle)
                    <h5 class="alert-heading h6 fw-semibold">{{ $alertTitle }}</h5>
                @endif

                @if ($message)
                    <div @class(['small', 'mb-2' => ($errors && $errors->any()) || !$slot->isEmpty()])>{{ $message }}</div>
                @endif

                {{-- This block is crucial for displaying validation errors --}}
                @if ($errors && $errors->any())
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                @if (!$slot->isEmpty())
                    {{ $slot }}
                @endif
            </div>
            @if ($isDismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            @endif
        </div>
    </div>
@endif
