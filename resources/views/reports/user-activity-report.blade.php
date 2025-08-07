{{-- resources/views/reports/user-activity-report.blade.php --}}
{{-- Livewire User Activity Report Component View --}}

<div>
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-person-check-fill me-2"></i>{{ __('Laporan Aktiviti Pengguna') }}
        </h1>
        {{-- Optional: Export Button --}}
    </div>

    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-body p-3 p-md-4">
            <h5 class="card-title mb-3 fw-medium d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2 text-primary"></i>{{ __('Saringan Laporan') }}
            </h5>
            <form wire:submit.prevent="applyFilters">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label for="repUsrActSearchLivewire"
                            class="form-label small">{{ __('Carian (Nama Pengguna, Emel)') }}</label>
                        <input type="text" wire:model.lazy="searchTerm" id="repUsrActSearchLivewire"
                            class="form-control form-control-sm" placeholder="{{ __('Masukkan kata kunci...') }}">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="repUsrActDeptLivewire" class="form-label small">{{ __('Jabatan') }}</label>
                        <select wire:model.lazy="filterDepartmentId" id="repUsrActDeptLivewire"
                            class="form-select form-select-sm">
                            <option value="">{{ __('Semua Jabatan') }}</option>
                            @foreach ($departmentOptions as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="repUsrActRoleLivewire" class="form-label small">{{ __('Peranan') }}</label>
                        <select wire:model.lazy="filterRoleName" id="repUsrActRoleLivewire"
                            class="form-select form-select-sm">
                            <option value="">{{ __('Semua Peranan') }}</option>
                            @foreach ($roleOptions as $name => $displayName)
                                <option value="{{ $name }}">{{ $displayName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12 mt-3 mt-lg-0 d-flex gap-2">
                        <button type="submit"
                            class="btn btn-primary btn-sm w-100 motac-btn-primary d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-search me-1"></i>{{ __('Saring') }}
                        </button>
                        <button type="button" wire:click="resetFilters"
                            class="btn btn-outline-secondary btn-sm w-100 motac-btn-outline d-inline-flex align-items-center justify-content-center"
                            title="{{ __('Set Semula Saringan') }}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm motac-card">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('name')"
                            role="button" style="cursor:pointer;">
                            {{ __('Nama Pengguna') }}
                            @if ($sortBy === 'name')
                                <i
                                    class="bi {{ $sortDirection === 'asc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up-alt' }} ms-1"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted ms-1 opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('email')"
                            role="button" style="cursor:pointer;">
                            {{ __('Emel') }}
                            @if ($sortBy === 'email')
                                <i
                                    class="bi {{ $sortDirection === 'asc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up-alt' }} ms-1"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted ms-1 opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peranan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center"
                            wire:click="setSortBy('email_applications_count')" role="button" style="cursor:pointer;">
                            {{ __('Bil. Mohon E-mel') }}
                            @if ($sortBy === 'email_applications_count')
                                <i
                                    class="bi {{ $sortDirection === 'asc' ? 'bi-sort-numeric-down' : 'bi-sort-numeric-up-alt' }} ms-1"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted ms-1 opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center"
                            wire:click="setSortBy('loan_applications_as_applicant_count')" role="button"
                            style="cursor:pointer;">
                            {{ __('Bil. Mohon Pinjaman') }}
                            @if ($sortBy === 'loan_applications_as_applicant_count')
                                <i
                                    class="bi {{ $sortDirection === 'asc' ? 'bi-sort-numeric-down' : 'bi-sort-numeric-up-alt' }} ms-1"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted ms-1 opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center"
                            wire:click="setSortBy('approvals_made_count')" role="button" style="cursor:pointer;">
                            {{ __('Bil. Kelulusan Dibuat') }}
                            @if ($sortBy === 'approvals_made_count')
                                <i
                                    class="bi {{ $sortDirection === 'asc' ? 'bi-sort-numeric-down' : 'bi-sort-numeric-up-alt' }} ms-1"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted ms-1 opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50 table-loading-row" class="transition-opacity">
                        <td colspan="7" class="p-0" style="border:none;">
                            <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar"
                                aria-label="{{ __('Memuatkan...') }}">
                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                    style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($reportData as $item)
                        <tr wire:key="user-act-report-{{ $item->id }}">
                            <td class="px-3 py-2 small text-dark fw-medium">{{ $item->name }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $item->email }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $item->department?->name ?? '-' }}</td>
                            <td class="px-3 py-2 small text-muted">
                                @forelse($item->roles as $role)
                                    <span
                                        class="badge bg-secondary-subtle text-secondary-emphasis fw-normal me-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted fst-italic small">{{ __('Tiada peranan') }}</span>
                                @endforelse
                            </td>
                            <td class="px-3 py-2 small text-muted text-center">{{ $item->email_applications_count }}
                            </td>
                            <td class="px-3 py-2 small text-muted text-center">
                                {{ $item->loan_applications_as_applicant_count }}</td>
                            <td class="px-3 py-2 small text-muted text-center">{{ $item->approvals_made_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="bi bi-table fs-1 mb-2 text-secondary"></i>
                                    {{ __('Tiada data laporan ditemui untuk saringan ini.') }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reportData->hasPages())
            <div class="card-footer bg-light border-top d-flex justify-content-center py-2">
                {{ $reportData->links() }}
            </div>
        @endif
    </div>
</div>
