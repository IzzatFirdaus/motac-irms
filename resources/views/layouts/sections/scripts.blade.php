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

{{-- Theme Main Menu Script --}}
<script src="{{ $assetsPath }}vendor/js/menu.js"></script>

{{-- Toastr & SweetAlert2 --}}
<script src="{{ $assetsPath }}vendor/libs/toastr/toastr.js"></script>
<script src="{{ $assetsPath }}vendor/libs/sweetalert2/sweetalert2.js"></script>

@yield('vendor-script') {{-- Page specific vendor scripts --}}

{{-- Theme Main JS --}}
<script src="{{ $assetsPath }}js/main.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('%c[SCRIPTS.BLADE.PHP] DOMContentLoaded event fired!',
            'color: #28a745; font-weight: bold;');

        // --- START: Global Theme Toggling JavaScript ---
        const THEME_STORAGE_KEY = 'motac_theme';
        const HTML_ELEMENT = document.documentElement;
        const DEFAULT_THEME = 'light';

        function updateNavbarThemeSwitcherIcon(theme) {
            const switcherLink = document.getElementById('motacNavbarThemeSwitcher');
            if (switcherLink) {
                const icon = switcherLink.querySelector('i');
                if (icon) {
                    const lightModeTitle = switcherLink.getAttribute('data-light-title') ||
                        'Tukar ke Mod Gelap';
                    const darkModeTitle = switcherLink.getAttribute('data-dark-title') || 'Tukar ke Mod Cerah';
                    icon.className = `bi ${theme === 'dark' ? 'bi-sun-fill' : 'bi-moon-stars-fill'} fs-5`;
                    switcherLink.setAttribute('title', theme === 'dark' ? darkModeTitle : lightModeTitle);
                    switcherLink.setAttribute('aria-label', theme === 'dark' ? darkModeTitle : lightModeTitle);
                }
            }
        }

        function applyTheme(theme) {
            HTML_ELEMENT.setAttribute('data-bs-theme', theme);
            updateNavbarThemeSwitcherIcon(theme);
        }

        window.globalToggleAppTheme = function() {
            let currentTheme = HTML_ELEMENT.getAttribute('data-bs-theme') || DEFAULT_THEME;
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem(THEME_STORAGE_KEY, newTheme);
            applyTheme(newTheme);
            if (window.Livewire) {
                Livewire.dispatch('themeHasChanged', {
                    theme: newTheme
                });
            }
        };

        function initializeCoreTheme() {
            const storedTheme = localStorage.getItem(THEME_STORAGE_KEY);
            const serverSetTheme = HTML_ELEMENT.getAttribute('data-bs-theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            let determinedTheme = storedTheme || (serverSetTheme && (serverSetTheme === 'light' ||
                serverSetTheme === 'dark') ? serverSetTheme : (systemPrefersDark ? 'dark' :
                DEFAULT_THEME));
            applyTheme(determinedTheme);
        }
        initializeCoreTheme();
        // --- END: Global Theme Toggling JavaScript ---

        // Toastr Initialization
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true
            };
            window.addEventListener('toastr', event => {
                if (event.detail && event.detail.type && event.detail.message) {
                    toastr[event.detail.type](event.detail.message, event.detail.title || null);
                }
            });
        }

        // Bootstrap Modal Listeners
        window.addEventListener('openModal', e => {
            const modalId = e.detail?.elementId;
            if (modalId) {
                const element = document.getElementById(modalId);
                if (element) {
                    new bootstrap.Modal(element).show();
                }
            }
        });

        window.addEventListener('closeModal', e => {
            const modalId = e.detail?.elementId;
            if (modalId) {
                const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
                modal?.hide();
            }
        });

        // Initialize all Bootstrap tooltips on the page
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // =================================================================
        // == START: DELETE CONFIRMATION CODE ==
        // =================================================================
        window.addEventListener('open-delete-modal', event => {
            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded. Cannot show delete confirmation.');
                return;
            }

            const {
                id,
                itemDescription,
                deleteMethod
            } = event.detail;

            Swal.fire({
                title: 'Anda Pasti?',
                text: `Anda akan memadam ${itemDescription}. Tindakan ini tidak boleh diubah.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Padam!',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch(deleteMethod, {
                        id: id
                    });
                }
            });
        });
        // =================================================================
        // == END: DELETE CONFIRMATION CODE ==
        // =================================================================

    }); // <-- Add the code *before* this closing line
</script>

@yield('page-script')
@stack('custom-scripts')
@livewireScripts
