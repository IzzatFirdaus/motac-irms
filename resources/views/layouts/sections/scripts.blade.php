{{-- resources/views/layouts/sections/scripts.blade.php --}}
{{-- Loads global JavaScript files and initializes common scripts. --}}
{{-- System Design: Phase 5 (Global JavaScript Execution) --}}

@php
    // $configData is globally available, sourced from Helpers::appClasses() in commonMaster.blade.php.
    $textDirection = $configData['textDirection'] ?? 'ltr'; // 'ltr' or 'rtl'
    $assetsPath = rtrim($configData['assetsPath'] ?? asset('assets/'), '/') . '/'; // Ensure single trailing slash
@endphp

{{-- Core Vendor JS --}}
<script src="{{ $assetsPath }}vendor/libs/jquery/jquery.js"></script> {{-- Keep if main.js or other template scripts depend on jQuery --}}
<script src="{{ $assetsPath }}vendor/libs/popper/popper.js"></script>
<script src="{{ $assetsPath }}vendor/js/bootstrap.js"></script> {{-- Bootstrap 5 JS Bundle --}}
<script src="{{ $assetsPath }}vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ $assetsPath }}vendor/libs/node-waves/node-waves.js"></script> {{-- For ripple effects --}}
<script src="{{ $assetsPath }}vendor/libs/hammer/hammer.js"></script> {{-- For touch gestures, if menu swipe is used --}}
<script src="{{ $assetsPath }}vendor/libs/typeahead-js/typeahead.bundle.js"></script> {{-- For search suggestions if used --}}
<script src="{{ $assetsPath }}vendor/js/menu.js"></script> {{-- Core theme menu logic --}}
<script src="{{ $assetsPath }}vendor/libs/toastr/toastr.js"></script> {{-- For notifications --}}

{{-- Page specific vendor JS --}}
@yield('vendor-script')

{{-- Main Application JS --}}
<script src="{{ $assetsPath }}js/main.js"></script>

{{-- Inline JS for initializations and Livewire event listeners --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Toastr Notifications
        // Design Language: Clear Instructions & Actionable Feedback
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right", // Consistent position
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "7000", // Slightly longer for readability
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "rtl": document.documentElement.getAttribute('dir') === 'rtl' // Dynamically set RTL for Toastr
            };

            // Listen for Livewire dispatched 'toastr' events
            // System Design 9.5 (Notifications via various channels)
            window.addEventListener('toastr', event => {
                if (event.detail && typeof event.detail.type === 'string' && typeof event.detail.message === 'string') {
                    toastr[event.detail.type](event.detail.message, event.detail.title ?? '');
                } else {
                    console.warn('Toastr event received with invalid details:', event.detail);
                }
            });
            // Example: Livewire component: $this->dispatch('toastr', ['type' => 'success', 'message' => 'Berjaya!']);
        } else {
            console.warn('Toastr library (toastr.js) not found or not initialized.');
        }

        // Livewire Modal/Offcanvas Listeners (using Bootstrap 5 JS API)
        // System Design implies dynamic UI components might use modals/offcanvas.
        const getBootstrapInstance = (elementId, constructor) => {
            const element = document.querySelector(elementId);
            return element ? constructor.getInstance(element) || new constructor(element) : null;
        };

        window.addEventListener('openModal', event => {
            if (event.detail?.elementId) {
                const modalInstance = getBootstrapInstance(event.detail.elementId, bootstrap.Modal);
                modalInstance?.show();
            }
        });
        window.addEventListener('closeModal', event => {
            if (event.detail?.elementId) {
                const modalInstance = getBootstrapInstance(event.detail.elementId, bootstrap.Modal);
                modalInstance?.hide();
            }
        });

        window.addEventListener('openCanvas', event => {
            if (event.detail?.elementId) {
                const canvasInstance = getBootstrapInstance(event.detail.elementId, bootstrap.Offcanvas);
                canvasInstance?.show();
            }
        });
        window.addEventListener('closeCanvas', event => {
            if (event.detail?.elementId) {
                const canvasInstance = getBootstrapInstance(event.detail.elementId, bootstrap.Offcanvas);
                canvasInstance?.hide();
            }
        });

        // Sound Event Listeners (optional, based on system requirements)
        const playSound = (soundName) => {
            try {
                // Ensure assetsPath has a trailing slash. Sound files in 'public/assets/sound/'
                new Audio("{{ $assetsPath }}sound/" + soundName + ".mp3").play().catch(e => console.warn(`Could not play sound ${soundName}:`, e));
            } catch (e) {
                console.warn(`Error initializing Audio object for sound ${soundName}:`, e);
            }
        };

        window.addEventListener('playMessageSound', () => playSound('message')); // e.g., assets/sound/message.mp3
        window.addEventListener('playNotificationSound', () => playSound('notification'));
        window.addEventListener('playErrorSound', () => playSound('error'));

        // ScrollToTop Event Listener
        window.addEventListener('scrollToTop', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>

{{-- Stack for pricing modal script if used (likely not for MOTAC internal system) --}}
{{-- @stack('pricing-script') --}}

{{-- Page specific inline JS --}}
@yield('page-script')

{{-- Custom JS pushed from Blade views --}}
@stack('custom-scripts')

{{-- Livewire Scripts --}}
@livewireScripts
