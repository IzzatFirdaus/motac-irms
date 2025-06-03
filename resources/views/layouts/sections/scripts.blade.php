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
        console.log('%c[Theme Script] Initializing theme functions...', 'color: #007bff; font-weight: bold;');
        const THEME_STORAGE_KEY = 'motac_theme'; // Made key more specific
        const HTML_ELEMENT = document.documentElement;
        const DEFAULT_THEME = 'light'; // Your system's default theme

        function applyTheme(theme) {
            console.log('[Theme] Applying theme now:', theme);
            HTML_ELEMENT.setAttribute('data-bs-theme', theme);
            updateNavbarThemeSwitcherIcon(theme); // Update the icon in the navbar
        }

        function updateNavbarThemeSwitcherIcon(theme) {
            console.log('[Theme] Attempting to update navbar icon for theme:', theme);
            const switcherLink = document.getElementById('motacNavbarThemeSwitcher');
            console.log('[Theme] Switcher link element (ID: motacNavbarThemeSwitcher):', switcherLink);

            if (switcherLink) {
                const icon = switcherLink.querySelector('i');
                if (icon) {
                    const lightModeTitle = switcherLink.getAttribute('data-light-title') || 'Tukar ke Mod Gelap';
                    const darkModeTitle = switcherLink.getAttribute('data-dark-title') || 'Tukar ke Mod Cerah';

                    icon.className = `bi ${theme === 'dark' ? 'bi-sun-fill' : 'bi-moon-stars-fill'} fs-5`;
                    switcherLink.setAttribute('title', theme === 'dark' ? darkModeTitle : lightModeTitle);
                    switcherLink.setAttribute('aria-label', theme === 'dark' ? darkModeTitle : lightModeTitle);
                    console.log('[Theme] Navbar icon class updated to:', icon.className);
                } else {
                    console.error('[Theme] Icon element (<i> tag) NOT found inside theme switcher link.');
                }
            } else {
                console.warn('[Theme] Theme switcher link with ID "motacNavbarThemeSwitcher" NOT found in DOM. Icon cannot be updated.');
            }
        }

        window.globalToggleAppTheme = function() {
            console.log('%c[Theme] globalToggleAppTheme function CALLED by click!', 'color: #28a745; font-weight: bold;');
            let currentTheme = HTML_ELEMENT.getAttribute('data-bs-theme') || DEFAULT_THEME;
            console.log('[Theme] Current theme before toggle:', currentTheme);
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            console.log('[Theme] New theme to apply:', newTheme);

            localStorage.setItem(THEME_STORAGE_KEY, newTheme);
            applyTheme(newTheme);

            if (window.Livewire) {
                Livewire.dispatch('themeHasChanged', { theme: newTheme });
            }
        };

        function initializeTheme() {
            console.log('[Theme] initializeTheme function started.');
            const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
            const serverSetTheme = HTML_ELEMENT.getAttribute('data-bs-theme'); // Theme set by server on initial render
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            let determinedTheme = DEFAULT_THEME; // Default to 'light'

            console.log('[Theme] - localStorage theme ("' + THEME_STORAGE_KEY + '"):', storedTheme);
            console.log('[Theme] - Server-set data-bs-theme attribute:', serverSetTheme);
            console.log('[Theme] - System prefers dark scheme:', systemPrefersDark);

            if (storedTheme) {
                determinedTheme = storedTheme;
                console.log('[Theme] Priority 1: Using stored theme from localStorage:', determinedTheme);
            } else if (serverSetTheme && (serverSetTheme === 'light' || serverSetTheme === 'dark')) {
                // If no localStorage preference, respect what the server initially rendered
                determinedTheme = serverSetTheme;
                console.log('[Theme] Priority 2: Using server-set theme (no localStorage override):', determinedTheme);
            } else if (systemPrefersDark) {
                determinedTheme = 'dark';
                console.log('[Theme] Priority 3: Using system preference (dark):', determinedTheme);
            } else {
                console.log('[Theme] Priority 4: Using default theme (light):', determinedTheme);
            }

            console.log('[Theme] Final initial theme to apply by initializeTheme():', determinedTheme);
            applyTheme(determinedTheme);
        }

        // Check if the switcher element exists before trying to initialize.
        // The element might be missing if the navbar isn't rendered or if it was removed.
        if (document.readyState === 'loading') { // DOM not ready yet
            console.warn('[Theme Script] DOM not ready when script is parsed. Initialization deferred to DOMContentLoaded.');
        } else { // DOM is already ready (interactive or complete)
             console.log('[Theme Script] DOM was already ready. Checking for switcher element.');
             if(document.getElementById('motacNavbarThemeSwitcher')) {
                 console.log('[Theme Script] motacNavbarThemeSwitcher element IS present. Initializing theme logic immediately.');
                 initializeTheme();
             } else {
                 console.warn('[Theme Script] motacNavbarThemeSwitcher element NOT present when trying immediate initialization.');
             }
        }
        // --- END: Global Theme Toggling JavaScript ---

        // Existing Toastr and Bootstrap components initialization
        if (typeof toastr !== 'undefined') {
            toastr.options = { /* ... your toastr options ... */ };
            window.addEventListener('toastr', event => { /* ... your toastr listener ... */ });
        } else {
            console.warn('[Scripts.blade.php] Toastr library not found.');
        }
        const getBsInstance = (id, Comp) => { /* ... */ };
        window.addEventListener('openModal', e => getBsInstance(e.detail?.elementId, bootstrap.Modal)?.show());
        window.addEventListener('closeModal', e => getBsInstance(e.detail?.elementId, bootstrap.Modal)?.hide());
        // ... other listeners ...
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });
    });
</script>

@yield('page-script')
@stack('custom-scripts')
@livewireScripts
