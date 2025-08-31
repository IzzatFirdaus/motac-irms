{{--
    resources/views/components/button.blade.php

    MYDS & MyGOVEA Compliant Primary Button Component
    Follows MYDS design system and MyGOVEA citizen-centric principles

    Features:
    - MYDS color tokens and typography
    - WCAG 2.1 accessibility (minimum 48px touch target)
    - MyGOVEA minimalist design principles
    - Consistent with government standards

    Usage:
    <x-button>{{ __('Simpan') }}</x-button>
    <x-button type="button" class="btn-lg">{{ __('Butang Besar') }}</x-button>
    <x-button variant="secondary">{{ __('Batal') }}</x-button>

    Dependencies: Bootstrap 5, MYDS CSS tokens
--}}

@php
    $variant = $attributes->get('variant', 'primary');
    $size = $attributes->get('size', 'medium');

    // MYDS button classes based on variant
    $baseClasses = 'btn mygovea-accessible fw-medium';

    $variantClasses = match($variant) {
        'primary' => 'btn-myds-primary',
        'secondary' => 'btn-outline-primary',
        'success' => 'btn-success',
        'danger' => 'btn-danger',
        'warning' => 'btn-warning',
        'info' => 'btn-info',
        default => 'btn-myds-primary'
    };

    $sizeClasses = match($size) {
        'small' => 'btn-sm',
        'large' => 'btn-lg',
        default => ''
    };

    $finalClasses = trim("{$baseClasses} {$variantClasses} {$sizeClasses}");
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => $finalClasses,
    'style' => 'min-height: 48px; border-radius: 8px;'
]) }}>
    {{ $slot }}
</button>

<style>
    /* MYDS Button Styles */
    .btn-myds-primary {
        background-color: var(--myds-primary, #2563EB);
        border-color: var(--myds-primary, #2563EB);
        color: #ffffff;
        font-family: var(--myds-font-body);
        font-weight: 500;
        padding: 12px 24px;
        border-radius: 8px;
        transition: all 0.2s ease;
        min-height: 48px; /* WCAG touch target */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-myds-primary:hover,
    .btn-myds-primary:focus {
        background-color: #1d4ed8;
        border-color: #1d4ed8;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .btn-myds-primary:active {
        background-color: #1e40af;
        border-color: #1e40af;
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
    }

    .btn-myds-primary:disabled,
    .btn-myds-primary.disabled {
        background-color: var(--myds-gray-300);
        border-color: var(--myds-gray-300);
        color: var(--myds-gray-500);
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    /* Secondary button improvements */
    .btn-outline-primary {
        border-color: var(--myds-primary, #2563EB);
        color: var(--myds-primary, #2563EB);
        background-color: transparent;
        min-height: 48px;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        background-color: var(--myds-primary, #2563EB);
        border-color: var(--myds-primary, #2563EB);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    /* Size variations */
    .btn-sm {
        min-height: 40px;
        padding: 8px 16px;
        font-size: 0.875rem;
    }

    .btn-lg {
        min-height: 56px;
        padding: 16px 32px;
        font-size: 1.125rem;
    }

    /* Dark mode adjustments */
    [data-bs-theme="dark"] .btn-outline-primary {
        border-color: #60a5fa;
        color: #60a5fa;
    }

    [data-bs-theme="dark"] .btn-outline-primary:hover,
    [data-bs-theme="dark"] .btn-outline-primary:focus {
        background-color: #60a5fa;
        border-color: #60a5fa;
        color: var(--myds-gray-900);
    }
</style>
