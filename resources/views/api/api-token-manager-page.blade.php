{{-- resources/views/api/api-token-manager-page.blade.php --}}
{{-- Renamed from api-token-manager.blade.php for clarity and consistency with naming convention --}}

<div>
    {{-- API Token Manager Section: use token-driven motac-card wrapper --}}
    <div class="motac-card mb-3">
        <div class="motac-card-header px-3 py-2">
            <x-form-section submit="createApiToken">
        <x-slot name="title">
            {{ __('Cipta Token API') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Token API membenarkan perkhidmatan pihak ketiga untuk mengesahkan dengan aplikasi kami bagi pihak anda.') }}
        </x-slot>

        <x-slot name="form">
            {{-- Success message shown when token is created --}}
            <x-action-message class="mb-3" on="created">
                <div class="alert alert-success d-flex align-items-center py-2">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ __('Berjaya Dicipta.') }}
                </div>
            </x-action-message>

            <div class="mb-3">
                <x-label for="name" class="form-label" value="{{ __('Nama Token') }}" />
                <x-input id="name" type="text" class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                    wire:model="createApiTokenForm.name" autofocus placeholder="{{ __('Cth: Token Servis Saya') }}" />
                <x-input-error for="name" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasPermissions())
                <div class="mb-3">
                    <x-label class="form-label" for="permissions" value="{{ __('Kebenaran') }}" />
                    <div class="mt-2 row g-2">
                        @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="form-check">
                                        <x-checkbox wire:model="createApiTokenForm.permissions"
                                            id="{{ 'create-' . $permission }}" :value="$permission" />
                                        <label class="form-check-label" for="{{ 'create-' . $permission }}">
                                            {{ $permission }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="actions">
            <x-button class="d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-1"></i>
                {{ __('Cipta') }}
            </x-button>
        </x-slot>
            </x-form-section>
        </div>
    </div>

    {{-- List all existing API tokens for the user --}}
    @if ($this->user->tokens->isNotEmpty())
        <hr class="my-4">
        <div class="mt-4">
            <div class="motac-card">
                <div class="motac-card-header px-3 py-2">
                    <x-action-section>
                <x-slot name="title">
                    {{ __('Urus Token API') }}
                </x-slot>
                <x-slot name="description">
                    {{ __('Anda boleh memadam mana-mana token sedia ada anda jika ia tidak diperlukan lagi.') }}
                </x-slot>
                <x-slot name="content">
                    <div class="list-group list-group-flush">
                        @foreach ($this->user->tokens->sortBy('name') as $token)
                            <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center px-0 py-2">
                                <div class="fw-medium text-dark">
                                    {{ $token->name }}
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($token->last_used_at)
                                        <div class="text-muted small">
                                            {{ __('Digunakan') }}:
                                            {{ $token->last_used_at->translatedFormat('d M Y, H:i') }}
                                        </div>
                                    @endif
                                    @if (Laravel\Jetstream\Jetstream::hasPermissions())
                                        <button
                                            class="btn btn-sm btn-link text-secondary text-decoration-none py-0 px-1"
                                            wire:click="manageApiTokenPermissions({{ $token->id }})"
                                            title="{{ __('Kebenaran') }}">
                                            <i class="bi bi-shield-check"></i>
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-link text-danger text-decoration-none py-0 px-1"
                                        wire:click="confirmApiTokenDeletion({{ $token->id }})"
                                        title="{{ __('Padam') }}">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
                    </x-action-section>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Display token when created --}}
    <x-dialog-modal wire:model.live="displayingToken">
        <x-slot name="title">
            <div class="d-flex align-items-center">
                <i class="bi bi-key-fill me-2 fs-5"></i>
                {{ __('Token API Baru Anda') }}
            </div>
        </x-slot>
        <x-slot name="content">
            <p>{{ __('Sila salin token API baharu anda. Untuk keselamatan anda, ia tidak akan dipaparkan lagi.') }}</p>
            <div class="mb-3">
                <x-input x-ref="plaintextToken" type="text" readonly :value="$plainTextToken"
                    class="form-control form-control-sm bg-light font-monospace"
                    autofocus autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                    @showing-token-modal.window="setTimeout(() => $refs.plaintextToken.select(), 250)" />
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('displayingToken', false)" wire:loading.attr="disabled">
                {{ __('Tutup') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal: Manage token permissions --}}
    <x-dialog-modal wire:model.live="managingApiTokenPermissions">
        <x-slot name="title">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-lock-fill me-2 fs-5"></i>
                {{ __('Kebenaran Token API') }}
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="mt-2 row g-2">
                @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                    <div class="col-md-6">
                        <div class="mb-2 form-check">
                            <x-checkbox wire:model="updateApiTokenForm.permissions" id="{{ 'update-' . $permission }}"
                                :value="$permission" />
                            <label class="form-check-label" for="{{ 'update-' . $permission }}">
                                {{ $permission }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$set('managingApiTokenPermissions', false)" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>
            <x-button class="ms-2 d-inline-flex align-items-center" wire:click="updateApiToken"
                wire:loading.attr="disabled">
                <i class="bi bi-save-fill me-1"></i>
                {{ __('Simpan') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal: Confirm token deletion --}}
    <x-confirmation-modal wire:model.live="confirmingApiTokenDeletion">
        <x-slot name="title">
            <div class="d-flex align-items-center text-danger">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                {{ __('Padam Token API') }}
            </div>
        </x-slot>
        <x-slot name="content">
            {{ __('Adakah anda pasti ingin memadam token API ini?') }}
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="$toggle('confirmingApiTokenDeletion')" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>
            <x-danger-button class="ms-2 d-inline-flex align-items-center" wire:loading.attr="disabled"
                wire:click="deleteApiToken">
                <i class="bi bi-trash3-fill me-1"></i>
                {{ __('Padam') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
