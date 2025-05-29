{{-- resources/views/components/banner.blade.php --}}
{{-- A banner component for displaying flash messages, typically at the top of the page. --}}
{{-- System Design: "The Big Picture" mentions <x-banner /> for flash messages. (Comment from original file) --}}
{{-- Design Language: Clear Instructions & Actionable Feedback --}}

@props(['style' => session('flash.bannerStyle', 'success'), 'message' => session('flash.banner')])

<div x-data="{
        show: true,
        style: @js($style),
        message: @js($message),
        timeout: null
     }"
     x-show="show && message"
     x-init="
        Livewire.on('banner-message', detail => { // Changed event.detail to detail for clarity
            style = detail.style || 'info';
            message = detail.message;
            show = true;
            clearTimeout(timeout); // Clear previous timeout if any
            timeout = setTimeout(() => show = false, detail.timeout || 7000);
        });
        if (message && !Livewire.firstLoad) { // Only run auto-hide for session messages after initial load if displayed by Livewire
            clearTimeout(timeout);
            timeout = setTimeout(() => show = false, 7000);
        } else if (message) { // For initial session message on page load
             clearTimeout(timeout);
             timeout = setTimeout(() => show = false, 7000);
        }
     "
     style="display: none;" {{-- Initially hidden, shown by Alpine.js --}}
     class="alert alert-dismissible fade show px-4 py-3 m-0 border-0 rounded-0" {{-- Added rounded-0 if it's a top banner --}}
     :class="{
        'alert-success text-white bg-success': style == 'success',
        'alert-danger text-white bg-danger': style == 'danger',
        'alert-warning text-dark bg-warning': style == 'warning', // text-dark for better contrast on yellow
        'alert-info text-white bg-info': style == 'info' || (style !== 'success' && style !== 'danger' && style !== 'warning')
     }"
     role="alert">

    <div class="d-flex align-items-center">
        {{-- Icon --}}
        <div class="flex-shrink-0 me-2">
            <template x-if="style == 'success'">
                <i class="ti ti-circle-check ti-lg"></i>
            </template>
            <template x-if="style == 'danger'">
                <i class="ti ti-alert-triangle ti-lg"></i>
            </template>
            <template x-if="style == 'warning'">
                <i class="ti ti-alert-hexagon ti-lg"></i>
            </template>
            <template x-if="style !== 'success' && style !== 'danger' && style !== 'warning'"> {{-- Simplified condition --}}
                <i class="ti ti-info-circle ti-lg"></i>
            </template>
        </div>

        {{-- Message --}}
        <div class="flex-grow-1" x-text="message">
            {{-- Message is set by Alpine.js --}}
        </div>

        {{-- Dismiss Button --}}
        <div class="flex-shrink-0 ms-auto ps-3"> {{-- ms-auto and ps-3 for spacing --}}
            <button type="button"
                    class="btn-close"
                    :class="{'btn-close-white': style == 'success' || style == 'danger' || style == 'info'}"
                    aria-label="{{ __('Tutup') }}"
                    x-on:click="show = false; message = null; clearTimeout(timeout);"></button> {{-- Clear message and timeout on manual close --}}
        </div>
    </div>
</div>
