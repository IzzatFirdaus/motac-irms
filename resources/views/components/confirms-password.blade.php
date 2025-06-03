{{-- resources/views/components/confirms-password.blade.php --}}
@props(['title' => __('Sahkan Kata Laluan'), 'content' => __('Untuk keselamatan anda, sila sahkan kata laluan anda untuk meneruskan.'), 'button' => __('Sahkan')])

@php
$confirmableId = md5($attributes->wire('then')->value());
@endphp

<span {{ $attributes->wire('then') }} x-data x-ref="span"
  x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
  x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);">
  {{ $slot }}
</span>

@once
  {{-- Ensure x-dialog-modal and its child x-components are styled to match MOTAC Bootstrap 5 theme --}}
  <x-dialog-modal wire:model.live="confirmingPassword">
    <x-slot name="title">
      <div class="d-flex align-items-center">
        <i class="bi bi-shield-lock-fill me-2 fs-5"></i> {{-- Bootstrap Icon --}}
        {{ $title }}
      </div>
    </x-slot>

    <x-slot name="content">
      {{ $content }}

      <div class="mt-3" x-data="{}"
        x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
        {{-- x-input should render a .form-control. Adding classes for guidance. --}}
        <x-input type="password" class="form-control form-control-sm {{ $errors->has('confirmable_password') ? 'is-invalid' : '' }}"
          placeholder="{{ __('Kata Laluan') }}" x-ref="confirmable_password" wire:model="confirmablePassword"
          wire:keydown.enter="confirmPassword" />

        {{-- x-input-error should render as .invalid-feedback --}}
        <x-input-error for="confirmable_password" class="mt-2 small text-danger" />
      </div>
    </x-slot>

    <x-slot name="footer">
      {{-- x-secondary-button should render as .btn .btn-outline-secondary (MOTAC Themed) --}}
      <x-secondary-button wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
        {{ __('Batal') }}
      </x-secondary-button>

      {{-- x-button should render as .btn .btn-primary (MOTAC Blue) --}}
      <x-button class="ms-2" dusk="confirm-password-button" wire:click="confirmPassword" wire:loading.attr="disabled">
        <i class="bi bi-check-lg me-1"></i>{{-- Bootstrap Icon --}}
        {{ $button }}
      </x-button>
    </x-slot>
  </x-dialog-modal>
@endonce
