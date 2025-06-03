{{-- resources/views/livewire/settings/permissions/index.blade.php --}}
{{-- This view is the template for the App\Livewire\Settings\Permissions Livewire component. --}}
<div>
    @section('title', __('Pengurusan Kebenaran Sistem'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            {{-- Iconography: Design Language 2.4 --}}
            <i class="bi bi-key-fill me-2"></i>
            {{ __('Pengurusan Kebenaran Sistem') }}
        </h1>
        @can('manage_permissions') {{-- Assuming a general permission for creating --}}
            <button wire:click="create" {{-- Assuming 'create' method in Livewire component opens the modal --}}
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2 motac-btn-primary"> {{-- Ensure .motac-btn-primary or themed .btn-primary --}}
                <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Kebenaran Baru') }}
            </button>
        @endcan
    </div>

    @include('_partials._alerts.alert-general') {{-- Ensure this uses MOTAC themed alerts --}}

    {{-- Permissions Table Card --}}
    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-list-check me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Senarai Kebenaran Sedia Ada') }}</h5>
        </div>
        <div class="card-body p-0 motac-card-body"> {{-- p-0 if table is flush --}}
            @if ($permissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle"> {{-- Bootstrap table classes --}}
                        <thead class="table-light"> {{-- Ensure table-light header is MOTAC themed --}}
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama Kebenaran') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Nama Pelindung') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Dicipta Pada') }}</th>
                                <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                                <td colspan="5" class="p-0 border-0">
                                    <div wire:loading.flex class="progress bg-transparent rounded-0" style="height: 2px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" {{-- Ensure bg-primary uses MOTAC primary color --}}
                                            role="progressbar" style="width: 100%"
                                            aria-label="{{ __('Memuatkan Data Kebenaran...') }}"></div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($permissions as $permission)
                                <tr wire:key="permission-{{ $permission->id }}">
                                    <td class="px-3 py-2 small text-muted">{{ $permission->id }}</td>
                                    <td class="px-3 py-2 small text-dark fw-medium">{{ $permission->name }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ $permission->guard_name }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ $permission->created_at->translatedFormat(config('app.date_format_my', 'd/m/Y') . ' H:i') }}</td>
                                    <td class="px-3 py-2 text-end">
                                        @can('manage_permissions') {{-- Or specific update/delete permissions --}}
                                        <button wire:click="edit({{ $permission->id }})"
                                            class="btn btn-sm btn-icon btn-outline-primary border-0 me-1 motac-btn-icon" title="{{ __('Kemaskini') }}">
                                            <i class="bi bi-pencil-fill fs-6"></i>
                                        </button>
                                        <button wire:click="confirmPermissionDeletion({{ $permission->id }})"
                                            class="btn btn-sm btn-icon btn-outline-danger border-0 motac-btn-icon" title="{{ __('Padam') }}">
                                            <i class="bi bi-trash3-fill fs-6"></i>
                                        </button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($permissions->hasPages())
                    <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
                        {{ $permissions->links() }} {{-- Ensure pagination is Bootstrap 5 styled and MOTAC themed --}}
                    </div>
                @endif
            @else
                <div class="alert alert-info text-center m-3"> {{-- Ensure alert-info is MOTAC themed --}}
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ __('Tiada kebenaran sistem ditemui.') }}
                    @can('manage_permissions')
                         <button wire:click="create" class="btn btn-sm btn-primary mt-2 ms-2">
                            <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Kebenaran Baru') }}
                        </button>
                    @endcan
                </div>
            @endif
        </div>
    </div>

    {{-- Create/Edit Permission Modal (Structure adapted from Roles modal) --}}
    @if ($showModal)
    <div wire:ignore.self class="modal fade" id="permissionFormModal" tabindex="-1" aria-labelledby="permissionFormModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content motac-modal-content">
                <form wire:submit.prevent="savePermission">
                    <div class="modal-header motac-modal-header">
                        <h5 class="modal-title d-flex align-items-center" id="permissionFormModalLabel">
                             <i class="bi {{ $isEditMode ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-2"></i>
                            {{ $isEditMode ? __('Kemaskini Kebenaran') : __('Tambah Kebenaran Baru') }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body motac-modal-body">
                        <div class="mb-3">
                            <label for="permissionName" class="form-label fw-medium">{{ __('Nama Kebenaran') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="permissionName" wire:model.defer="name" placeholder="{{ __('Cth: view_reports, manage_users') }}">
                            <div class="form-text small">{{__('Gunakan format snake_case (cth: create_loan_applications)')}}</div>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        {{-- Guard name is typically 'web' and not user-editable for basic setups --}}
                    </div>
                    <div class="modal-footer motac-modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading wire:target="savePermission" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <i wire:loading.remove wire:target="savePermission" class="bi bi-check-lg me-1"></i>
                            {{ $isEditMode ? __('Simpan Perubahan') : __('Cipta Kebenaran') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation Modal (Structure adapted from Roles modal) --}}
    @if ($showDeleteConfirmationModal)
    <div wire:ignore.self class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content motac-modal-content">
                <div class="modal-header motac-modal-header">
                    <h5 class="modal-title d-flex align-items-center" id="deletePermissionModalLabel">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        {{ __('Sahkan Pemadaman Kebenaran') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeDeleteConfirmationModal" aria-label="Close"></button>
                </div>
                <div class="modal-body motac-modal-body">
                     <p>{{ __('Adakah anda pasti ingin memadam kebenaran') }} "<strong>{{ $permissionNameToDelete }}</strong>"?</p>
                     <p class="small text-danger">{{ __('Tindakan ini tidak boleh diterbalikkan.') }}</p>
                </div>
                <div class="modal-footer motac-modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDeleteConfirmationModal">{{ __('Batal') }}</button>
                    <button type="button" class="btn btn-danger" wire:click="deletePermission">
                        <span wire:loading wire:target="deletePermission" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        <i wire:loading.remove wire:target="deletePermission" class="bi bi-trash3-fill me-1"></i>
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
    // JavaScript for handling Bootstrap modals with Livewire events
    // This assumes you have Bootstrap's JS loaded in your main layout.
    document.addEventListener('livewire:init', () => {
        const permissionFormModalEl = document.getElementById('permissionFormModal');
        const deletePermissionModalEl = document.getElementById('deletePermissionModal');
        let permissionFormModalInstance = null;
        let deletePermissionModalInstance = null;

        if(permissionFormModalEl) permissionFormModalInstance = new bootstrap.Modal(permissionFormModalEl);
        if(deletePermissionModalEl) deletePermissionModalInstance = new bootstrap.Modal(deletePermissionModalEl);

        @this.on('showPermissionModal', () => { // Assuming your component dispatches this event
            if(permissionFormModalInstance) permissionFormModalInstance.show();
        });
        @this.on('hidePermissionModal', () => {
            if(permissionFormModalInstance && bootstrap.Modal.getInstance(permissionFormModalEl)?._isShown) {
                permissionFormModalInstance.hide();
            }
        });
        @this.on('showDeletePermissionConfirmationModal', () => {
            if(deletePermissionModalInstance) deletePermissionModalInstance.show();
        });
        @this.on('hideDeletePermissionConfirmationModal', () => {
            if(deletePermissionModalInstance && bootstrap.Modal.getInstance(deletePermissionModalEl)?._isShown) {
                deletePermissionModalInstance.hide();
            }
        });

        // Sync Livewire state if Bootstrap closes modal via backdrop or ESC
        if(permissionFormModalEl) {
            permissionFormModalEl.addEventListener('hidden.bs.modal', event => {
                if(@this.get('showModal')) {
                    @this.call('closeModal'); // Ensure component's closeModal is called
                }
            });
        }
        if(deletePermissionModalEl) {
            deletePermissionModalEl.addEventListener('hidden.bs.modal', event => {
                if(@this.get('showDeleteConfirmationModal')) {
                     @this.call('closeDeleteConfirmationModal'); // Ensure component's method is called
                }
            });
        }
    });
</script>
@endpush
