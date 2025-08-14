{{--
    resources/views/components/confirms-password.blade.php

    MYDS-compliant Password confirmation dialog for sensitive operations.
    Integrates with Livewire for secure password verification.

    - Follows MYDS modal/dialog anatomy, grid spacing, and accessibility guidelines.
    - Applies MYDS color tokens, typography, spacing, and motion for transitions.
    - Adheres to MyGOVEA principles: error prevention, user control, accessibility, minimalism, clear hierarchy, and documentation.

    Props:
    - $title: string - Modal title (default: 'Sahkan Kata Laluan')
    - $content: string - Modal content/explanation
    - $button: string - Confirm button text (default: 'Sahkan')

    Usage:
    <x-confirms-password wire:then="deleteUser">
        <x-danger-button>Delete Account</x-danger-button>
    </x-confirms-password>
--}}

@props([
    'title' => 'Sahkan Kata Laluan',
    'content' => 'Untuk keselamatan anda, sila sahkan kata laluan anda untuk meneruskan.',
    'button' => 'Sahkan'
])

@php
    // Generate a unique ID for the confirmation context
    $confirmableId = md5($attributes->wire('then')->value());
@endphp

{{-- Trigger Element: Wraps the action requiring confirmation --}}
<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
    class="myds-confirm-trigger"
>
    {{ $slot }}
</span>

{{-- Password Confirmation Modal (MYDS Dialog Anatomy) --}}
@once
    <x-dialog-modal wire:model.live="confirmingPassword">
        <x-slot name="title">
            <div class="d-flex align-items-center gap-2">
                {{-- MYDS: Leading Icon for Security --}}
                <i class="bi bi-shield-lock-fill text-primary" style="font-size: 1.5rem;" aria-hidden="true"></i>
                <span class="myds-heading h5 fw-semibold" id="confirm-password-title">{{ $title }}</span>
            </div>
        </x-slot>

        <x-slot name="content">
            {{-- Explanation Text --}}
            <div class="myds-body-text mb-2 text-muted" id="confirm-password-desc">
                {{ $content }}
            </div>

            {{-- Password Input Field --}}
            <div class="mt-3" x-data
                x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
                <x-input
                    type="password"
                    class="form-control form-control-sm myds-input myds-radius-md {{ $errors->has('confirmable_password') ? 'is-invalid myds-input-error' : '' }}"
                    placeholder="Kata Laluan"
                    x-ref="confirmable_password"
                    wire:model="confirmablePassword"
                    wire:keydown.enter="confirmPassword"
                    autocomplete="current-password"
                    aria-label="Kata Laluan"
                    aria-describedby="confirm-password-desc"
                />

                {{-- Inline validation error --}}
                <x-input-error for="confirmable_password" class="mt-2 small text-danger myds-input-error-message" />
            </div>
        </x-slot>

        <x-slot name="footer">
            {{-- Cancel Button (MYDS secondary button) --}}
            <x-secondary-button wire:click="stopConfirmingPassword" wire:loading.attr="disabled" icon="bi-x">
                {{ __('Batal') }}
            </x-secondary-button>

            {{-- Confirm Button (MYDS primary button, danger context) --}}
            <x-button class="ms-2" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled">
                <i class="bi bi-check-lg me-1"></i>
                {{ $button }}
            </x-button>
        </x-slot>
    </x-dialog-modal>
@endonce

{{--
    == Documentation & MYDS/MyGOVEA Compliance ==
    - Dialog modal anatomy follows MYDS: header (icon+title), content (explanation+input), footer (actions).
    - Applies MYDS spacing, color tokens, typography, and radius.
    - Accessibility: ARIA labels, keyboard focus management, error feedback.
    - Principle 5 (Minimalist): Only necessary fields/actions shown.
    - Principle 17 (Error Prevention): Inline validation, confirmation flow.
    - Principle 14 (Typography): Uses MYDS heading/body classes.
    - Principle 16 (User Control): Clear cancel/confirm actions, keyboard and mouse accessible.
--}}
