<div>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="h5 mb-0 fw-semibold text-dark">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>{{ __('Laporan Permohonan Pinjaman Peralatan ICT') }}
                </h3>
                @if (Route::has('reports.index'))
                    <a href="{{ route('reports.index') }}"
                       class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('Kembali ke Senarai Laporan') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body p-3 p-md-4">
            <x-alert-bootstrap />

            {{-- Filters --}}
            <div class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label for="searchTermLoanApp" class="form-label form-label-sm">{{ __('Carian ID/Pemohon/Tujuan') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="search" class="form-control form-control-sm" id="searchTermLoanApp" placeholder="{{ __('Cth: 123, nama, mesyuarat...') }}">
                </div>
                <div class="col-md-2">
                    <label for="filterStatusLoan" class="form-label form-label-sm">{{ __('Status Permohonan') }}</label>
                    <select wire:model.live="filterStatus" class="form-select form-select-sm" id="filterStatusLoan">
                        <option value="">{{ __('Semua Status') }}</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterDepartmentIdLoan" class="form-label form-label-sm">{{ __('Jabatan Pemohon') }}</label>
                    <select wire:model.live="filterDepartmentId" class="form-select form-select-sm" id="filterDepartmentIdLoan">
                        <option value="">{{ __('Semua Jabatan') }}</option>
                        @foreach($departmentOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterDateFromLoan" class="form-label form-label-sm">{{ __('Tarikh Mohon Dari') }}</label>
                    <input wire:model.live="filterDateFrom" type="date" class="form-control form-control-sm" id="filterDateFromLoan">
                </div>
                <div class="col-md-2">
                    <label for="filterDateToLoan" class="form-label form-label-sm">{{ __('Hingga Tarikh') }}</label>
                    <input wire:model.live="filterDateTo" type="date" class="form-control form-control-sm" id="filterDateToLoan">
                </div>
                 <div class="col-md-1">
                    <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100" type="button">{{ __('Reset') }}</button>
                </div>
            </div>

            <div class="table-responsive">
                @if ($reportData->isNotEmpty())
                    <table class="table table-hover table-striped table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="small" wire:click="setSortBy('id')" style="cursor: pointer;">{{ __('ID') }} <x-sort-icon field="id" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small" wire:click="setSortBy('users.name')" style="cursor: pointer;">{{ __('Pemohon') }} <x-sort-icon field="users.name" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small">{{ __('Jabatan') }}</th>
                                <th class="small" wire:click="setSortBy('purpose')" style="cursor: pointer;">{{ __('Tujuan') }} <x-sort-icon field="purpose" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small" wire:click="setSortBy('loan_start_date')" style="cursor: pointer;">{{ __('Tarikh Pinjam') }} <x-sort-icon field="loan_start_date" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small" wire:click="setSortBy('loan_end_date')" style="cursor: pointer;">{{ __('Tarikh Pulang') }} <x-sort-icon field="loan_end_date" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small" wire:click="setSortBy('status')" style="cursor: pointer;">{{ __('Status') }} <x-sort-icon field="status" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small" wire:click="setSortBy('created_at')" style="cursor: pointer;">{{ __('Tarikh Mohon') }} <x-sort-icon field="created_at" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                            </tr>
                        </thead>
                        <tbody>
                             <tr wire:loading.class.delay="opacity-50">
                                <td colspan="8" class="p-0">
                                     <div wire:loading.flex class="progress" style="height: 3px; width: 100%;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($reportData as $application)
                                <tr wire:key="loan-app-report-{{ $application->id }}">
                                    <td class="small align-middle">{{ $application->id }}</td>
                                    <td class="small align-middle">{{ $application->user?->name ?? __('N/A') }}</td>
                                    <td class="small align-middle">{{ $application->user?->department?->name ?? __('N/A') }}</td>
                                    <td class="small align-middle" style="max-width: 200px; white-space: normal;">{{ Str::limit($application->purpose, 50) }}</td>
                                    <td class="small align-middle">{{ $application->loan_start_date?->translatedFormat(config('motac.date_format_my', 'd/m/Y')) }}</td>
                                    <td class="small align-middle">{{ $application->loan_end_date?->translatedFormat(config('motac.date_format_my', 'd/m/Y')) }}</td>
                                    <td class="small align-middle">
                                        <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($application->status, 'bootstrap_badge') }} px-2 py-1">
                                            {{ $application->status_translated ?? __('N/A') }}
                                        </span>
                                    </td>
                                    <td class="small align-middle">{{ $application->created_at?->translatedFormat(config('motac.datetime_format_my', 'd/m/Y H:i A')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($reportData->hasPages())
                        <div class="card-footer bg-light border-top-0 d-flex justify-content-center pt-3 pb-2">
                            {{ $reportData->links() }}
                        </div>
                    @endif
                @else
                     <div class="alert alert-warning text-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ __('Tiada data permohonan pinjaman ditemui untuk kriteria ini.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
