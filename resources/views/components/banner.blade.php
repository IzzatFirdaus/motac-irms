{{-- resources/views/components/banner.blade.php --}}
@props(['style' => session('flash.bannerStyle', 'success'), 'message' => session('flash.banner')])

<div x-data="{
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
        if (message) { // Simplified auto-hide for initial session message
            clearTimeout(timeout);
            timeout = setTimeout(() => show = false, 7000);
        }
     "
     style="display: none;"
     class="alert alert-dismissible fade show px-4 py-3 m-0 border-0 rounded-0" {{-- Ensure rounded-0 if it's a full-width top banner --}}
     :class="{
        'alert-success': style == 'success', /* Let MOTAC theme define text/bg for .alert-success */
        'alert-danger': style == 'danger',   /* Let MOTAC theme define text/bg for .alert-danger */
        'alert-warning': style == 'warning', /* Let MOTAC theme define text/bg for .alert-warning (usually dark text on yellow) */
        'alert-info': style == 'info' || (style !== 'success' && style !== 'danger' && style !== 'warning') /* Default, MOTAC themed */
        /* Original explicit classes (can be used if your theme doesn't override Bootstrap alerts sufficiently):
        'alert-success text-white bg-success': style == 'success',
        'alert-danger text-white bg-danger': style == 'danger',
        'alert-warning text-dark bg-warning': style == 'warning',
        'alert-info text-white bg-info': style == 'info' || (style !== 'success' && style !== 'danger' && style !== 'warning')
        */
     }"
     role="alert">

    <div class="d-flex align-items-center">
        {{-- Icon --}}
        <div class="flex-shrink-0 me-2">
            {{-- Using Bootstrap Icons as per Design Doc 2.4 --}}
            <template x-if="style == 'success'">
                <i class="bi bi-check-circle-fill fs-5"></i> {{-- fs-5 for ti-lg equivalent --}}
            </template>
            <template x-if="style == 'danger'">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            </template>
            <template x-if="style == 'warning'">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i> {{-- Often same icon for warning/danger, adjust if needed --}}
            </template>
            <template x-if="style !== 'success' && style !== 'danger' && style !== 'warning'">
                <i class="bi bi-info-circle-fill fs-5"></i>
            </template>
        </div>

        {{-- Message --}}
        <div class="flex-grow-1" x-text="message">
            {{-- Message is set by Alpine.js --}}
        </div>

        {{-- Dismiss Button --}}
        <div class="flex-shrink-0 ms-auto ps-3">
            <button type="button"
                    class="btn-close"
                    {{-- :class="{'btn-close-white': style == 'success' || style == 'danger' || style == 'info'}" --}} {{-- btn-close-white is applied by Bootstrap based on alert variant contrast --}}
                    aria-label="{{ __('Tutup') }}"
                    x-on:click="show = false; message = null; clearTimeout(timeout);"></button>
        </div>
    </div>
</div>
