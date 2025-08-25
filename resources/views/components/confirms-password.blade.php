{{--
    resources/views/components/confirms-password.blade.php

    Password confirmation component for sensitive operations.
    Integrates with Livewire for secure password verification.

    Props:
    - $title: string - Modal title (default: 'Sahkan Kata Laluan')
    - $content: string - Modal content text
    - $button: string - Confirm button text (default: 'Sahkan')

    Usage:
    <x-confirms-password wire:then="deleteUser">
        <x-danger-button>Delete Account</x-danger-button>
    </x-confirms-password>

    Features:
    - Secure password confirmation
    - Auto-focus on password field
    - Enter key support
    - Loading states

    Dependencies: Alpine.js, Livewire, Bootstrap 5, x-dialog-modal
--}}
@props(['title' => __('Sahkan Kata Laluan'), 'content' => __('Untuk keselamatan anda, sila sahkan kata laluan anda untuk meneruskan.'), 'button' => __('Sahkan')])

@php
$confirmableId = md5($attributes->wire('then')->value());
@endphp

{{-- Trigger Element --}}
<span {{ $attributes->wire('then') }} x-data x-ref="span"
  x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
  x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);">
  {{ $slot }}
</span>

{{-- Password Confirmation Modal (rendered once) --}}
@once
  <x-dialog-modal wire:model.live="confirmingPassword">
    <x-slot name="title">
      <div class="d-flex align-items-center">
        <i class="bi bi-shield-lock-fill me-2 fs-5"></i>
        {{ $title }}
      </div>
    </x-slot>

    <x-slot name="content">
      {{ $content }}

      {{-- Password Input Field --}}
      <div class="mt-3" x-data="{}"
        x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
        <x-input type="password" class="form-control form-control-sm {{ $errors->has('confirmable_password') ? 'is-invalid' : '' }}"
          placeholder="{{ __('Kata Laluan') }}" x-ref="confirmable_password" wire:model="confirmablePassword"
          wire:keydown.enter="confirmPassword" />

        <x-input-error for="confirmable_password" class="mt-2 small text-danger" />
      </div>
    </x-slot>

    <x-slot name="footer">
      {{-- Cancel Button --}}
      <x-secondary-button wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
        {{ __('Batal') }}
      </x-secondary-button>

      {{-- Confirm Button --}}
      <x-button class="ms-2" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled">
        <i class="bi bi-check-lg me-1"></i>
        {{ $button }}
      </x-button>
    </x-slot>
  </x-dialog-modal>
@endonce
