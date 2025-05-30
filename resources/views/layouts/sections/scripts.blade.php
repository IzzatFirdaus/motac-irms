{{-- scripts.blade.php --}}
<script src="{{ asset(mix('assets/vendor/libs/jquery/jquery.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/popper/popper.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/bootstrap.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/node-waves/node-waves.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/hammer/hammer.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/libs/typeahead-js/typeahead.js')) }}"></script>
<script src="{{ asset(mix('assets/vendor/js/menu.js')) }}"></script>
<script src="{{ asset('assets/vendor/libs/toastr/toastr.js') }}"></script>

@yield('vendor-script')
<script src="{{ asset(mix('assets/js/main.js')) }}"></script>

<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right", // Consistent with LTR/RTL as toastr handles actual positioning
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        // Design Document: Ensure RTL works for notifications
        "rtl": {{ app()->getLocale() === 'ar' || app()->getLocale() === 'fa' || app()->getLocale() === 'he' ? 'true' : 'false' }}
    }

    window.addEventListener('toastr', event => {
        toastr[event.detail.type](event.detail.message, // Message should be translated before being dispatched
            event.detail.title ?? '') // Title should be translated before being dispatched
    });
</script>

<script>
    // Modal and Offcanvas closing events are fine
    window.addEventListener('closeModal', event => {
        $(event.detail.elementId).modal('hide');
    })
    window.addEventListener('closeCanvas', event => {
        $(event.detail.elementId).offcanvas('hide');
    })
</script>

<script>
    // Sound notifications are good for an internal system for immediate feedback.
    // Ensure these sound files are appropriate and not overly distracting.
    window.addEventListener('playMessageSound', event => {
        new Audio('{{ asset('assets/sound/message.mp3') }}').play();
    })
    window.addEventListener('playNotificationSound', event => {
        new Audio('{{ asset('assets/sound/notification.mp3') }}').play();
    })
    window.addEventListener('playErrorSound', event => {
        new Audio('{{ asset('assets/sound/error.mp3') }}').play();
    })
</script>

<script>
    window.addEventListener('scrollToTop', event => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    })
</script>

@stack('pricing-script')
@yield('page-script')
@stack('modals')
@stack('custom-scripts')

@livewireScripts

{{-- resources/views/layouts/sections/scripts.blade.php --}}
{{-- @php
    $configData = \App\Helpers\Helpers::appClasses();
    $textDirection = $configData['textDirection'] ?? 'ltr';
    $assetsPath = rtrim(asset('assets/'), '/') . '/';
@endphp

<script src="{{ $assetsPath }}vendor/libs/jquery/jquery.js"></script>
<script src="{{ $assetsPath }}vendor/libs/popper/popper.js"></script>
<script src="{{ $assetsPath }}vendor/js/bootstrap.js"></script>
<script src="{{ $assetsPath }}vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ $assetsPath }}vendor/libs/node-waves/node-waves.js"></script>
@if (isset($configData['hammerJS']) && $configData['hammerJS'] === true)
<script src="{{ $assetsPath }}vendor/libs/hammer/hammer.js"></script>
@endif
@if (isset($configData['typeaheadJS']) && $configData['typeaheadJS'] === true)
<script src="{{ $assetsPath }}vendor/libs/typeahead-js/typeahead.bundle.js"></script>
@endif
<script src="{{ $assetsPath }}vendor/js/menu.js"></script>
<script src="{{ $assetsPath }}vendor/libs/toastr/toastr.js"></script>

@yield('vendor-script')

<script src="{{ $assetsPath }}js/main.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true, "debug": false, "newestOnTop": true, "progressBar": true,
                "positionClass": "toast-top-right", "preventDuplicates": true, "onclick": null,
                "showDuration": "300", "hideDuration": "1000", "timeOut": "7000", "extendedTimeOut": "1000",
                "showEasing": "swing", "hideEasing": "linear", "showMethod": "fadeIn", "hideMethod": "fadeOut",
                "rtl": document.documentElement.getAttribute('dir') === 'rtl'
            };
            window.addEventListener('toastr', event => {
                if (event.detail && typeof event.detail.type === 'string' && typeof event.detail.message === 'string') {
                    toastr[event.detail.type](event.detail.message, event.detail.title ?? '');
                } else { console.warn('Toastr event invalid:', event.detail); }
            });
        } else { console.warn('Toastr library not found.'); }

        const getBsInstance = (id, Comp) => { const el = document.getElementById(id); return el ? Comp.getInstance(el) || new Comp(el) : null; };
        window.addEventListener('openModal', e => getBsInstance(e.detail?.elementId, bootstrap.Modal)?.show());
        window.addEventListener('closeModal', e => getBsInstance(e.detail?.elementId, bootstrap.Modal)?.hide());
        window.addEventListener('openCanvas', e => getBsInstance(e.detail?.elementId, bootstrap.Offcanvas)?.show());
        window.addEventListener('closeCanvas', e => getBsInstance(e.detail?.elementId, bootstrap.Offcanvas)?.hide());
        window.addEventListener('scrollToTop', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
    });
</script>

@yield('page-script')
@stack('custom-scripts')
@livewireScripts --}}
