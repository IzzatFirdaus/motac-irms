{{-- resources/views/layouts/sections/scripts.blade.php --}}

{{--
    $configData is globally available here, sourced from Helpers::appClasses() in commonMaster.blade.php.
--}}
@php
    $textDirection = $configData['textDirection'] ?? 'ltr';
    $assetsPath = $configData['assetsPath'] ?? asset('/assets/') . ''; // Ensure trailing slash consistency if needed by main.js
@endphp

<script src="{{ $assetsPath }}vendor/libs/jquery/jquery.js"></script> {{-- Keep if main.js or other scripts depend on it --}}
<script src="{{ $assetsPath }}vendor/libs/popper/popper.js"></script>
<script src="{{ $assetsPath }}vendor/js/bootstrap.js"></script>
<script src="{{ $assetsPath }}vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ $assetsPath }}vendor/libs/node-waves/node-waves.js"></script>
<script src="{{ $assetsPath }}vendor/libs/hammer/hammer.js"></script> {{-- For touch gestures, if used by the theme --}}
<script src="{{ $assetsPath }}vendor/libs/typeahead-js/typeahead.bundle.js"></script> {{-- Usually bundle includes Bloodhound --}}
<script src="{{ $assetsPath }}vendor/js/menu.js"></script>
<script src="{{ $assetsPath }}vendor/libs/toastr/toastr.js"></script>

@yield('vendor-script')

<script src="{{ $assetsPath }}js/main.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true, // Often preferred
                "debug": false,
                "newestOnTop": true, // Often preferred
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true, // Often preferred
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
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
                if (event.detail && event.detail.type && event.detail.message) {
                    toastr[event.detail.type](event.detail.message, event.detail.title ?? '');
                } else {
                    console.warn('Toastr event received with missing details:', event.detail);
                }
            });
        } else {
            console.warn('Toastr library not found.');
        }

        // Livewire Modal/Offcanvas Listeners (using Bootstrap 5 JS API)
        // System Design implies dynamic UI components might use modals.
        window.addEventListener('closeModal', event => {
            if (event.detail && event.detail.elementId) {
                const modalElement = document.querySelector(event.detail.elementId);
                if (modalElement) {
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                } else {
                    console.warn(`Modal element not found for close event: ${event.detail.elementId}`);
                }
            }
        });

        window.addEventListener('closeCanvas', event => {
            if (event.detail && event.detail.elementId) {
                const canvasElement = document.querySelector(event.detail.elementId);
                if (canvasElement) {
                    const canvasInstance = bootstrap.Offcanvas.getInstance(canvasElement);
                    if (canvasInstance) {
                        canvasInstance.hide();
                    }
                } else {
                    console.warn(`Offcanvas element not found for close event: ${event.detail.elementId}`);
                }
            }
        });

        // Sound Event Listeners
        const playSound = (soundName) => {
            try {
                // Ensure assetsPath has trailing slash if sound files are directly in 'sound/' folder
                new Audio("{{ rtrim($assetsPath, '/') }}/sound/" + soundName + ".mp3").play().catch(e => console.warn(`Could not play sound ${soundName}:`, e));
            } catch (e) {
                console.warn(`Error initializing sound ${soundName}:`, e);
            }
        };

        window.addEventListener('playMessageSound', () => playSound('message'));
        window.addEventListener('playNotificationSound', () => playSound('notification'));
        window.addEventListener('playErrorSound', () => playSound('error'));

        // ScrollToTop Event Listener
        window.addEventListener('scrollToTop', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>

@stack('pricing-script')

@yield('page-script')

@stack('custom-scripts')

@livewireScripts
