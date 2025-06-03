{{-- resources/views/livewire/settings/roles/index.blade.php --}}
<div class="container mt-4">
    @section('title', __('Pengurusan Peranan & Kebenaran')) {{-- Manual title setting in Blade --}}

    <div class="row mb-3 align-items-center pb-2 border-bottom">
        <div class="col">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-shield-lock-fill me-2"></i>
                {{ __('Pengurusan Peranan Pengguna') }}
            </h1>
        </div>
        <div class="col text-end">
            {{-- Using the configured role model class for the can directive --}}
            @can('create', config('permission.models.role'))
            <button wire:click="create" class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold px-3 py-2 motac-btn-primary">
                <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Peranan Baru') }}
            </button>
            @endcan
        </div>
    </div>

    @include('_partials._alerts.alert-general')

    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
             <i class="bi bi-list-ul me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Senarai Peranan Sedia Ada') }}</h5>
        </div>
        <div class="card-body p-0 motac-card-body">
            @if ($roles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama Peranan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama Pelindung') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Kebenaran') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Pengguna') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dicipta Pada') }}</th>
                                <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                                <td colspan="7" class="p-0 border-0">
                                    <div wire:loading.flex class="progress bg-transparent rounded-0" style="height: 2px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                            role="progressbar" style="width: 100%"
                                            aria-label="{{ __('Memuatkan Data Peranan...') }}"></div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($roles as $role)
                                <tr wire:key="role-{{ $role->id }}">
                                    <td class="px-3 py-2 small">{{ $role->id }}</td>
                                    <td class="px-3 py-2 small text-dark fw-medium">{{ $role->name }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ $role->guard_name }}</td>
                                    <td class="px-3 py-2 small text-center text-muted">
                                        <span class="badge bg-info-subtle text-info-emphasis rounded-pill">{{ $role->permissions_count }}</span>
                                    </td>
                                    <td class="px-3 py-2 small text-center text-muted">
                                        <span class="badge bg-primary-subtle text-primary-emphasis rounded-pill">{{ $role->users_count }}</span>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $role->created_at->translatedFormat(config('app.date_format_my', 'd/m/Y') . ' H:i') }}</td>
                                    <td class="px-3 py-2 text-end">
                                        @can('update', $role)
                                        <button wire:click="edit({{ $role->id }})" class="btn btn-sm btn-icon btn-outline-primary border-0 me-1 motac-btn-icon" title="{{ __('Kemaskini') }}">
                                            <i class="bi bi-pencil-fill fs-6"></i>
                                        </button>
                                        @endcan
                                        @can('delete', $role)
                                        <button wire:click="confirmRoleDeletion({{ $role->id }})" class="btn btn-sm btn-icon btn-outline-danger border-0 motac-btn-icon" title="{{ __('Padam') }}"
                                            @if(in_array($role->name, $this->coreRoles)) disabled @endif >
                                            <i class="bi bi-trash3-fill fs-6"></i>
                                        </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($roles->hasPages())
                    <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
                        {{ $roles->links() }}
                    </div>
                @endif
            @else
                <div class="alert alert-info text-center m-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ __('Tiada peranan ditemui dalam sistem.') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Create/Edit Role Modal --}}
    @if ($showModal)
    <div wire:ignore.self class="modal fade" id="roleFormModal" tabindex="-1" aria-labelledby="roleFormModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content motac-modal-content">
                <form wire:submit.prevent="saveRole">
                    <div class="modal-header motac-modal-header">
                        <h5 class="modal-title d-flex align-items-center" id="roleFormModalLabel">
                            <i class="bi {{ $isEditMode ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-2"></i>
                            {{ $isEditMode ? __('Kemaskini Peranan') : __('Tambah Peranan Baru') }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body motac-modal-body">
                        <div class="mb-3">
                            <label for="roleNameModalInput" class="form-label fw-medium">{{ __('Nama Peranan') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="roleNameModalInput" wire:model.defer="name" placeholder="{{ __('Cth: Pengurus Kelulusan') }}">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">{{ __('Kebenaran') }}</label>
                            <div class="row px-2 border rounded py-2" style="max-height: 300px; overflow-y: auto;">
                                @if(is_array($allPermissionsForView) && count($allPermissionsForView) > 0)
                                    @foreach ($allPermissionsForView as $id => $permissionName)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ $id }}" id="permission_{{ $id }}_modal" wire:model.defer="selectedPermissions">
                                            <label class="form-check-label small" for="permission_{{ $id }}_modal">
                                                {{ $permissionName }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted small">{{__('Tiada kebenaran sistem ditemui. Sila jalankan `permission:sync` atau tambah kebenaran.')}}</p>
                                @endif
                            </div>
                             @error('selectedPermissions') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                             @error('selectedPermissions.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer motac-modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading wire:target="saveRole" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <i wire:loading.remove wire:target="saveRole" class="bi bi-check-lg me-1"></i>
                            {{ $isEditMode ? __('Simpan Perubahan') : __('Cipta Peranan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteConfirmationModal)
    <div wire:ignore.self class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content motac-modal-content">
                <div class="modal-header motac-modal-header">
                    <h5 class="modal-title d-flex align-items-center" id="deleteRoleModalLabel">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        {{ __('Sahkan Pemadaman Peranan') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeDeleteConfirmationModal" aria-label="Close"></button>
                </div>
                <div class="modal-body motac-modal-body">
                     <p>{{ __('Adakah anda pasti ingin memadam peranan') }} "<strong>{{ $roleNameToDelete }}</strong>"?</p>
                     <p class="small text-danger">{{ __('Tindakan ini tidak boleh diterbalikkan.') }}</p>
                </div>
                <div class="modal-footer motac-modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDeleteConfirmationModal">{{ __('Batal') }}</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteRole">
                        <span wire:loading wire:target="deleteRole" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        <i wire:loading.remove wire:target="deleteRole" class="bi bi-trash3-fill me-1"></i>
                        {{ __('Padam') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        const roleModalEl = document.getElementById('roleFormModal');
        const deleteRoleModalEl = document.getElementById('deleteRoleModal');
        let roleModalInstance = null;
        let deleteRoleModalInstance = null;

        if(roleModalEl) {
            roleModalInstance = new bootstrap.Modal(roleModalEl);
            roleModalEl.addEventListener('hidden.bs.modal', event => {
                if(@this.get('showModal')) {
                    @this.call('closeModal');
                }
            });
        }
        if(deleteRoleModalEl) {
            deleteRoleModalInstance = new bootstrap.Modal(deleteRoleModalEl);
             deleteRoleModalEl.addEventListener('hidden.bs.modal', event => {
                if(@this.get('showDeleteConfirmationModal')) {
                     @this.call('closeDeleteConfirmationModal');
                }
            });
        }

        @this.on('showRoleModal', () => {
            if(roleModalInstance) roleModalInstance.show();
        });
        @this.on('hideRoleModal', () => {
            if(roleModalInstance && bootstrap.Modal.getInstance(roleModalEl)?._isShown) {
                roleModalInstance.hide();
            }
        });
        @this.on('showDeleteRoleConfirmationModal', () => {
            if(deleteRoleModalInstance) deleteRoleModalInstance.show();
        });
        @this.on('hideDeleteRoleConfirmationModal', () => {
            if(deleteRoleModalInstance && bootstrap.Modal.getInstance(deleteRoleModalEl)?._isShown) {
                deleteRoleModalInstance.hide();
            }
        });
    });
</script>
@endpush
