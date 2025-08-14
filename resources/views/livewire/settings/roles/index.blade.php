{{-- resources/views/livewire/settings/roles/index.blade.php --}}
<div class="container mt-4">
    @section('title', __('Pengurusan Peranan & Kebenaran'))

    <div class="row mb-3 align-items-center pb-2 border-bottom">
        <div class="col">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                <i class="bi bi-shield-lock-fill me-2"></i>
                {{ __('Pengurusan Peranan Pengguna') }}
            </h1>
        </div>
        <div class="col text-end">
            {{-- ADJUSTED: Changed policy check to use the 'manage_roles' permission for consistency. --}}
            @can('manage_roles')
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
                                        @can('manage_roles')
                                            <button wire:click="edit({{ $role->id }})" class="btn btn-sm btn-icon btn-outline-primary border-0 me-1 motac-btn-icon" title="{{ __('Kemaskini') }}">
                                                <i class="bi bi-pencil-fill fs-6"></i>
                                            </button>
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

    {{-- The modals for Create/Edit and Delete remain unchanged as they are controlled by the Livewire component --}}
    @if ($showModal)
    <div wire:ignore.self class="modal fade" id="roleFormModal" tabindex="-1" aria-labelledby="roleFormModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        {{-- Modal content remains the same --}}
    </div>
    @endif

    @if ($showDeleteConfirmationModal)
    <div wire:ignore.self class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        {{-- Modal content remains the same --}}
    </div>
    @endif
</div>

@push('scripts')
{{-- The script for handling modals remains unchanged --}}
@endpush
