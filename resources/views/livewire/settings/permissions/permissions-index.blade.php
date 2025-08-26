{{-- resources/views/livewire/settings/permissions/permissions-index.blade.php --}}
{{-- Livewire PermissionsIndex component view for system permission management --}}

@section('title', __('Pengurusan Kebenaran Sistem'))

<div>
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-key-fill me-2"></i>
            {{ __('Pengurusan Kebenaran Sistem') }}
        </h1>
        @can('manage_permissions')
            <button wire:click="create"
                class="motac-btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2">
                <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Kebenaran Baru') }}
            </button>
        @endcan
    </div>

    @include('_partials._alerts.alert-general')

    {{-- Permissions Table Card --}}
    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-list-check me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Senarai Kebenaran Sedia Ada') }}</h5>
        </div>
        <div class="card-body p-0 motac-card-body">
            @if ($permissions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
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
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
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
                                        @can('manage_permissions')
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
                        {{ $permissions->links() }}
                    </div>
                @endif
            @else
                <div class="alert alert-info text-center m-3">
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

    {{-- Create/Edit Permission Modal --}}
    @if ($showModal)
    <div wire:ignore.self class="modal fade" id="permissionFormModal" tabindex="-1" aria-labelledby="permissionFormModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: block;">
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
                    </div>
                    <div class="modal-footer motac-modal-footer">
                        <button type="button" class="motac-btn-outline" wire:click="closeModal">{{ __('Batal') }}</button>
                        <button type="submit" class="motac-btn-primary">
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

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteConfirmationModal)
    <div wire:ignore.self class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: block;">
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
                    <button type="button" class="motac-btn-outline" wire:click="closeDeleteConfirmationModal">{{ __('Batal') }}</button>
                    <button type="button" class="motac-btn-danger" wire:click="deletePermission">
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
