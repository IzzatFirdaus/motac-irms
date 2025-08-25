{{--
    resources/views/components/modal.blade.php

    Bootstrap modal component with Alpine.js integration for Livewire compatibility.
    Supports various sizes, custom headers, flexible content areas, and Livewire wire:model binding.

    Props:
    - $id: string - Modal ID (auto-generated if not provided)
    - $maxWidth: string - Modal size: 'sm', 'lg', 'xl', 'fullscreen' (optional)
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
        <!-- Modal content -->
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
    'headerClass' => 'modal-header',
    'bodyClass' => 'modal-body',
    'footerClass' => 'modal-footer',
])

@php
    // Generate unique modal ID if not provided
    $id = $id ?? md5($attributes->wire('model').uniqid());

    // Determine modal size class
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
                keyboard: false
            });
        }
        $watch('show', value => {
            if (value) {
                bootstrapModal.show();
            } else {
                bootstrapModal.hide();
            }
        });
        modalElement.addEventListener('hidden.bs.modal', () => {
            show = false;
        });
    }"
    wire:ignore.self
    class="modal fade"
    tabindex="-1"
    id="{{ $id }}"
    aria-labelledby="{{ $id }}Label"
    aria-hidden="true"
    x-ref="{{ $id }}"
>
    <div class="modal-dialog{{ $modalSizeClass }} modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            {{-- Modal Header --}}
            @if ($title || isset($header))
                <div class="{{ $headerClass }}">
                    @if (isset($header))
                        {{ $header }}
                    @else
                        <h5 class="modal-title d-flex align-items-center" id="{{ $id }}Label">
                            @if($icon)<i class="bi {{ $icon }} me-2 fs-5"></i>@endif
                            {{ $title }}
                        </h5>
                        <button type="button" class="btn-close" @click="show = false" aria-label="{{ __('Tutup') }}"></button>
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
</div>
