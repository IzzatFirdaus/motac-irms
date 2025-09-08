{{--
    resources/views/components/modal.blade.php
    MYDS-compliant Modal component for MOTAC IRMS v4.0

    - Follows MYDS dialog/modal anatomy, radius, shadow, grid, and accessibility standards.
    - Responsive sizing (sm, lg, xl, fullscreen), header/body/footer slots.
    - Keyboard and ARIA accessibility for modal dialogs.
    - Integrates Alpine.js for Livewire compatibility (show/hide, focus trap).
    - Uses MYDS color tokens, typography, spacing, and motion tokens.
    - Referenced: MYDS-Design-Overview.md, MYDS-Develop-Overview.md, prinsip-reka-bentuk-mygovea.md

    Props:
    - $id: string - Modal ID (auto-generated if not provided)
    - $maxWidth: string - Modal size: 'sm', 'lg', 'xl', 'fullscreen'
    - $title: string - Modal title (optional)
    - $icon: string - Icon class for title (optional)
    - $headerClass: string - Header CSS classes (default: 'modal-header')
    - $bodyClass: string - Body CSS classes (default: 'modal-body')
    - $footerClass: string - Footer CSS classes (default: 'modal-footer')

    Slots:
    - $slot: Main modal content
    - $header: Custom header content (optional)
    - $footer: Footer content (optional)

    Usage:
    <x-modal wire:model="showModal" title="Edit User" icon="bi-pencil">
        <x-slot name="footer">
            <button class="btn btn-secondary">Close</button>
            <button class="btn btn-primary">Save</button>
        </x-slot>
    </x-modal>

    Dependencies: Alpine.js, Bootstrap 5, Livewire
--}}
@props([
    'id',
    'maxWidth' => null,
    'title' => null,
    'icon' => null,
    'headerClass' => 'myds-modal-header modal-header py-3 px-4 border-bottom',
    'bodyClass' => 'myds-modal-body modal-body px-4 py-4',
    'footerClass' => 'myds-modal-footer modal-footer px-4 py-3 border-top',
])

@php
    // Generate unique modal ID if not provided
    $id = $id ?? md5($attributes->wire('model').uniqid());

    // Determine modal size class (MYDS grid system)
    $modalSizeClass = '';
    switch ($maxWidth ?? '') {
        case 'sm': $modalSizeClass = ' modal-sm'; break;
        case 'lg': $modalSizeClass = ' modal-lg'; break;
        case 'xl': $modalSizeClass = ' modal-xl'; break;
        case 'fullscreen': $modalSizeClass = ' modal-fullscreen'; break;
        default: $modalSizeClass = ''; break;
    }
@endphp

<div
    x-data="{ show: @entangle($attributes->wire('model')).defer }"
    x-init="() => {
        // Alpine.js initializes the modal and keeps show state in sync with Livewire
        let modalElement = document.getElementById('{{ $id }}');
        if (!modalElement) {
            console.error('Modal element #{{ $id }} not found.');
            return;
        }
        let bootstrapModal = bootstrap.Modal.getInstance(modalElement);
        if (!bootstrapModal) {
            bootstrapModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: true // allow escape key
            });
        }
        $watch('show', value => {
            if (value) {
                bootstrapModal.show();
                // Focus first focusable element in modal for accessibility
                setTimeout(() => {
                    let input = modalElement.querySelector('input, select, textarea, button');
                    if (input) input.focus();
                }, 100);
            } else {
                bootstrapModal.hide();
            }
        });
        modalElement.addEventListener('hidden.bs.modal', () => {
            show = false;
        });
    }"
    wire:ignore.self
    class="modal fade myds-modal"
    tabindex="-1"
    id="{{ $id }}"
    aria-labelledby="{{ $id }}Label"
    aria-modal="true"
    role="dialog"
    aria-hidden="true"
    x-ref="{{ $id }}"
>
    <div class="modal-dialog{{ $modalSizeClass }} modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content myds-radius-m myds-shadow-card" style="border-radius:8px; box-shadow:0px 2px 6px 0px rgba(0,0,0,0.05), 0px 6px 24px 0px rgba(0,0,0,0.05); background:var(--myds-bg-white);">
            {{-- Modal Header --}}
            @if ($title || isset($header))
                <div class="{{ $headerClass }}">
                    @if (isset($header))
                        {{ $header }}
                    @else
                        <h5 class="modal-title d-flex align-items-center myds-font-poppins fw-semibold" id="{{ $id }}Label">
                            @if($icon)<i class="bi {{ $icon }} me-2 fs-5 text-primary"></i>@endif
                            {{ $title }}
                        </h5>
                        {{-- Close button with ARIA for accessibility --}}
                        <button type="button" class="btn-close myds-close" @click="show = false" aria-label="Tutup"></button>
                    @endif
                </div>
            @endif

            {{-- Modal Body --}}
            <div class="{{ $bodyClass }}">
                {{ $slot }}
            </div>

            {{-- Modal Footer --}}
            @if (isset($footer))
                <div class="{{ $footerClass }}">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
    {{-- Modal background overlay for focus (MYDS standard) --}}
    <div class="modal-backdrop fade show myds-bg-overlay" style="background:rgba(0,0,0,0.5);"></div>
</div>

{{--
    === MYDS Documentation Reference ===
    - Modal follows MYDS dialog anatomy and accessibility practices.
    - Uses MYDS color tokens, radius (8px), shadow, and font families.
    - Keyboard accessible (Tab, Escape), ARIA roles/labels, focus management.
    - Responsive grid sizing via modal-{sm|lg|xl|fullscreen} classes.
    - Custom header/body/footer slots for flexibility.
    - Overlay/backdrop uses MYDS overlay color for focus and safety.
    - Refer to MYDS-Design-Overview.md for modal/dialog specifications.
    - Mapping to MyGOVEA Principles: Seragam, Minimalis, Kawalan Pengguna, Pencegahan Ralat, Struktur Hierarki, Panduan & Dokumentasi.
--}}
