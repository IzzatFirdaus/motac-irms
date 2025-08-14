{{--
    resources/views/components/banner.blade.php

    MYDS-compliant flash message banner component.
    - Supports success, danger, warning, info (semantic status)
    - Auto-dismiss after 7s
    - Responsive, accessible, ARIA roles, keyboard navigation
    - Follows MyGOVEA principles: clear feedback, minimal, accessible, citizen-centric
    - Uses MYDS color tokens, grid, icon system, focus ring

    Props:
    - $style: string - Banner style: 'success', 'danger', 'warning', 'info' (from session)
    - $message: string - Banner message content (from session)

    Usage:
    <x-banner />

    Features:
    - Auto-dismiss after 7 seconds
    - Livewire event integration
    - Multiple alert styles with appropriate icons
    - Responsive design
    - ARIA role="alert" for accessibility

    Dependencies: Alpine.js, Livewire, MYDS CSS/JS
--}}

@props([
    // Use Blade's global $flash variable or pass props from parent, fallback to empty string if not set
    'style' => isset($flash['bannerStyle']) ? $flash['bannerStyle'] : (isset($bannerStyle) ? $bannerStyle : 'success'),
    'message' => isset($flash['banner']) ? $flash['banner'] : (isset($banner) ? $banner : ''),
])

@php
    // Map style to MYDS tokens and icons
    $bannerConfig = [
        'success' => [
            'bg' => 'myds-bg-success-100',
            'txt' => 'myds-txt-success',
            'icon' => 'bi-check-circle-fill',
            'aria' => 'Berjaya',
        ],
        'danger' => [
            'bg' => 'myds-bg-danger-100',
            'txt' => 'myds-txt-danger',
            'icon' => 'bi-exclamation-triangle-fill',
            'aria' => 'Ralat',
        ],
        'warning' => [
            'bg' => 'myds-bg-warning-100',
            'txt' => 'myds-txt-warning',
            'icon' => 'bi-exclamation-triangle-fill',
            'aria' => 'Amaran',
        ],
        'info' => [
            'bg' => 'myds-bg-primary-100',
            'txt' => 'myds-txt-primary',
            'icon' => 'bi-info-circle-fill',
            'aria' => 'Makluman',
        ],
    ];
    // Fallback for unknown style
    $cfg = $bannerConfig[$style] ?? $bannerConfig['info'];
@endphp

{{-- Alpine.js for show/hide and auto-dismiss --}}
<div
    x-data="{
        show: true,
        style: @js($style),
        message: @js($message),
        timeout: null
     }"
     x-show="show && message"
     x-init="
        Livewire.on('banner-message', detail => {
            style = detail.style || 'info';
            message = detail.message;
            show = true;
            clearTimeout(timeout);
            timeout = setTimeout(() => show = false, detail.timeout || 7000);
        });
        if (message) {
            clearTimeout(timeout);
            timeout = setTimeout(() => show = false, 7000);
        }
     "
     style="display: none;"
     class="myds-banner alert px-4 py-3 m-0 border-0 rounded-0 {{ $cfg['bg'] }} {{ $cfg['txt'] }} myds-shadow-card"
     role="alert"
     aria-live="polite"
     aria-atomic="true"
     tabindex="0"
>
    <div class="d-flex align-items-center" style="gap: 12px;">
        {{-- Dynamic Icon Based on Style --}}
        <div class="flex-shrink-0" aria-hidden="true">
            <i class="bi {{ $cfg['icon'] }} fs-5" aria-label="{{ $cfg['aria'] }}"></i>
        </div>

        {{-- Message Content --}}
        <div class="flex-grow-1 small fw-medium" x-text="message"></div>

        {{-- Close Button --}}
        <div class="flex-shrink-0 ms-auto ps-3">
            <button type="button"
                    class="btn myds-btn-tertiary"
                    aria-label="Tutup"
                    x-on:click="show = false; message = null; clearTimeout(timeout);"
                    tabindex="0"
            >
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</div>

{{--
    === MYDS & MyGOVEA Compliance Notes ===
    - Uses MYDS color tokens for background/text, semantic status, and icon system for clarity (Principle #7, #13)
    - ARIA roles for accessibility (Principle #1, #6, #15, #18)
    - Minimal, clear presentation, auto-dismiss, keyboard focusable (Principle #5, #17)
    - Auto-dismiss and Livewire event integration for feedback (Principle #11)
    - Responsive padding/gap for mobile/tablet/desktop (Principle #10)
    - Button follows MYDS tertiary variant for non-intrusive close (Principle #16)
--}}
