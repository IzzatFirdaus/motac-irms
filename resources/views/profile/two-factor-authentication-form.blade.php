{{-- resources/views/profile/two-factor-authentication-form.blade.php (MOTAC Bootstrap 5 Version) --}}
<div class="card shadow-sm motac-card">
    <div class="card-header bg-light py-3 motac-card-header">
        <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-shield-lock-fill me-2"></i>{{ __('Pengesahan Dua Faktor (2FA)') }}
        </h3>
    </div>
    <div class="card-body p-3 p-md-4">
        <p class="card-text text-muted small mb-3">
            {{ __('Tambah lapisan keselamatan tambahan pada akaun anda menggunakan pengesahan dua faktor.') }}
        </p>

        <h6 class="fw-semibold">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ __('Anda sedang dalam proses mengaktifkan pengesahan dua faktor.') }}
                @else
                    {{ __('Anda telah mengaktifkan pengesahan dua faktor.') }}
                @endif
            @else
                {{ __('Anda belum mengaktifkan pengesahan dua faktor.') }}
            @endif
        </h6>

        <p class="small text-muted mt-2">
            {{ __('Apabila pengesahan dua faktor diaktifkan, anda akan diminta token rawak yang selamat semasa pengesahan. Anda boleh mendapatkan token ini dari aplikasi pengesah seperti Google Authenticator di telefon anda.') }}
        </p>

        @if ($this->enabled)
            @if ($showingQrCode)
                <p class="small text-muted mt-3">
                    @if ($showingConfirmation)
                        {{ __('Untuk menyelesaikan pengaktifan, sila imbas kod QR berikut menggunakan aplikasi pengesah telefon anda atau masukkan kunci persediaan dan kemudian masukkan kod OTP yang dijana.') }}
                    @else
                        {{ __('Pengesahan dua faktor kini diaktifkan. Imbas kod QR berikut menggunakan aplikasi pengesah telefon anda atau masukkan kunci persediaan.') }}
                    @endif
                </p>

                <div class="mt-3 text-center"> {{-- Centered QR Code --}}
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-3">
                    <p class="fw-medium small">{{ __('Kunci Persediaan (Setup Key)') }}: <code
                            class="font-monospace bg-light p-1 rounded">{{ decrypt($this->user->two_factor_secret) }}</code>
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-3">
                        <label for="code-{{ $this->getId() }}"
                            class="form-label fw-medium">{{ __('Kod Pengesahan (OTP)') }}</label>
                        <input id="code-{{ $this->getId() }}"
                            class="form-control form-control-sm @error('code') is-invalid @enderror" type="text"
                            inputmode="numeric" name="code" autofocus autocomplete="one-time-code" wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <p class="small text-muted mt-3">
                    {{ __('Simpan kod pemulihan ini dalam pengurus kata laluan yang selamat. Ia boleh digunakan untuk memulihkan akses ke akaun anda jika peranti pengesahan dua faktor anda hilang.') }}
                </p>

                <div class="bg-light rounded p-3 mt-2 font-monospace small">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
    <div class="card-footer bg-light text-end py-3 border-top">
        @if (!$this->enabled)
            <x-confirms-password wire:then="enableTwoFactorAuthentication">
                <button type="button" class="btn btn-primary motac-btn-primary" wire:loading.attr="disabled">
                    <i class="bi bi-shield-check me-1"></i>{{ __('Aktifkan 2FA') }}
                </button>
            </x-confirms-password>
        @else
            @if ($showingRecoveryCodes)
                <x-confirms-password wire:then="regenerateRecoveryCodes">
                    <button class="btn btn-outline-secondary motac-btn-outline me-2">
                        <i class="bi bi-arrow-repeat me-1"></i>{{ __('Jana Semula Kod Pemulihan') }}
                    </button>
                </x-confirms-password>
            @elseif ($showingConfirmation)
                <x-confirms-password wire:then="confirmTwoFactorAuthentication">
                    <button type="button" class="btn btn-primary motac-btn-primary" wire:loading.attr="disabled">
                        <i class="bi bi-check-circle-fill me-1"></i>{{ __('Sahkan Pengaktifan') }}
                    </button>
                </x-confirms-password>
            @else
                <x-confirms-password wire:then="showRecoveryCodes">
                    <button class="btn btn-outline-secondary motac-btn-outline me-2">
                        <i class="bi bi-list-stars me-1"></i>{{ __('Tunjukkan Kod Pemulihan') }}
                    </button>
                </x-confirms-password>
            @endif

            <x-confirms-password wire:then="disableTwoFactorAuthentication">
                <button class="btn btn-danger" wire:loading.attr="disabled"> {{-- Standard btn-danger will be MOTAC themed --}}
                    <i class="bi bi-shield-slash-fill me-1"></i>{{ __('Nyahaktifkan 2FA') }}
                </button>
            </x-confirms-password>
        @endif
    </div>
</div>
