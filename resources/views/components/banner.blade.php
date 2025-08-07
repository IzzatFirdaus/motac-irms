{{--
    resources/views/components/banner.blade.php

    Flash message banner component with auto-dismiss functionality.
    Supports multiple styles and integrates with Livewire events.

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

    Dependencies: Alpine.js, Bootstrap 5, Livewire
--}}
@props(['style' => session('flash.bannerStyle', 'success'), 'message' => session('flash.banner')])

<div x-data="{
        show: true,
        style: @js($style),
        message: @js($message),
        timeout: null
     }"
     x-show="show && message"
     x-init="
        // Listen for Livewire banner events
        Livewire.on('banner-message', detail => {
            style = detail.style || 'info';
            message = detail.message;
            show = true;
            clearTimeout(timeout);
            timeout = setTimeout(() => show = false, detail.timeout || 7000);
        });

        // Auto-hide existing message
        if (message) {
            clearTimeout(timeout);
            timeout = setTimeout(() => show = false, 7000);
        }
     "
     style="display: none;"
     class="alert alert-dismissible fade show px-4 py-3 m-0 border-0 rounded-0"
     :class="{
        'alert-success': style == 'success',
        'alert-danger': style == 'danger',
        'alert-warning': style == 'warning',
        'alert-info': style == 'info' || (style !== 'success' && style !== 'danger' && style !== 'warning')
     }"
     role="alert">

    <div class="d-flex align-items-center">
        {{-- Dynamic Icon Based on Style --}}
        <div class="flex-shrink-0 me-2">
            <template x-if="style == 'success'">
                <i class="bi bi-check-circle-fill fs-5"></i>
            </template>
            <template x-if="style == 'danger'">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </template>
            <template x-if="style == 'warning'">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </template>
            <template x-if="style !== 'success' && style !== 'danger' && style !== 'warning'">
                <i class="bi bi-info-circle-fill fs-5"></i>
            </template>
        </div>

        {{-- Message Content --}}
        <div class="flex-grow-1" x-text="message"></div>

        {{-- Close Button --}}
        <div class="flex-shrink-0 ms-auto ps-3">
            <button type="button" class="btn-close"
                    aria-label="{{ __('Tutup') }}"
                    x-on:click="show = false; message = null; clearTimeout(timeout);"></button>
        </div>
    </div>
</div>
