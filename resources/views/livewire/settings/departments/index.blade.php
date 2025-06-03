{{-- resources/views/livewire/settings/departments/index.blade.php --}}
<div>
    @section('title', __('Pengurusan Jabatan'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-diagram-3-fill me-2"></i>
            {{ __('Pengurusan Jabatan') }}
        </h1>
        {{-- UNCOMMENTED and assuming 'create' policy exists for Department model --}}
        @can('create', App\Models\Department::class)
            <button wire:click="$dispatch('open-modal', { modalId: 'departmentFormModal', action: 'create' })"
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2 motac-btn-primary">
                <i class="bi bi-plus-lg me-2"></i>
                {{ __('Tambah Jabatan Baru') }}
            </button>
        @endcan
    </div>

    @include('_partials._alerts.alert-general') {{-- Ensure this uses MOTAC themed alerts --}}

    {{-- Search Card --}}
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-funnel-fill me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Carian Jabatan') }}</h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <label for="departmentSearch"
                class="form-label visually-hidden">{{ __('Cari jabatan (nama, kod)...') }}</label>
            <input wire:model.live.debounce.300ms="search" type="text" id="departmentSearch"
                class="form-control form-control-sm" placeholder="{{ __('Cari jabatan (nama, kod)...') }}">
        </div>
    </div>

    {{-- Departments Table Card --}}
    <div class="card shadow-sm motac-card">
        <div
            class="card-header bg-light py-3 d-flex flex-wrap justify-content-between align-items-center motac-card-header">
            <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Senarai Jabatan') }}
            </h5>
            @if ($departments->total() > 0) {{-- Check if $departments is not null and has total() method --}}
            <span class="text-muted small">
                {{ __('Memaparkan :from-:to daripada :total rekod', ['from' => $departments->firstItem(), 'to' => $departments->lastItem(), 'total' => $departments->total()]) }}
            </span>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('name')"
                            style="cursor: pointer;">
                            {{ __('Nama Jabatan') }}
                            @if ($sortField === 'name')
                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('code')"
                            style="cursor: pointer;">
                            {{ __('Kod') }}
                            @if ($sortField === 'code')
                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2"
                            wire:click="sortBy('branch_type')" style="cursor: pointer;">
                            {{ __('Jenis Cawangan') }}
                            @if ($sortField === 'branch_type')
                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('is_active')"
                            style="cursor: pointer;">
                            {{ __('Status') }}
                            @if ($sortField === 'is_active')
                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            @endif
                        </th>
                        <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="5" class="p-0 border-0">
                            <div wire:loading.flex class="progress bg-transparent rounded-0"
                                style="height: 2px; width: 100%;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" style="width: 100%"
                                    aria-label="{{ __('Memuatkan Data Jabatan...') }}"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($departments as $department)
                        <tr wire:key="department-{{ $department->id }}">
                            <td class="px-3 py-2 small text-dark fw-medium">{{ $department->name }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $department->code ?? '-' }}</td>
                            <td class="px-3 py-2 small text-muted">
                                {{ $department->branch_type_label ?? __(Str::title(str_replace('_', ' ', $department->branch_type))) }}
                            </td>
                            <td class="px-3 py-2 small">
                                @if ($department->is_active)
                                    <span class="badge text-bg-success">{{ __('Aktif') }}</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ __('Tidak Aktif') }}</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-end">
                                {{-- UNCOMMENTED and REVISED Action buttons --}}
                                <div class="d-inline-flex align-items-center gap-1">
                                    @can('update', $department)
                                    <button wire:click="$dispatch('open-modal', { modalId: 'departmentFormModal', action: 'edit', departmentId: {{ $department->id }} })"
                                        class="btn btn-secondary p-2" {{-- Solid, larger button --}}
                                        title="{{ __('Kemaskini') }}">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    @endcan
                                    @can('delete', $department)
                                    <button wire:click="$dispatch('open-delete-modal', { id: {{ $department->id }}, modelClass: 'App\\Models\\Department', itemDescription: '{{ e(addslashes($department->name)) }}', deleteMethod: 'deleteDepartment' })"
                                        class="btn btn-danger p-2" {{-- Solid, larger button --}}
                                        title="{{ __('Padam') }}">
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
                                    @if (empty($search))
                                        @can('create', App\Models\Department::class)
                                        <button wire:click="$dispatch('open-modal', { modalId: 'departmentFormModal', action: 'create' })" class="btn btn-sm btn-link mt-1">
                                            <i class="bi bi-plus-circle-dotted me-1"></i> {{ __('Tambah Jabatan Baru Sekarang') }}
                                        </button>
                                        @endcan
                                    @endif
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
    {{-- Include modals for create/edit/delete if handled within this Livewire component (e.g., outside this main div or via slots) --}}
    {{-- For example: <livewire:settings.departments.department-form-modal /> --}}
    {{-- <livewire:settings.departments.delete-department-modal /> --}}
</div>
