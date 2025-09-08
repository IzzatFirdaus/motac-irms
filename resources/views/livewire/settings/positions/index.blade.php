<<<<<<< HEAD
{{-- resources/views/livewire/settings/positions/index.blade.php --}}
<div>
    @section('title', __('Pengurusan Jawatan'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-person-workspace me-2"></i>
            {{ __('Pengurusan Jawatan') }}
        </h1>
        {{-- ADJUSTMENT: "Add" button is protected by the 'create' policy method --}}
        @can('create', App\Models\Position::class)
            <button wire:click="create" class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2 motac-btn-primary">
                <i class="bi bi-plus-lg me-2"></i>
                {{ __('Tambah Jawatan Baru') }}
            </button>
        @endcan
    </div>

    @include('_partials._alerts.alert-general')

    {{-- Search Card --}}
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-search me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Carian Jawatan') }}</h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <label for="positionSearch" class="form-label visually-hidden">{{ __('Cari jawatan...') }}</label>
            <input wire:model.live.debounce.300ms="search" type="text" id="positionSearch"
                class="form-control" placeholder="{{ __('Cari jawatan berdasarkan nama...') }}">
        </div>
    </div>

    {{-- Positions Table Card --}}
    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 d-flex flex-wrap justify-content-between align-items-center motac-card-header">
            <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Senarai Jawatan') }}
            </h5>
            @if ($positions->total() > 0)
                <span class="text-muted small">
                    {{ __('Memaparkan :from-:to daripada :total rekod', ['from' => $positions->firstItem(), 'to' => $positions->lastItem(), 'total' => $positions->total()]) }}
                </span>
            @endif
        </div>
        <div class="card-body p-0 motac-card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('name')" style="cursor: pointer;">
                                {{ __('Nama Jawatan') }} <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            </th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Gred Berkaitan') }}</th>
                            <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('is_active')" style="cursor: pointer;">
                                {{ __('Status') }} <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            </th>
                            <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($positions as $position)
                            <tr wire:key="position-{{ $position->id }}">
                                <td class="px-3 py-2">
                                    <span class="fw-medium text-dark">{{ $position->name }}</span>
                                </td>
                                <td class="px-3 py-2 small text-muted">{{ $position->grade->name ?? 'N/A' }}</td>
                                <td class="px-3 py-2 small">
                                    <span class="badge bg-{{ $position->is_active ? 'success' : 'secondary' }}-subtle text-{{ $position->is_active ? 'success' : 'secondary' }}-emphasis rounded-pill">
                                        {{ $position->is_active ? __('Aktif') : __('Tidak Aktif') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        {{-- ADJUSTMENT: "Edit" button is protected by the 'update' policy method --}}
                                        @can('update', $position)
                                            <button wire:click="edit({{ $position->id }})" class="btn btn-sm btn-icon btn-outline-primary border-0" title="{{ __('Kemaskini') }}">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        @endcan
                                        {{-- ADJUSTMENT: "Delete" button is protected by the 'delete' policy method --}}
                                        @can('delete', $position)
                                            <button wire:click="confirmPositionDeletion({{ $position->id }})" class="btn btn-sm btn-icon btn-outline-danger border-0" title="{{ __('Padam') }}">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-5 text-center">
                                    <div class="d-flex flex-column align-items-center text-muted small">
                                        <i class="bi bi-person-workspace fs-1 mb-2 text-secondary"></i>
                                        <p>{{ __('Tiada jawatan ditemui.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($positions->hasPages())
                <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
                    {{ $positions->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Position Form Modal (Create/Edit) --}}
    <div x-data="{ show: @entangle('showModal').live }" x-show="show" x-cloak class="modal fade" :class="{'show': show}" style="display: none;" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content motac-modal-content">
                <div class="modal-header motac-modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? 'Edit Jawatan' : 'Cipta Jawatan Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="savePosition">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="name">Nama Jawatan<span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Contoh: Pegawai Teknologi Maklumat" wire:model.blur="name">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="grade_id">Gred Berkaitan</label>
                            <select class="form-select @error('grade_id') is-invalid @enderror" id="grade_id" wire:model.blur="grade_id">
                                <option value="">- Pilih Gred -</option>
                                @foreach($gradeOptions as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('grade_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="description">Penerangan (Pilihan)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" rows="3" placeholder="Penerangan tambahan tentang jawatan..." wire:model.blur="description"></textarea>
                            @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="is_active" wire:model.blur="is_active">
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ $isEditMode ? 'Kemaskini' : 'Cipta' }}</span>
                            <span wire:loading>{{ __('Menyimpan...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-data="{ show: @entangle('showDeleteConfirmationModal').live }" x-show="show" x-cloak class="modal fade" :class="{'show': show}" style="display: none;" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content motac-modal-content">
                <div class="modal-header motac-modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                        {{ __('Sahkan Pemadaman Jawatan') }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeDeleteConfirmationModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Adakah anda pasti ingin memadam jawatan "<strong>{{ $positionNameToDelete }}</strong>"? Tindakan ini tidak boleh diundur.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDeleteConfirmationModal">Batal</button>
                    <button type="button" class="btn btn-danger" wire:click="deletePosition">{{ __('Ya, Padam') }}</button>
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
                <h2 class="h2 fw-bold text-dark mb-0">{{ __('Pengurusan Jawatan') }}</h2>
            </div>
            <div class="col-md-6">
                <input wire:model.debounce.300ms="search" type="text" class="form-control" placeholder="{{ __('Cari jawatan (nama, gred)...') }}">
            </div>
        </div>

        {{-- Example: <a href="{{ route('settings.positions.create') }}" class="btn btn-primary mb-3">{{ __('Tambah Jawatan Baru') }}</a> --}}

        <div class="card shadow-sm">
            <div class="card-body">
                @if($positions->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    {{ __('Nama Jawatan') }}
                                    @if($sortField === 'name')
                                        <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('grade_id')" style="cursor: pointer;">
                                    {{ __('Gred') }} {{-- Or sort by related table column if possible with a join --}}
                                    @if($sortField === 'grade_id')
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
                            @foreach ($positions as $position)
                            <tr>
                                <td>{{ $position->name }}</td>
                                <td>{{ $position->grade->name ?? '-' }}</td> {{-- Assumes 'grade' relationship exists and grade has a 'name' attribute --}}
                                <td>
                                    @if($position->is_active)
                                        <span class="badge bg-success">{{ __('Aktif') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Tidak Aktif') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Add action buttons (e.g., Edit, View) here --}}
                                    {{-- Example: <a href="{{ route('settings.positions.edit', $position) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a> --}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $positions->links() }}
                </div>
                @else
                <div class="alert alert-info">
                    {{ __('Tiada jawatan ditemui.') }}
                </div>
                @endif
            </div>
        </div>
    </div>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
</div>
