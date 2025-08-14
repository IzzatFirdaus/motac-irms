{{-- resources/views/profile/delete-user-form.blade.php (MOTAC Bootstrap 5 Version) --}}
<div class="card shadow-sm motac-card border-danger"> {{-- Added border-danger for emphasis --}}
    <div class="card-header bg-danger-subtle text-danger-emphasis py-3 motac-card-header"> {{-- Themed header --}}
        <h3 class="h5 card-title fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-trash3-fill me-2"></i>{{ __('Hapus Akaun Pengguna') }}
        </h3>
    </div>
    <div class="card-body p-3 p-md-4">
        <p class="card-text text-muted small mb-3">
            {{ __('Hapuskan akaun anda secara kekal.') }}
        </p>
        <div>
            <p class="small">
                {{ __('Setelah akaun anda dihapuskan, semua sumber dan data akan dihapuskan secara kekal. Sebelum menghapuskan akaun anda, sila muat turun sebarang data atau maklumat yang ingin anda simpan.') }}
            </p>
        </div>
    </div>
    <div class="card-footer bg-light text-end py-3 border-top">
        <button class="btn btn-danger" wire:click="confirmUserDeletion" wire:loading.attr="disabled">
            <i class="bi bi-trash3-fill me-1"></i>{{ __('Hapus Akaun Ini') }}
        </button>
    </div>

    {{-- Delete User Confirmation Modal --}}
    <div class="modal fade" id="confirmingUserDeletionModal-{{ $this->getId() }}" tabindex="-1"
        aria-labelledby="confirmingUserDeletionModalLabel-{{ $this->getId() }}" aria-hidden="true" wire:ignore.self
        x-data="{ show: @entangle('confirmingUserDeletion').defer }" x-show="show" @hidden.bs.modal="show = false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmingUserDeletionModalLabel-{{ $this->getId() }}"><i
                            class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Sahkan Pemadaman Akaun') }}</h5>
                    <button type="button" class="btn-close btn-close-white" @click="show = false"
                        aria-label="{{ __('Tutup') }}"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Adakah anda pasti ingin menghapuskan akaun anda? Setelah akaun anda dihapuskan, semua sumber dan data akan dihapuskan secara kekal. Sila masukkan kata laluan anda untuk mengesahkan bahawa anda ingin menghapuskan akaun anda secara kekal.') }}
                    </p>
                    <div class="mt-3" x-data="{}"
                        x-on:confirming-delete-user.window="setTimeout(() => $refs.password_delete_user.focus(), 250)">
                        <label for="password_delete_user-{{ $this->getId() }}"
                            class="form-label fw-medium">{{ __('Kata Laluan Semasa') }} <span
                                class="text-danger">*</span></label>
                        <input id="password_delete_user-{{ $this->getId() }}" type="password"
                            placeholder="{{ __('Kata Laluan') }}" x-ref="password_delete_user"
                            class="form-control form-control-sm @error('password') is-invalid @enderror"
                            wire:model="password" wire:keydown.enter="deleteUser" required />
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
                    <button class="btn btn-danger ms-2" wire:click="deleteUser" wire:loading.attr="disabled">
                        <i class="bi bi-trash3-fill me-1"></i>{{ __('Ya, Hapus Akaun Saya') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div x-show="confirmingUserDeletion" class="modal-backdrop fade show"
        id="backdropConfirmingUserDeletion-{{ $this->getId() }}" style="display: none;"></div>
    <script>
        document.addEventListener('livewire:init', () => {
            let modalElement = document.getElementById('confirmingUserDeletionModal-{{ $this->getId() }}');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                Livewire.on('confirmingUserDeletion', () => {
                    modal.show();
                }); // Assuming 'confirmingUserDeletion' is the event or use the wire:model directly
                modalElement.addEventListener('hidden.bs.modal', function() {
                    @this.set('confirmingUserDeletion', false);
                });
            }
        });
    </script>
</div>
