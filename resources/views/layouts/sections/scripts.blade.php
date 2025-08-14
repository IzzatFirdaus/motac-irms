{{-- resources/views/layouts/sections/scripts.blade.php --}}
@php
    $assetsPath = rtrim(asset('assets/'), '/') . '/';
@endphp

{{-- Core Vendors --}}
<script src="{{ $assetsPath }}vendor/libs/jquery/jquery.js"></script>
<script src="{{ $assetsPath }}vendor/libs/popper/popper.js"></script>
<script src="{{ $assetsPath }}vendor/js/bootstrap.js"></script>
<script src="{{ $assetsPath }}vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ $assetsPath }}vendor/libs/node-waves/node-waves.js"></script>
<script src="{{ $assetsPath }}vendor/js/menu.js"></script>

{{-- Toastr & SweetAlert2 --}}
<script src="{{ $assetsPath }}vendor/libs/toastr/toastr.js"></script>
<script src="{{ $assetsPath }}vendor/libs/sweetalert2/sweetalert2.js"></script>
@yield('vendor-script')

{{-- Theme Main JS --}}
<script src="{{ $assetsPath }}js/main.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Global Theme Toggler Click Handler ---
        // This function is now the only part needed here. It calls the master script's logic.
        window.toggleTheme = () => {
            const current = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme-preference', newTheme);
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            if (window.Livewire) {
                window.Livewire.dispatch('themeHasChanged', { theme: newTheme });
            }
        };

        // Initialize Toastr
        if (typeof toastr !== 'undefined') {
            toastr.options = { "closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "preventDuplicates": true };
            window.addEventListener('toastr', event => {
                if (event.detail && event.detail.type && event.detail.message) {
                    toastr[event.detail.type](event.detail.message, event.detail.title || null);
                }
            });
        }

        // Initialize Bootstrap Modals via Livewire events
        window.addEventListener('openModal', e => {
            const el = document.getElementById(e.detail?.elementId);
            if (el) new bootstrap.Modal(el).show();
        });
        window.addEventListener('closeModal', e => {
            const modal = bootstrap.Modal.getInstance(document.getElementById(e.detail?.elementId));
            modal?.hide();
        });

        // Initialize Bootstrap Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) { return new bootstrap.Tooltip(tooltipTriggerEl); });

        // Initialize Delete Confirmation Modal via Livewire events
        window.addEventListener('open-delete-modal', event => {
            if (typeof Swal === 'undefined') return;
            const { id, itemDescription, deleteMethod } = event.detail;
            Swal.fire({
                title: 'Anda Pasti?',
                text: `Anda akan memadam ${itemDescription}. Tindakan ini tidak boleh diubah.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Padam!',
                cancelButtonText: 'Batal',
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-secondary ms-2' },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch(deleteMethod, { id: id });
                }
            });
        });
    });
</script>

@yield('page-script')
@stack('custom-scripts')
@livewireScripts
