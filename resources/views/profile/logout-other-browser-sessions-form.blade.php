{{-- resources/views/profile/logout-other-browser-sessions-form.blade.php (MOTAC Bootstrap 5 Version) --}}
<div class="card shadow-sm motac-card">
    <div class="card-header bg-light py-3 motac-card-header">
        <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-display me-2"></i>{{ __('Sesi Pelayar Imbas Aktif') }}
        </h3>
    </div>
    <div class="card-body p-3 p-md-4">
        <p class="card-text text-muted small mb-3">
            {{ __('Urus dan log keluar sesi aktif anda pada pelayar imbas dan peranti lain. Jika perlu, anda boleh log keluar daripada semua sesi pelayar imbas anda yang lain merentas semua peranti anda. Jika anda merasakan akaun anda telah terjejas, anda juga patut mengemas kini kata laluan anda.') }}
        </p>

        @if (session()->has('status') && session('status_target') === $this->getId() . '.logoutOtherBrowserSessions')
            <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"
                    aria-label="{{ __('Tutup') }}"></button>
            </div>
        @endif

        @if (count($this->sessions) > 0)
            <div class="mt-3 list-group list-group-flush">
                @foreach ($this->sessions as $session)
                    <div class="list-group-item px-0 py-2 d-flex align-items-center">
                        <div class="me-3">
                            @if ($session->agent->isDesktop())
                                <i class="bi bi-display fs-3 text-muted"></i>
                            @else
                                <i class="bi bi-phone-fill fs-3 text-muted"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="small">
                                {{ $session->agent->platform() ? $session->agent->platform() : __('Tidak Diketahui') }}
                                -
                                {{ $session->agent->browser() ? $session->agent->browser() : __('Tidak Diketahui') }}
                            </div>
                            <div>
                                <div class="small text-muted">
                                    {{ $session->ip_address }},
                                    @if ($session->is_current_device)
                                        <span class="text-success fw-medium">{{ __('(Peranti ini)') }}</span>
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
    </div>
    <div class="card-footer bg-light text-end py-3 border-top">
        <button class="btn btn-primary motac-btn-primary" wire:click="confirmLogout" wire:loading.attr="disabled">
            <i class="bi bi-box-arrow-right me-1"></i>{{ __('Log Keluar Sesi Pelayar Imbas Lain') }}
        </button>
    </div>

    {{-- Logout Other Sessions Confirmation Modal --}}
    <div class="modal fade" id="confirmingLogoutModal-{{ $this->getId() }}" tabindex="-1"
        aria-labelledby="confirmingLogoutModalLabel-{{ $this->getId() }}" aria-hidden="true" wire:ignore.self
        x-data="{ show: @entangle('confirmingLogout').defer }" x-show="show" @hidden.bs.modal="show = false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmingLogoutModalLabel-{{ $this->getId() }}"><i
                            class="bi bi-shield-exclamation me-2"></i>{{ __('Log Keluar Sesi Pelayar Lain') }}</h5>
                    <button type="button" class="btn-close" @click="show = false"
                        aria-label="{{ __('Tutup') }}"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Sila masukkan kata laluan anda untuk mengesahkan bahawa anda ingin log keluar daripada sesi pelayar imbas anda yang lain merentas semua peranti anda.') }}
                    </p>
                    <div class="mt-3" x-data="{}"
                        x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password_logout.focus(), 250)">
                        <input type="password" placeholder="{{ __('Kata Laluan') }}" x-ref="password_logout"
                            class="form-control form-control-sm @error('password') is-invalid @enderror"
                            wire:model="password" wire:keydown.enter="logoutOtherBrowserSessions" />
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary motac-btn-outline" @click="show = false"
                        wire:loading.attr="disabled">
                        {{ __('Batal') }}
                    </button>
                    <button class="btn btn-danger ms-2" wire:click="logoutOtherBrowserSessions"
                        wire:loading.attr="disabled">
                        <i class="bi bi-box-arrow-right me-1"></i>{{ __('Log Keluar Sesi Lain') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div x-show="confirmingLogout" class="modal-backdrop fade show" id="backdropConfirmingLogout-{{ $this->getId() }}"
        style="display: none;"></div>
    {{-- Initialize modal with Alpine if wire:model.live is not sufficient or causes issues --}}
    <script>
        document.addEventListener('livewire:init', () => {
            let modalElement = document.getElementById('confirmingLogoutModal-{{ $this->getId() }}');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                Livewire.on('confirmingLogoutOtherBrowserSessions', () => {
                    modal.show();
                });
                Livewire.on('otherBrowserSessionsLoggedOut', () => {
                    modal.hide();
                }); // Assuming you dispatch this after success
                modalElement.addEventListener('hidden.bs.modal', function() {
                    @this.set('confirmingLogout', false); // Sync alpine state if closed via ESC or backdrop
                });
            }
        });
    </script>
</div>
