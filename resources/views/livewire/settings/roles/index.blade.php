{{-- Corrected: resources/views/livewire/settings/roles/index.blade.php --}}
{{-- This is the direct view for the App\Livewire\Settings\Roles Livewire component --}}
{{-- It should have only ONE root HTML element. --}}
<div class="container mt-4"> {{-- SINGLE ROOT ELEMENT --}}
    <div class="row mb-3">
        <div class="col">
            <h1 class="h3">{{ __('Pengurusan Peranan Pengguna') }}</h1>
        </div>
        <div class="col text-end">
            <button wire:click="create" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> {{ __('Tambah Peranan Baru') }}
            </button>
        </div>
    </div>

    {{-- Include alerts partial if you have one for session messages --}}
    @include('_partials._alerts.alert-general') {{-- Make sure this partial exists and uses Bootstrap --}}

    <div class="card shadow-sm">
        <div class="card-body">
            @if ($roles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Nama Peranan') }}</th>
                                <th>{{ __('Nama Pelindung') }}</th>
                                <th class="text-center">{{ __('Kebenaran') }}</th>
                                <th class="text-center">{{ __('Pengguna') }}</th>
                                <th>{{ __('Dicipta Pada') }}</th>
                                <th class="text-end">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                                <tr wire:key="role-{{ $role->id }}">
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->guard_name }}</td>
                                    <td class="text-center">{{ $role->permissions_count }}</td>
                                    <td class="text-center">{{ $role->users_count }}</td>
                                    <td>{{ $role->created_at->format(config('app.date_format_my', 'd/m/Y') . ' H:i') }}</td>
                                    <td class="text-end">
                                        <button wire:click="edit({{ $role->id }})" class="btn btn-sm btn-outline-primary me-1" title="{{ __('Kemaskini') }}">
                                            <i class="ti ti-pencil"></i>
                                        </button>
                                        <button wire:click="confirmRoleDeletion({{ $role->id }})" class="btn btn-sm btn-outline-danger" title="{{ __('Padam') }}"
                                            @if(in_array($role->name, $this->coreRoles ?? [])) disabled @endif > {{-- Accessing coreRoles from component --}}
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($roles->hasPages())
                    <div class="mt-3">
                        {{ $roles->links() }}
                    </div>
                @endif
            @else
                <div class="alert alert-info text-center">
                    {{ __('Tiada peranan ditemui dalam sistem.') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Create/Edit Role Modal --}}
    @if ($showModal)
    <div class="modal fade show d-block" id="roleFormModal" tabindex="-1" aria-labelledby="roleFormModalLabel" aria-modal="true" role="dialog" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit.prevent="saveRole">
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleFormModalLabel">
                            {{ $isEditMode ? __('Kemaskini Peranan') : __('Tambah Peranan Baru') }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Nama Peranan') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model.defer="name" placeholder="{{ __('Cth: Pengurus Kelulusan') }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('Kebenaran') }}</label>
                            <div class="row px-2" style="max-height: 300px; overflow-y: auto;">
                                @if(is_array($allPermissionsForView) && count($allPermissionsForView) > 0)
                                    @foreach ($allPermissionsForView as $id => $permissionName)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $id }}" id="permission_{{ $id }}" wire:model.defer="selectedPermissions">
                                            <label class="form-check-label" for="permission_{{ $id }}">
                                                {{ $permissionName }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">{{__('Tiada kebenaran ditemui.')}}</p>
                                @endif
                            </div>
                             @error('selectedPermissions') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                             @error('selectedPermissions.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading wire:target="saveRole" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            {{ $isEditMode ? __('Simpan Perubahan') : __('Cipta Peranan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="display: @if($showModal) block @else none @endif;"></div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteConfirmationModal)
    <div class="modal fade show d-block" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-modal="true" role="dialog" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRoleModalLabel">{{ __('Sahkan Pemadaman Peranan') }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showDeleteConfirmationModal', false)" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Adakah anda pasti ingin memadam peranan') }} "<strong>{{ $roleNameToDelete }}</strong>"? {{ __('Tindakan ini tidak boleh diterbalikkan.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="$set('showDeleteConfirmationModal', false)">{{ __('Batal') }}</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteRole">
                        <span wire:loading wire:target="deleteRole" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        {{ __('Padam') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" style="display: @if($showDeleteConfirmationModal) block @else none @endif;"></div>
    @endif

    {{-- Removed @push('scripts') as it should be in the main layout (layouts.app) if this is a full-page component view --}}
    {{-- If you need scripts specific to this component that should go at the end of the body,
         ensure your main layout (layouts.app) has a @stack('scripts') directive before the closing </body> tag.
         Then you can use @push('scripts') here. However, for Livewire direct views, inline <script> tags or
         dispatching browser events from the component are often preferred for component-specific JS.
         The existing script for modal handling relies on Bootstrap being globally available.
    --}}
    {{-- The @push for scripts should ideally be inside the single root element if absolutely necessary here,
         or better yet, handled by events or placed in the main layout.
         For simplicity and to ensure a single root, I'm removing it here, assuming modal JS handled globally or via events.
         The previous JS might still cause issues if it's outside the single root element.
         Let's assume the modal CSS (`d-block`, backdrop) is sufficient for Livewire to toggle.
         The JS provided earlier for modal control using `Livewire.hook` should be in your main `app.js` or layout file.
    --}}
</div> {{-- END OF SINGLE ROOT ELEMENT --}}
