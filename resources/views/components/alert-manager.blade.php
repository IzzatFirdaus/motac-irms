<<<<<<< HEAD
{{--
    MYDS-compliant Alert Component
    resources/views/components/alert.blade.php

    - Supports: status types (success, danger, warning, info)
    - Accessible: ARIA role, semantic icons, focusable close
    - Uses MYDS color tokens, spacing, motion tokens
    - Complies with MyGOVEA Principle 5, 6, 7, 8, 13, 14, 17 (Minimal, Consistent, Clear, Realistic, UI/UX, Typography, Error Prevention)
--}}

@props([
    'type' => 'info',           // Alert type: success, danger, warning, info
    'message' => null,          // Main message text
    'title' => null,            // Custom title override
    'dismissible' => null,      // Dismissibility
    'icon' => null,             // Custom icon class
    'errors' => null            // Validation errors to display
])

@php
    // MYDS status color tokens
    $mydsTypeMap = [
        'success' => [
            'bg' => 'bg-success-50',
            'border' => 'otl-success-200',
            'icon' => 'bi-check-circle-fill',
            'txt' => 'txt-success',
            'defaultTitle' => 'Berjaya!'
        ],
        'danger' => [
            'bg' => 'bg-danger-50',
            'border' => 'otl-danger-200',
            'icon' => 'bi-exclamation-triangle-fill',
            'txt' => 'txt-danger',
            'defaultTitle' => 'Ralat!'
        ],
        'warning' => [
            'bg' => 'bg-warning-50',
            'border' => 'otl-warning-200',
            'icon' => 'bi-exclamation-triangle-fill',
            'txt' => 'txt-warning',
            'defaultTitle' => 'Amaran!'
        ],
        'info' => [
            'bg' => 'bg-primary-50',
            'border' => 'otl-primary-200',
            'icon' => 'bi-info-circle-fill',
            'txt' => 'txt-primary',
            'defaultTitle' => 'Makluman'
        ],
    ];

    $mapped = $mydsTypeMap[$type] ?? $mydsTypeMap['info'];
    $isDismissible = $dismissible ?? ($type !== 'info');
    $alertTitle = $title ?? $mapped['defaultTitle'];
    $currentIconClass = $icon ?? $mapped['icon'];
    $hasContent = $message || !$slot->isEmpty() || ($errors && $errors->any());
@endphp

@if ($hasContent)
    <div
        {{ $attributes->merge([
            'class' => "myds-alert shadow-card {$mapped['bg']} border-start border-4 {$mapped['border']} px-4 py-3 mb-3 rounded-lg d-flex align-items-start position-relative animate__animated animate__fadeIn",
            'role' => 'alert',
            'style' => 'transition: 400ms cubic-bezier(0.4, 1.4, 0.2, 1);'
        ]) }}
        aria-live="polite"
        tabindex="0"
    >
        {{-- Leading Icon (uses MYDS semantic icon and color) --}}
        @if($currentIconClass)
            <span class="flex-shrink-0 me-3" aria-hidden="true">
                <i class="bi {{ $currentIconClass }} fs-4 {{ $mapped['txt'] }}"></i>
            </span>
        @endif

        {{-- Alert Content --}}
        <div class="flex-grow-1">
            {{-- Alert Title --}}
            @if($alertTitle)
                <h5 class="alert-heading h6 fw-semibold mb-1 {{ $mapped['txt'] }} font-poppins">
                    {{ $alertTitle }}
                </h5>
            @endif

            {{-- Main Message --}}
            @if ($message)
                <div class="small mb-2 font-inter" style="color:var(--myds-txt-black-900)">
                    {{ $message }}
                </div>
            @endif

            {{-- Validation Errors List --}}
            @if ($errors && $errors->any())
                <ul class="mb-0 ps-3 small font-inter" aria-label="Senarai Ralat">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            {{-- Additional Content Slot --}}
            @if (!$slot->isEmpty())
                <div class="font-inter mt-2">
                    {{ $slot }}
                </div>
            @endif
        </div>

        {{-- Dismiss Button (if dismissible) --}}
        @if ($isDismissible)
            <button type="button"
                class="btn-close ms-3 mt-1"
                data-bs-dismiss="alert"
                aria-label="Tutup"
                tabindex="0"
                style="outline: 2px solid transparent; outline-offset: 2px;"
            ></button>
        @endif
    </div>
@endif

{{--
    MYDS Alert Anatomy:
    - Uses semantic colour tokens for background, border, text, and icon.
    - Shadow and radius match standard MYDS card.
    - ARIA role="alert", aria-live="polite" for accessibility.
    - Dismiss button is focusable for keyboard users.
    - Error list is rendered with ARIA label for screen readers.
    - Animate.css fadeIn used for motion (MYDS easeoutback.medium).
    - Typography: Poppins for heading, Inter for body, per MYDS spec.
    - Compliant with MyGOVEA Principles: Citizen-centric, Minimal, Consistent, Clear, Error Prevention, Accessibility.
--}}
=======
@props(['type' => 'info', 'message' => null, 'title' => null])

@php
    $baseClass = 'alert p-4 mb-4 border rounded-md text-sm';
    $typeClasses = [
        'success' => 'bg-green-100 dark:bg-green-800 border-green-300 dark:border-green-600 text-green-700 dark:text-green-200',
        'danger' => 'bg-red-100 dark:bg-red-800 border-red-300 dark:border-red-600 text-red-700 dark:text-red-200',
        'warning' => 'bg-yellow-100 dark:bg-yellow-800 border-yellow-300 dark:border-yellow-600 text-yellow-700 dark:text-yellow-200',
        'info' => 'bg-blue-100 dark:bg-blue-800 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-200',
    ];
    $iconClasses = [
        'success' => 'ti ti-circle-check text-green-500 dark:text-green-400', // Tabler Icon example
        'danger' => 'ti ti-alert-circle text-red-500 dark:text-red-400',
        'warning' => 'ti ti-alert-triangle text-yellow-500 dark:text-yellow-400',
        'info' => 'ti ti-info-circle text-blue-500 dark:text-blue-400',
    ];
    $containerClass = $baseClass . ' ' . ($typeClasses[$type] ?? $typeClasses['info']);
    $currentIconClass = $iconClasses[$type] ?? $iconClasses['info'];

    $alertTitle = $title ?? ucfirst($type) . '!';
@endphp

@if ($message || !$slot->isEmpty())
    <div {{ $attributes->merge(['class' => $containerClass]) }} role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="{{ $currentIconClass }} h-5 w-5"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium">{{ $alertTitle }}</h3>
                <div class="mt-2 text-sm">
                    @if ($message)
                        <p>{{ $message }}</p>
                    @endif
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
@endif
>>>>>>> 7940bed (feat: Standardize authorization policies, update service provider and models, and refine configuration for consistent role management and grade-based approvals; Refactor: Streamline notification system with generic classes and consolidations)
