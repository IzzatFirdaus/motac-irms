{{-- resources/views/components/banner.blade.php --}}
{{-- A banner component for displaying flash messages, typically at the top of the page. --}}
{{-- System Design: "The Big Picture" mentions <x-banner /> for flash messages. --}}
{{-- Design Language: Clear Instructions & Actionable Feedback --}}

@props(['style' => session('flash.bannerStyle', 'success'), 'message' => session('flash.banner')])

<div x-data="{
        show: true,
        style: @js($style),
        message: @js($message)
     }"
     x-show="show && message"
     x-init="
        Livewire.on('banner-message', event => {
            style = event.detail.style || 'info'; // Default to info if not provided
            message = event.detail.message;
            show = true;
            setTimeout(() => show = false, event.detail.timeout || 7000); // Auto-hide after 7 seconds
        });
        // Auto-hide session-based banner after 7 seconds
        if (message) {
            setTimeout(() => show = false, 7000);
        }
     "
     style="display: none;" {{-- Initially hidden, shown by Alpine.js --}}
     class="alert alert-dismissible fade show px-4 py-3 m-0 border-0"
     :class="{
        'alert-success text-white bg-success': style == 'success',
        'alert-danger text-white bg-danger': style == 'danger',
        'alert-warning text-dark bg-warning': style == 'warning',
        'alert-info text-white bg-info': style == 'info' || (style != 'success' && style != 'danger' && style != 'warning')
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
            <template x-if="style != 'success' && style != 'danger' && style != 'warning'">
                <i class="ti ti-info-circle ti-lg"></i>
            </template>
        </div>

        {{-- Message --}}
        <div class="flex-grow-1" x-text="message">
            {{-- Message is set by Alpine.js --}}
        </div>

        {{-- Dismiss Button --}}
        <div class="flex-shrink-0 ms-3">
            <button type="button"
                    class="btn-close"
                    :class="{'btn-close-white': style == 'success' || style == 'danger' || style == 'info'}"
                    aria-label="{{ __('Tutup') }}"
                    x-on:click="show = false"></button>
        </div>
    </div>
</div>
