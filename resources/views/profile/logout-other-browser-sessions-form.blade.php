{{--
    NOTE: This is a Laravel Jetstream component styled with Tailwind CSS.
    For the MOTAC system, this requires a UI refactor to Bootstrap 5 and
    replacement of Jetstream x-components with MOTAC's Bootstrap components.
    Adjustments below primarily make static text translatable.
--}}
<x-action-section>
  <x-slot name="title">
    {{ __('Sesi Pelayar Imbas') }}
  </x-slot>

  <x-slot name="description">
    {{ __('Urus dan log keluar sesi aktif anda pada pelayar imbas dan peranti lain.') }}
  </x-slot>

  <x-slot name="content">
    <x-action-message on="loggedOut">
      {{ __('Selesai.') }}
    </x-action-message>

    <p class="card-text">
      {{ __('Jika perlu, anda boleh log keluar daripada semua sesi pelayar imbas anda yang lain merentas semua peranti anda. Beberapa sesi terkini anda disenaraikan di bawah; walau bagaimanapun, senarai ini mungkin tidak lengkap. Jika anda merasakan akaun anda telah terjejas, anda juga patut mengemas kini kata laluan anda.') }}
    </p>

    @if (count($this->sessions) > 0)
      <div class="mt-3">
        @foreach ($this->sessions as $session)
          <div class="d-flex align-items-center mb-3"> {{-- Bootstrap align-items-center and mb-3 --}}
            <div>
              @if ($session->agent->isDesktop())
                <svg fill="none" width="32" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  viewBox="0 0 24 24" stroke="currentColor" class="text-muted">
                  <path
                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                  </path>
                </svg>
              @else
                <svg xmlns="http://www.w3.org/2000/svg" width="32" viewBox="0 0 24 24" stroke-width="2"
                  stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"
                  class="text-muted">
                  <path d="M0 0h24v24H0z" stroke="none"></path>
                  <rect x="7" y="4" width="10" height="16" rx="1"></rect>
                  <path d="M11 5h2M12 17v.01"></path>
                </svg>
              @endif
            </div>

            <div class="ms-2"> {{-- Bootstrap ms-2 --}}
              <div>
                {{ $session->agent->platform() ? $session->agent->platform() : __('Tidak Diketahui') }} -
                {{ $session->agent->browser() ? $session->agent->browser() : __('Tidak Diketahui') }}
              </div>

              <div>
                <div class="small text-muted">
                  {{ $session->ip_address }},

                  @if ($session->is_current_device)
                    <span class="text-success fw-medium">{{ __('Peranti ini') }}</span>
                  @else
                    {{ __('Aktif terakhir') }} {{ $session->last_active }}
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    <div class="d-flex mt-3">
      {{-- Assuming x-button is your MOTAC system's Bootstrap button component --}}
      <x-button wire:click="confirmLogout" wire:loading.attr="disabled">
        {{ __('Log Keluar Sesi Pelayar Imbas Lain') }}
      </x-button>
    </div>

    <x-dialog-modal wire:model.live="confirmingLogout">
      <x-slot name="title">
        {{ __('Log Keluar Sesi Pelayar Imbas Lain') }}
      </x-slot>

      <x-slot name="content">
        {{ __('Sila masukkan kata laluan anda untuk mengesahkan bahawa anda ingin log keluar daripada sesi pelayar imbas anda yang lain merentas semua peranti anda.') }}

        <div class="mt-3" x-data="{}"
          x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
          <x-input type="password" placeholder="{{ __('Kata Laluan') }}" x-ref="password"
            class="{{ $errors->has('password') ? 'is-invalid' : '' }}" wire:model="password"
            wire:keydown.enter="logoutOtherBrowserSessions" />

          <x-input-error for="password" class="mt-2" />
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
          {{ __('Batal') }}
        </x-secondary-button>

        {{-- This should use x-danger-button if available and styled for Bootstrap --}}
        <button class="btn btn-danger ms-1 text-uppercase" wire:click="logoutOtherBrowserSessions"
          wire:loading.attr="disabled">
          {{ __('Log Keluar Sesi Pelayar Imbas Lain') }}
        </button>
      </x-slot>
    </x-dialog-modal>
  </x-slot>
</x-action-section>
