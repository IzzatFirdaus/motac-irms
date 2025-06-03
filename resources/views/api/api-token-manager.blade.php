<div>
    {{-- x-form-section should be styled like a .card with .card-header, .card-body, .card-footer in MOTAC theme --}}
    <x-form-section submit="createApiToken">
        <x-slot name="title">
            {{ __('Cipta Token API') }}
        </x-slot>

        <x-slot name="description">
            {{ __('Token API membenarkan perkhidmatan pihak ketiga untuk mengesahkan dengan aplikasi kami bagi pihak anda.') }}
        </x-slot>

        <x-slot name="form">
            {{-- x-action-message should be styled like an .alert .alert-success --}}
            <x-action-message class="mb-3" on="created"> {{-- Added mb-3 for spacing --}}
                <div class="alert alert-success d-flex align-items-center py-2"> {{-- Example Bootstrap alert styling --}}
                    <i class="bi bi-check-circle-fill me-2"></i> {{ __('Berjaya Dicipta.') }}
                </div>
            </x-action-message>

            <div class="mb-3">
                {{-- x-label should render as <label class="form-label fw-medium"> --}}
                <x-label for="name" class="form-label" value="{{ __('Nama Token') }}" />
                {{-- x-input should render as <input class="form-control form-control-sm"> and pick up MOTAC theme --}}
                <x-input id="name" type="text" class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                    wire:model="createApiTokenForm.name" autofocus placeholder="{{ __('Cth: Token Servis Saya') }}" />
                <x-input-error for="name" /> {{-- Should render as .invalid-feedback --}}
            </div>

            @if (Laravel\Jetstream\Jetstream::hasPermissions())
                <div class="mb-3"> {{-- Added mb-3 --}}
                    <x-label class="form-label" for="permissions" value="{{ __('Kebenaran') }}" />

                    <div class="mt-2 row g-2"> {{-- Added g-2 for spacing --}}
                        @foreach (Laravel\Jetstream\Jetstream::$permissions as $permission)
                            <div class="col-md-6"> {{-- Changed to col-md-6 for better layout on medium screens --}}
                                <div class="mb-2"> {{-- Reduced mb-3 to mb-2 for tighter list --}}
                                    <div class="form-check"> {{-- Standard Bootstrap form-check --}}
                                        {{-- x-checkbox should render as <input type="checkbox" class="form-check-input"> --}}
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
            {{-- x-button should render as <button class="btn btn-primary"> (MOTAC Blue) --}}
            <x-button class="d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-1"></i> {{-- Bootstrap Icon --}}
                {{ __('Cipta') }}
            </x-button>
        </x-slot>
    </x-form-section>

    @if ($this->user->tokens->isNotEmpty())
        <hr class="my-4"> {{-- Added hr for separation --}}
        <div class="mt-4">
            {{-- x-action-section similar to x-form-section, should be styled like a .card --}}
            <x-action-section>
                <x-slot name="title">
                    {{ __('Urus Token API') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Anda boleh memadam mana-mana token sedia ada anda jika ia tidak diperlukan lagi.') }}
                </x-slot>

                <x-slot name="content">
                    <div class="list-group list-group-flush"> {{-- Using Bootstrap list-group for a cleaner list --}}
                        @foreach ($this->user->tokens->sortBy('name') as $token)
                            <div
                                class="list-group-item d-flex flex-wrap justify-content-between align-items-center px-0 py-2">
                                <div class="fw-medium text-dark"> {{-- text-dark for better emphasis --}}
                                    {{ $token->name }}
                                </div>

                                <div class="d-flex align-items-center gap-2"> {{-- Added gap-2 for spacing --}}
                                    @if ($token->last_used_at)
                                        <div class="text-muted small">
                                            {{ __('Digunakan') }}:
                                            {{ $token->last_used_at->translatedFormat('d M Y, H:i') }}
                                            {{-- More specific format --}}
                                        </div>
                                    @endif

                                    @if (Laravel\Jetstream\Jetstream::hasPermissions())
                                        {{-- Button should be styled like .btn .btn-sm .btn-link .text-secondary --}}
                                        <button
                                            class="btn btn-sm btn-link text-secondary text-decoration-none py-0 px-1"
                                            wire:click="manageApiTokenPermissions({{ $token->id }})"
                                            title="{{ __('Kebenaran') }}">
                                            <i class="bi bi-shield-check"></i> {{-- Bootstrap Icon --}}
                                            {{-- __('Permissions') --}} {{-- Text can be hidden for icon-only button on small spaces --}}
                                        </button>
                                    @endif

                                    {{-- Button should be styled like .btn .btn-sm .btn-link .text-danger --}}
                                    <button class="btn btn-sm btn-link text-danger text-decoration-none py-0 px-1"
                                        wire:click="confirmApiTokenDeletion({{ $token->id }})"
                                        title="{{ __('Padam') }}">
                                        <i class="bi bi-trash3"></i> {{-- Bootstrap Icon --}}
                                        {{-- __('Delete') --}}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
            </x-action-section>
        </div>
    @endif

    {{-- x-dialog-modal should be styled as a Bootstrap .modal --}}
    <x-dialog-modal wire:model.live="displayingToken">
        <x-slot name="title">
            <div class="d-flex align-items-center"> {{-- For icon alignment --}}
                <i class="bi bi-key-fill me-2 fs-5"></i> {{-- Bootstrap Icon --}}
                {{ __('Token API Baru Anda') }}
            </div>
        </x-slot>

        <x-slot name="content">
            <p>{{ __('Sila salin token API baharu anda. Untuk keselamatan anda, ia tidak akan dipaparkan lagi.') }}</p>

            <div class="mb-3">
                {{-- x-input should be <input class="form-control form-control-sm bg-light font-monospace"> for this use case --}}
                <x-input x-ref="plaintextToken" type="text" readonly :value="$plainTextToken"
                    class="form-control form-control-sm bg-light font-monospace" {{-- Added Bootstrap classes for better styling --}} autofocus
                    autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                    @showing-token-modal.window="setTimeout(() => $refs.plaintextToken.select(), 250)" />
            </div>
        </x-slot>

        <x-slot name="footer">
            {{-- x-secondary-button should be <button class="btn btn-outline-secondary"> (MOTAC Themed) --}}
            <x-secondary-button wire:click="$set('displayingToken', false)" wire:loading.attr="disabled">
                {{ __('Tutup') }}
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model.live="managingApiTokenPermissions">
        <x-slot name="title">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-lock-fill me-2 fs-5"></i> {{-- Bootstrap Icon --}}
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

            {{-- x-button should be <button class="btn btn-primary"> (MOTAC Blue) --}}
            <x-button class="ms-2 d-inline-flex align-items-center" wire:click="updateApiToken"
                wire:loading.attr="disabled">
                <i class="bi bi-save-fill me-1"></i> {{-- Bootstrap Icon --}}
                {{ __('Simpan') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- x-confirmation-modal should be styled as a Bootstrap .modal with a warning/danger theme for title --}}
    <x-confirmation-modal wire:model.live="confirmingApiTokenDeletion">
        <x-slot name="title">
            <div class="d-flex align-items-center text-danger"> {{-- Danger color for title --}}
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> {{-- Bootstrap Icon --}}
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

            {{-- x-danger-button should be <button class="btn btn-danger"> (MOTAC Themed) --}}
            <x-danger-button class="ms-2 d-inline-flex align-items-center" wire:loading.attr="disabled"
                wire:click="deleteApiToken">
                <i class="bi bi-trash3-fill me-1"></i> {{-- Bootstrap Icon --}}
                {{ __('Padam') }}
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
