{{--
    NOTE: This is a Laravel Jetstream component styled with Tailwind CSS.
    For the MOTAC system, this requires a UI refactor to Bootstrap 5 and
    replacement of Jetstream x-components with MOTAC's Bootstrap components.
    Adjustments below primarily make static text translatable.
--}}
<x-action-section>
  <x-slot name="title">
    {{ __('Pengesahan Dua Faktor') }}
  </x-slot>

  <x-slot name="description">
    {{ __('Tambah keselamatan tambahan pada akaun anda menggunakan pengesahan dua faktor.') }}
  </x-slot>

  <x-slot name="content">
    <h6 class="fw-semibold"> {{-- Bootstrap font weight --}}
      @if ($this->enabled)
        @if ($showingConfirmation)
          {{ __('Anda sedang mengaktifkan pengesahan dua faktor.') }}
        @else
          {{ __('Anda telah mengaktifkan pengesahan dua faktor.') }}
        @endif
      @else
        {{ __('Anda belum mengaktifkan pengesahan dua faktor.') }}
      @endif
    </h6>

    <p class="card-text">
      {{ __('Apabila pengesahan dua faktor diaktifkan, anda akan diminta untuk token rawak yang selamat semasa pengesahan. Anda boleh mendapatkan token ini dari aplikasi Google Authenticator telefon anda.') }}
    </p>

    @if ($this->enabled)
      @if ($showingQrCode)
        <p class="card-text mt-2">
          @if ($showingConfirmation)
            {{ __('Imbas kod QR berikut menggunakan aplikasi pengesah telefon anda dan sahkan dengan kod OTP yang dijana.') }}
          @else
            {{ __('Pengesahan dua faktor kini diaktifkan. Imbas kod QR berikut menggunakan aplikasi pengesah telefon anda.') }}
          @endif
        </p>

        <div class="mt-2">
          {!! $this->user->twoFactorQrCodeSvg() !!}
        </div>

        <div class="mt-4">
            <p class="fw-medium">
              {{ __('Kunci Persediaan') }}: {{ decrypt($this->user->two_factor_secret) }}
            </p>
        </div>

        @if ($showingConfirmation)
          <div class="mt-2">
            <x-label for="code" value="{{ __('Kod') }}" />
            <x-input id="code" class="d-block mt-1 w-100" type="text" inputmode="numeric" name="code" autofocus autocomplete="one-time-code" {{-- Bootstrap utilities --}}
                wire:model="code"
                wire:keydown.enter="confirmTwoFactorAuthentication" />
            <x-input-error for="code" class="mt-1" /> {{-- Bootstrap margin top --}}
          </div>
        @endif
      @endif

      @if ($showingRecoveryCodes)
        <p class="card-text mt-2">
          {{ __('Simpan kod pemulihan ini dalam pengurus kata laluan yang selamat. Ia boleh digunakan untuk memulihkan akses ke akaun anda jika peranti pengesahan dua faktor anda hilang.') }}
        </p>

        <div class="bg-light rounded p-3 mt-2"> {{-- Bootstrap bg-light, rounded, p-3 --}}
          @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
            <div class="font-monospace">{{ $code }}</div> {{-- Bootstrap font-monospace --}}
          @endforeach
        </div>
      @endif
    @endif

    <div class="mt-3"> {{-- Bootstrap margin top --}}
      @if (!$this->enabled)
        <x-confirms-password wire:then="enableTwoFactorAuthentication">
          {{-- Assuming x-button is your MOTAC system's Bootstrap button --}}
          <x-button type="button" wire:loading.attr="disabled">
            {{ __('Aktifkan') }}
          </x-button>
        </x-confirms-password>
      @else
        @if ($showingRecoveryCodes)
          <x-confirms-password wire:then="regenerateRecoveryCodes">
            <x-secondary-button class="me-1"> {{-- Bootstrap margin end --}}
              {{ __('Jana Semula Kod Pemulihan') }}
            </x-secondary-button>
          </x-confirms-password>
        @elseif ($showingConfirmation)
          <x-confirms-password wire:then="confirmTwoFactorAuthentication">
            <x-button type="button" wire:loading.attr="disabled">
              {{ __('Sahkan') }}
            </x-button>
          </x-confirms-password>
        @else
          <x-confirms-password wire:then="showRecoveryCodes">
            <x-secondary-button class="me-1">
              {{ __('Tunjukkan Kod Pemulihan') }}
            </x-secondary-button>
          </x-confirms-password>
        @endif

        <x-confirms-password wire:then="disableTwoFactorAuthentication">
          {{-- Assuming x-danger-button is your MOTAC system's Bootstrap danger button --}}
          <x-danger-button wire:loading.attr="disabled" class="ms-1"> {{-- Bootstrap margin start --}}
            {{ __('Nyahaktifkan') }}
          </x-danger-button>
        </x-confirms-password>
      @endif
    </div>
  </x-slot>
</x-action-section>
