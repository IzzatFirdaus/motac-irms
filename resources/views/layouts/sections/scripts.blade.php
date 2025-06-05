{{-- resources/views/layouts/sections/scripts.blade.php --}}
@php
    $configData = \App\Helpers\Helpers::appClasses();
    $assetsPath = rtrim(asset('assets/'), '/') . '/';
@endphp

{{-- Core Vendors --}}
<script src="{{ $assetsPath }}vendor/libs/jquery/jquery.js"></script>
<script src="{{ $assetsPath }}vendor/libs/popper/popper.js"></script>
<script src="{{ $assetsPath }}vendor/js/bootstrap.js"></script>
<script src="{{ $assetsPath }}vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ $assetsPath }}vendor/libs/node-waves/node-waves.js"></script>
{{-- Optional Vendors from your original file --}}
@if (isset($configData['hammerJS']) && $configData['hammerJS'] === true)
    <script src="{{ $assetsPath }}vendor/libs/hammer/hammer.js"></script>
@endif
@if (isset($configData['typeaheadJS']) && $configData['typeaheadJS'] === true)
    <script src="{{ $assetsPath }}vendor/libs/typeahead-js/typeahead.bundle.js"></script>
@endif
{{-- Theme Main Menu Script --}}
<script src="{{ $assetsPath }}vendor/js/menu.js"></script>
{{-- Toastr --}}
<script src="{{ $assetsPath }}vendor/libs/toastr/toastr.js"></script>

@yield('vendor-script') {{-- Page specific vendor scripts --}}

{{-- Theme Main JS (ensure this doesn't conflict with the theme logic below) --}}
<script src="{{ $assetsPath }}js/main.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('%c[SCRIPTS.BLADE.PHP] DOMContentLoaded event fired!', 'color: #28a745; font-weight: bold;');

        // --- START: Global Theme Toggling JavaScript ---
        // console.log('%c[Theme Script] Initializing theme functions...', 'color: #007bff; font-weight: bold;'); // Can be verbose
        const THEME_STORAGE_KEY = 'motac_theme';
        const HTML_ELEMENT = document.documentElement;
        const DEFAULT_THEME = 'light'; // Your system's default theme

        function updateNavbarThemeSwitcherIcon(theme) {
            const switcherLink = document.getElementById('motacNavbarThemeSwitcher');
            if (switcherLink) {
                const icon = switcherLink.querySelector('i');
                if (icon) {
                    const lightModeTitle = switcherLink.getAttribute('data-light-title') || 'Tukar ke Mod Gelap';
                    const darkModeTitle = switcherLink.getAttribute('data-dark-title') || 'Tukar ke Mod Cerah';

                    icon.className = `bi ${theme === 'dark' ? 'bi-sun-fill' : 'bi-moon-stars-fill'} fs-5`;
                    switcherLink.setAttribute('title', theme === 'dark' ? darkModeTitle : lightModeTitle);
                    switcherLink.setAttribute('aria-label', theme === 'dark' ? darkModeTitle : lightModeTitle);
                    // console.log('[Theme Script] Navbar icon class updated to:', icon.className); // Verbose
                } else {
                    // This error is relevant if the switcher link exists but its icon tag is missing.
                    console.error('[Theme Script] Icon element (<i> tag) NOT found inside #motacNavbarThemeSwitcher.');
                }
            } else {
                // This is an informational message, expected on pages like /login where the switcher is not present.
                console.info('[Theme Script] Optional: motacNavbarThemeSwitcher element not found. Navbar icon update skipped.');
            }
        }

        function applyTheme(theme) {
            // console.log('[Theme Script] Applying theme to HTML element:', theme); // Verbose
            HTML_ELEMENT.setAttribute('data-bs-theme', theme);
            updateNavbarThemeSwitcherIcon(theme); // This will update the icon if the element exists
        }

        window.globalToggleAppTheme = function() {
            console.log('%c[Theme Script] globalToggleAppTheme function CALLED!', 'color: #28a745; font-weight: bold;');
            let currentTheme = HTML_ELEMENT.getAttribute('data-bs-theme') || DEFAULT_THEME;
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            localStorage.setItem(THEME_STORAGE_KEY, newTheme);
            applyTheme(newTheme);

            if (window.Livewire) {
                Livewire.dispatch('themeHasChanged', { theme: newTheme });
            }
        };

        function initializeCoreTheme() {
            // console.log('[Theme Script] initializeCoreTheme function started.'); // Verbose
            const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
            const serverSetTheme = HTML_ELEMENT.getAttribute('data-bs-theme'); // Theme potentially set by server-side on initial render
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            let determinedTheme = DEFAULT_THEME; // Default to 'light'

            if (storedTheme) {
                determinedTheme = storedTheme;
            } else if (serverSetTheme && (serverSetTheme === 'light' || serverSetTheme === 'dark')) {
                // Respect server-set theme if no local storage override
                determinedTheme = serverSetTheme;
            } else if (systemPrefersDark) {
                determinedTheme = 'dark';
            }
            // else, determinedTheme remains DEFAULT_THEME

            // console.log('[Theme Script] Final initial theme to apply:', determinedTheme); // Verbose
            applyTheme(determinedTheme);
        }

        // Always initialize the core theme settings (e.g., setting data-bs-theme on <html>).
        // The updateNavbarThemeSwitcherIcon function (called within applyTheme)
        // will gracefully handle cases where the switcher UI element is not present.
        initializeCoreTheme();
        // --- END: Global Theme Toggling JavaScript ---

        // Existing Toastr and Bootstrap components initialization
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
                // ... any other specific toastr options you use ...
            };
            window.addEventListener('toastr', event => {
                if (event.detail && event.detail.type && event.detail.message) {
                    if (toastr[event.detail.type]) {
                        toastr[event.detail.type](event.detail.message, event.detail.title || null);
                    } else {
                        console.warn('[Scripts.blade.php] Invalid Toastr type:', event.detail.type);
                    }
                }
            });
        } else {
            console.warn('[Scripts.blade.php] Toastr library not found.');
        }

        // Placeholder for getBsInstance as it was in your original file.
        // The modal listeners below use Bootstrap's static methods directly for robustness.
        const getBsInstance = (id, Comp) => {
            /* User's original placeholder comment or actual implementation for getBsInstance */
            // console.warn('[Scripts.blade.php] getBsInstance is a placeholder and might need implementation if used elsewhere.');
            return null; // Default placeholder behavior
        };

        window.addEventListener('openModal', e => {
            const modalId = e.detail?.elementId;
            if (modalId) {
                const element = document.getElementById(modalId);
                if (element) {
                    let instance = bootstrap.Modal.getInstance(element);
                    if (!instance) {
                        instance = new bootstrap.Modal(element);
                    }
                    instance.show();
                } else {
                    console.warn('[Scripts.blade.php] Element with ID "' + modalId + '" not found for openModal event.');
                }
            }
        });

        window.addEventListener('closeModal', e => {
            const modalId = e.detail?.elementId;
            if (modalId) {
                const element = document.getElementById(modalId);
                if (element) {
                    const instance = bootstrap.Modal.getInstance(element);
                    instance?.hide(); // Use optional chaining as instance might be null if not initialized
                } else {
                    console.warn('[Scripts.blade.php] Element with ID "' + modalId + '" not found for closeModal event.');
                }
            }
        });

        // Initialize all Bootstrap tooltips on the page
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // ... any other global listeners or initializations ...
    });
</script>

@yield('page-script')
@stack('custom-scripts')
@livewireScripts
