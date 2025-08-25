<<<<<<< HEAD
{{-- resources/views/livewire/settings/departments/index.blade.php --}}
<div>
    @section('title', __('Pengurusan Jabatan'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-diagram-3-fill me-2"></i>
            {{ __('Pengurusan Jabatan') }}
        </h1>
        @can('create', App\Models\Department::class)
            <button wire:click="$dispatch('open-modal', { modalId: 'departmentFormModal', action: 'create' })"
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2 motac-btn-primary">
                <i class="bi bi-plus-lg me-2"></i>
                {{ __('Tambah Jabatan Baru') }}
            </button>
        @endcan
    </div>

    @include('_partials._alerts.alert-general')

    {{-- Search Card --}}
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-funnel-fill me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Carian Jabatan') }}</h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <label for="departmentSearch" class="form-label visually-hidden">{{ __('Cari jabatan (nama, kod)...') }}</label>
            <input wire:model.live.debounce.300ms="search" type="text" id="departmentSearch"
                class="form-control" placeholder="{{ __('Cari jabatan berdasarkan nama atau kod...') }}">
        </div>
    </div>

    {{-- Departments Table Card --}}
    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 d-flex flex-wrap justify-content-between align-items-center motac-card-header">
            <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Senarai Jabatan') }}
            </h5>
            @if ($departments->total() > 0)
                <span class="text-muted small">
                    {{ __('Memaparkan :from-:to daripada :total rekod', ['from' => $departments->firstItem(), 'to' => $departments->lastItem(), 'total' => $departments->total()]) }}
                </span>
            @endif
        </div>
        <div class="card-body p-0 motac-card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('name')" style="cursor: pointer;">
                                {{ __('Nama Jabatan') }} <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            </th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('code')" style="cursor: pointer;">
                                {{ __('Kod') }} <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            </th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Ketua Jabatan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('is_active')" style="cursor: pointer;">
                                {{ __('Status') }} <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            </th>
                            <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                            <td colspan="5" class="p-0 border-0">
                                <div wire:loading.flex class="progress bg-transparent rounded-0" style="height: 2px; width: 100%;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                                </div>
                            </td>
                        </tr>
                        @forelse ($departments as $department)
                            <tr wire:key="department-{{ $department->id }}">
                                <td class="px-3 py-2">
                                    <span class="fw-medium text-dark">{{ $department->name }}</span>
                                    <div class="small text-muted">{{ $department->branch_type_label }}</div>
                                </td>
                                <td class="px-3 py-2 small text-muted">{{ $department->code ?? '-' }}</td>
                                <td class="px-3 py-2 small text-muted">{{ optional($department->headOfDepartment)->name ?? '-' }}</td>
                                <td class="px-3 py-2 small">
                                    <span class="badge bg-{{ $department->is_active ? 'success' : 'secondary' }}-subtle text-{{ $department->is_active ? 'success' : 'secondary' }}-emphasis rounded-pill">
                                        {{ $department->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        @can('update', $department)
                                            <button wire:click="$dispatch('open-modal', { modalId: 'departmentFormModal', action: 'edit', departmentId: {{ $department->id }} })"
                                                class="btn btn-sm btn-icon btn-outline-primary border-0" title="{{ __('Kemaskini') }}">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        @endcan
                                        @can('delete', $department)
                                            <button wire:click="$dispatch('open-delete-modal', { id: {{ $department->id }}, itemDescription: '{{ e(addslashes($department->name)) }}', deleteMethod: 'deleteDepartment' })"
                                                class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{ __('Padam') }}">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-5 text-center">
                                    <div class="d-flex flex-column align-items-center text-muted small">
                                        <i class="bi bi-diagram-2 fs-1 mb-2 text-secondary"></i>
                                        <p>{{ __('Tiada jabatan ditemui.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($departments->hasPages())
                <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
                    {{ $departments->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- ADJUSTMENT: Added a self-contained delete confirmation modal using Alpine.js, similar to the users index view. --}}
    <div x-data="{ show: false, itemId: null, itemDescription: '', deleteMethod: '' }"
         x-show="show"
         x-cloak
         @open-delete-modal.window="
            show = true;
            itemId = $event.detail.id;
            itemDescription = $event.detail.itemDescription;
            deleteMethod = $event.detail.deleteMethod;
         "
         class="modal fade"
         :class="{ 'show': show }"
         style="display: none;"
         aria-modal="true"
         role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content motac-modal-content">
                <div class="modal-header motac-modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        {{ __('Sahkan Pemadaman') }}
                    </h5>
                    <button type="button" class="btn-close" @click="show = false" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('Adakah anda pasti ingin memadam Jabatan') }} "<strong><span x-text="itemDescription"></span></strong>"?</p>
                    <p class="small text-danger">{{ __('Tindakan ini tidak boleh diundur.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="show = false">{{ __('Batal') }}</button>
                    <button type="button" class="btn btn-danger" @click="$wire.call(deleteMethod, itemId); show = false;">{{ __('Ya, Padam') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div x-show="show" x-cloak class="modal-backdrop fade show"></div>

=======
<div>
    <div class="container py-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <h2 class="h2 fw-bold text-dark mb-0">{{ __('Pengurusan Jabatan') }}</h2>
            </div>
            <div class="col-md-6">
                <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="{{ __('Cari jabatan (nama, kod)...') }}">
            </div>
        </div>

        {{-- You can add a button here to create a new department if needed --}}
        {{-- Example: <a href="{{ route('settings.departments.create') }}" class="btn btn-primary mb-3">{{ __('Tambah Jabatan Baru') }}</a> --}}

        <div class="card shadow-sm">
            <div class="card-body">
                @if($departments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    {{ __('Nama Jabatan') }}
                                    @if($sortField === 'name')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('code')" style="cursor: pointer;">
                                    {{ __('Kod') }}
                                    @if($sortField === 'code')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('branch_type')" style="cursor: pointer;">
                                    {{ __('Jenis Cawangan') }}
                                    @if($sortField === 'branch_type')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                                    {{ __('Status') }}
                                    @if($sortField === 'is_active')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                            <tr>
                                <td>{{ $department->name }}</td>
                                <td>{{ $department->code ?? '-' }}</td>
                                <td>{{ $department->branch_type_label ?? $department->branch_type }}</td> {{-- Assuming you have a branch_type_label accessor or use constants --}}
                                <td>
                                    @if($department->is_active)
                                        <span class="badge bg-success">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Tidak Aktif') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Add action buttons (e.g., Edit, View) here --}}
                                    {{-- Example: <a href="{{ route('settings.departments.edit', $department) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a> --}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $departments->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    {{ __('Tiada jabatan ditemui.') }}
                </div>
                @endif
            </div>
        </div>
    </div>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
</div>
