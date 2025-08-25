{{-- resources/views/livewire/settings/positions/positions-index.blade.php --}}
{{-- Livewire PositionsIndex component view for managing positions --}}

@section('title', __('Pengurusan Jawatan'))

<div>
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-person-workspace me-2"></i>
            {{ __('Pengurusan Jawatan') }}
        </h1>
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
                                        @can('update', $position)
                                            <button wire:click="edit({{ $position->id }})" class="btn btn-sm btn-icon btn-outline-primary border-0" title="{{ __('Kemaskini') }}">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        @endcan
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

    {{-- Modal for create/edit position (Reusable Partial) --}}
    @include('livewire.settings.positions.position-form-modal')

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
                    <p>{{ __('Adakah anda pasti ingin memadam jawatan') }} "<strong>{{ $positionNameToDelete }}</strong>"? {{ __('Tindakan ini tidak boleh diundur.') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDeleteConfirmationModal">{{ __('Batal') }}</button>
                    <button type="button" class="btn btn-danger" wire:click="deletePosition">{{ __('Ya, Padam') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div x-show="show" x-cloak class="modal-backdrop fade show"></div>
</div>
