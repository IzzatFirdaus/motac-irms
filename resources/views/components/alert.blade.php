{{--
    resources/views/components/alert.blade.php

    A flexible alert component supporting multiple types (success, danger, warning, info).
    Automatically handles icons, dismissibility, and validation errors display.

    Props:
    - $type: string - Alert type: 'success', 'danger', 'warning', 'info' (default: 'info')
    - $message: string - Main message text (optional)
    - $title: string - Custom title, overrides default (optional)
    - $dismissible: bool - Whether alert can be dismissed (auto-set based on type)
    - $icon: string - Custom icon class, overrides default (optional)
    - $errors: MessageBag - Validation errors to display (optional)

    Usage:
    <x-alert type="success" :message="__('Operation completed successfully!')" />
    <x-alert type="danger" :errors="$errors" />

    Dependencies: Bootstrap 5, Bootstrap Icons
--}}
@props([
    'type' => 'info',
    'message' => null,
    'title' => null,
    'dismissible' => null,
    'icon' => null,
    'errors' => null
])

@php
    // Initialize alert configuration
    $alertClass = 'alert';
    $iconClassProvided = $icon;
    $defaultIconClass = '';
    $defaultTitle = '';
    $isDismissible = $dismissible;

    // Configure alert based on type
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

    // Add dismissible classes if needed
    if ($isDismissible) {
        $alertClass .= ' alert-dismissible fade show';
    }

    // Determine final values
    $alertTitle = $title ?? $defaultTitle;
    $currentIconClass = $iconClassProvided ?? $defaultIconClass;
    $hasContent = $message || !$slot->isEmpty() || ($errors && $errors->any());
@endphp

@if ($hasContent)
    <div {{ $attributes->merge(['class' => $alertClass]) }} role="alert">
        <div class="d-flex align-items-start">
            {{-- Alert Icon --}}
            @if($currentIconClass)
                <div class="flex-shrink-0 me-2">
                    <i class="bi {{ $currentIconClass }} fs-5"></i>
                </div>
            @endif

            <div class="flex-grow-1">
                {{-- Alert Title --}}
                @if($alertTitle)
                    <h5 class="alert-heading h6 fw-semibold">{{ $alertTitle }}</h5>
                @endif

                {{-- Main Message --}}
                @if ($message)
                    <div @class(['small', 'mb-2' => ($errors && $errors->any()) || !$slot->isEmpty()])>{{ $message }}</div>
                @endif

                {{-- Validation Errors List --}}
                @if ($errors && $errors->any())
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif

                {{-- Additional Content Slot --}}
                @if (!$slot->isEmpty())
                    {{ $slot }}
                @endif
            </div>

            {{-- Dismiss Button --}}
            @if ($isDismissible)
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
            @endif
        </div>
    </div>
@endif
