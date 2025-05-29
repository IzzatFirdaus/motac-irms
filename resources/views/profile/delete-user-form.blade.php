{{--
    NOTE: This is a Laravel Jetstream component styled with Tailwind CSS.
    For the MOTAC system, this requires a UI refactor to Bootstrap 5 and
    replacement of Jetstream x-components with MOTAC's Bootstrap components.
    Adjustments below primarily make static text translatable.
--}}
<x-action-section>
  <x-slot name="title">
    {{ __('Hapus Akaun') }}
  </x-slot>

  <x-slot name="description">
    {{ __('Hapuskan akaun anda secara kekal.') }}
  </x-slot>

  <x-slot name="content">
    <div>
      {{ __('Setelah akaun anda dihapuskan, semua sumber dan data akan dihapuskan secara kekal. Sebelum menghapuskan akaun anda, sila muat turun sebarang data atau maklumat yang ingin anda simpan.') }}
    </div>

    <div class="mt-3">
      <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
        {{ __('Hapus Akaun') }}
      </x-danger-button>
    </div>

    <x-dialog-modal wire:model.live="confirmingUserDeletion">
      <x-slot name="title">
        {{ __('Hapus Akaun') }}
      </x-slot>

      <x-slot name="content">
        {{ __('Adakah anda pasti ingin menghapuskan akaun anda? Setelah akaun anda dihapuskan, semua sumber dan data akan dihapuskan secara kekal. Sila masukkan kata laluan anda untuk mengesahkan bahawa anda ingin menghapuskan akaun anda secara kekal.') }}

        <div class="mt-2" x-data="{}"
          x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
          <x-input type="password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
            placeholder="{{ __('Kata Laluan') }}" x-ref="password" wire:model="password"
            wire:keydown.enter="deleteUser" />

          <x-input-error for="password" />
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
          {{ __('Batal') }}
        </x-secondary-button>

        <x-danger-button class="ms-1" wire:click="deleteUser" wire:loading.attr="disabled">
          {{ __('Hapus Akaun') }}
        </x-danger-button>
      </x-slot>
    </x-dialog-modal>
  </x-slot>
</x-action-section>
