{{-- resources/views/layouts/sections/scripts.blade.php --}}
@php
    $configData = \App\Helpers\Helpers::appClasses();
    $textDirection = $configData['textDirection'] ?? 'ltr';
    $assetsPath = rtrim(asset('assets/'), '/') . '/';
@endphp

<script src="{{ $assetsPath }}vendor/libs/jquery/jquery.js"></script>
<script src="{{ $assetsPath }}vendor/libs/popper/popper.js"></script>
<script src="{{ $assetsPath }}vendor/js/bootstrap.js"></script>
<script src="{{ $assetsPath }}vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ $assetsPath }}vendor/libs/node-waves/node-waves.js"></script>
@if(isset($configData['hammerJS']) && $configData['hammerJS'] === true)
<script src="{{ $assetsPath }}vendor/libs/hammer/hammer.js"></script>
@endif
@if(isset($configData['typeaheadJS']) && $configData['typeaheadJS'] === true)
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
@livewireScripts
