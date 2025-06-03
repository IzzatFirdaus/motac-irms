{{-- resources/views/livewire/settings/positions/index.blade.php --}}
<div>
    @section('title', __('Pengurusan Jawatan'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            {{-- Iconography: Design Language 2.4 --}}
            <i class="bi bi-person-badge-fill me-2"></i>
            {{ __('Pengurusan Jawatan') }}
        </h1>
        {{-- @can('create', App\Models\Position::class) --}}
            {{-- Assuming a Livewire modal or a dedicated create page.
                 This button should trigger the modal or navigate to the create route.
                 Example for triggering a Livewire modal:
            <button wire:click="$dispatch('open-modal', { modalId: 'positionFormModal', action: 'create' })"
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2 motac-btn-primary">
                <i class="bi bi-plus-lg me-2"></i>
                {{ __('Tambah Jawatan Baru') }}
            </button>
            --}}
        {{-- @endcan --}}
    </div>

    @include('_partials._alerts.alert-general') {{-- Ensure this uses MOTAC themed alerts --}}

    {{-- Search Card --}}
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-funnel-fill me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Carian Jawatan') }}</h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <label for="positionSearch" class="form-label visually-hidden">{{ __('Cari jawatan (nama, gred)...') }}</label>
            <input wire:model.live.debounce.300ms="search" type="text" id="positionSearch"
                   class="form-control form-control-sm" placeholder="{{ __('Cari jawatan (nama, gred)...') }}"> {{-- Ensure form-control is MOTAC themed --}}
        </div>
    </div>

    {{-- Positions Table Card --}}
    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 d-flex flex-wrap justify-content-between align-items-center motac-card-header">
            <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Senarai Jawatan') }}
            </h5>
            {{-- Optional: Add total count if available from Livewire component
            <span class="text-muted small">
                {{ __('Memaparkan :count rekod', ['count' => $positions->total()]) }}
            </span>
             --}}
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle"> {{-- Ensure table is MOTAC themed --}}
                <thead class="table-light"> {{-- Ensure table-light header is MOTAC themed --}}
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('name')" style="cursor: pointer;">
                            {{ __('Nama Jawatan') }}
                            @if($sortField === 'name')
                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('grade_id')" style="cursor: pointer;">
                            {{ __('Gred') }}
                            @if($sortField === 'grade_id')
                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="sortBy('is_active')" style="cursor: pointer;">
                            {{ __('Status') }}
                            @if($sortField === 'is_active')
                                <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50"></i>
                            @endif
                        </th>
                        <th class="text-end small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="4" class="p-0 border-0"> {{-- Colspan should match number of columns --}}
                            <div wire:loading.flex class="progress bg-transparent rounded-0" style="height: 2px; width: 100%;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" {{-- Ensure bg-primary uses MOTAC primary color --}}
                                    role="progressbar" style="width: 100%"
                                    aria-label="{{ __('Memuatkan Data Jawatan...') }}"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($positions as $position)
                    <tr wire:key="position-{{ $position->id }}">
                        <td class="px-3 py-2 small text-dark fw-medium">{{ $position->name }}</td>
                        <td class="px-3 py-2 small text-muted">{{ $position->grade->name ?? '-' }}</td>
                        <td class="px-3 py-2 small">
                            {{-- Ensure badge classes are MOTAC themed (Design Language 2.1, 3.3) --}}
                            @if($position->is_active)
                                <span class="badge text-bg-success">{{ __('Aktif') }}</span> {{-- Bootstrap 5.3+ class for themed badge --}}
                            @else
                                <span class="badge text-bg-secondary">{{ __('Tidak Aktif') }}</span> {{-- Or text-bg-danger / custom inactive style --}}
                            @endif
                        </td>
                        <td class="px-3 py-2 text-end">
                            {{-- Add action buttons here --}}
                            {{-- Example based on other index pages:
                            @can('update', $position)
                            <button wire:click="$dispatch('open-modal', { modalId: 'positionFormModal', action: 'edit', positionId: {{ $position->id }} })"
                                class="btn btn-sm btn-icon btn-outline-primary border-0 me-1 motac-btn-icon" title="{{ __('Kemaskini') }}">
                                <i class="bi bi-pencil-fill fs-6"></i>
                            </button>
                            @endcan
                            @can('delete', $position)
                            <button wire:click="$dispatch('open-delete-modal', { id: {{ $position->id }}, modelClass: 'App\\Models\\Position', itemDescription: '{{ $position->name }}', deleteMethod: 'deletePosition' })"
                                class="btn btn-sm btn-icon btn-outline-danger border-0 motac-btn-icon" title="{{ __('Padam') }}">
                                <i class="bi bi-trash3-fill fs-6"></i>
                            </button>
                            @endcan
                            --}}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-3 py-5 text-center"> {{-- Colspan should match --}}
                            <div class="d-flex flex-column align-items-center text-muted small">
                                <i class="bi bi-person-badge fs-1 mb-2 text-secondary"></i>
                                <p>{{ __('Tiada jawatan ditemui.') }}</p>
                                @if(empty($search))
                                     {{-- <button wire:click="$dispatch('open-modal', { modalId: 'positionFormModal', action: 'create' })" class="btn btn-sm btn-primary mt-2">
                                        <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Jawatan Baru') }}
                                    </button> --}}
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($positions->hasPages())
        <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
            {{ $positions->links() }} {{-- Ensure pagination is Bootstrap 5 styled and MOTAC themed --}}
        </div>
        @endif
    </div>
    {{-- Include modals for create/edit/delete if handled within this Livewire component --}}
</div>
