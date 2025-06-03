{{-- resources/views/profile/update-password-form.blade.php (MOTAC Bootstrap 5 Version) --}}
<div class="card shadow-sm motac-card">
    <div class="card-header bg-light py-3 motac-card-header">
        <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-key-fill me-2"></i>{{ __('Kemaskini Kata Laluan') }}
        </h3>
    </div>
    <form wire:submit.prevent="updatePassword">
        <div class="card-body p-3 p-md-4">
            <p class="card-text text-muted small mb-3">
                {{ __('Pastikan akaun anda menggunakan kata laluan yang panjang dan rawak untuk kekal selamat.') }}
            </p>

            <div wire:loading wire:target="updatePassword" class="alert alert-info small py-2 mb-3">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                {{ __('Menyimpan...') }}
            </div>
            @if (session()->has('status') && session('status_target') === $this->getId() . '.updatePassword')
                <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"
                        aria-label="{{ __('Tutup') }}"></button>
                </div>
            @endif


            <div class="mb-3">
                <label for="current_password-{{ $this->getId() }}"
                    class="form-label fw-medium">{{ __('Kata Laluan Semasa') }} <span
                        class="text-danger">*</span></label>
                <input id="current_password-{{ $this->getId() }}" type="password"
                    class="form-control form-control-sm @error('state.current_password') is-invalid @enderror"
                    wire:model="state.current_password" autocomplete="current-password" required />
                @error('state.current_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password-{{ $this->getId() }}" class="form-label fw-medium">{{ __('Kata Laluan Baru') }}
                    <span class="text-danger">*</span></label>
                <input id="password-{{ $this->getId() }}" type="password"
                    class="form-control form-control-sm @error('state.password') is-invalid @enderror"
                    wire:model="state.password" autocomplete="new-password" required />
                @error('state.password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text small">
                    {{ __('Gunakan sekurang-kurangnya 8 aksara. Gabungan huruf besar, huruf kecil, nombor dan simbol adalah digalakkan.') }}
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation-{{ $this->getId() }}"
                    class="form-label fw-medium">{{ __('Sahkan Kata Laluan Baru') }} <span
                        class="text-danger">*</span></label>
                <input id="password_confirmation-{{ $this->getId() }}" type="password"
                    class="form-control form-control-sm @error('state.password_confirmation') is-invalid @enderror"
                    wire:model="state.password_confirmation" autocomplete="new-password" required />
                @error('state.password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="card-footer bg-light text-end py-3 border-top">
            <button type="submit" class="btn btn-primary motac-btn-primary" wire:loading.attr="disabled"
                wire:target="updatePassword">
                <span wire:loading.remove wire:target="updatePassword">
                    <i class="bi bi-save-fill me-1"></i>{{ __('Simpan Kata Laluan') }}
                </span>
                <span wire:loading wire:target="updatePassword" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    {{ __('Menyimpan...') }}
                </span>
            </button>
        </div>
    </form>
</div>
