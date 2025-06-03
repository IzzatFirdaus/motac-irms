{{-- resources/views/livewire/reports/email-accounts-report.blade.php --}}
<div>
    {{-- Ensure .card and .motac-card (if used via x-card) are MOTAC themed --}}
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header"> {{-- Ensure bg-light uses MOTAC theme surface color --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="h5 mb-0 fw-semibold text-dark d-flex align-items-center">
                    <i class="bi bi-envelope-paper-fill me-2"></i>{{-- Already Bootstrap Icon --}}
                    {{ __('Laporan Akaun E-mel & ID Pengguna') }}
                </h3>
                @if (Route::has('reports.index'))
                    <a href="{{ route('reports.index') }}"
                       class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> {{-- Already Bootstrap Icon --}}
                        {{ __('Kembali ke Senarai Laporan') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body p-3 p-md-4 motac-card-body">
            <x-alert-bootstrap /> {{-- Ensure this component uses MOTAC themed alerts --}}

            {{-- Filters --}}
            <div class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label for="searchTerm" class="form-label form-label-sm">{{ __('Carian Nama/Emel') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="search" class="form-control form-control-sm" id="searchTerm" placeholder="{{ __('Cth: nama, emel@motac...') }}"> {{-- Ensure form-control is MOTAC themed --}}
                </div>
                <div class="col-md-2">
                    <label for="filterStatus" class="form-label form-label-sm">{{ __('Status Permohonan') }}</label>
                    <select wire:model.live="filterStatus" class="form-select form-select-sm" id="filterStatus"> {{-- Ensure form-select is MOTAC themed --}}
                        <option value="">{{ __('Semua Status') }}</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterServiceStatus" class="form-label form-label-sm">{{ __('Taraf Perkhidmatan Pemohon') }}</label>
                    <select wire:model.live="filterServiceStatus" class="form-select form-select-sm" id="filterServiceStatus">
                        <option value="">{{ __('Semua Taraf') }}</option>
                        @foreach($serviceStatusOptions as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterDateFrom" class="form-label form-label-sm">{{ __('Tarikh Permohonan Dari') }}</label>
                    <input wire:model.live="filterDateFrom" type="date" class="form-control form-control-sm" id="filterDateFrom">
                </div>
                <div class="col-md-2">
                    <label for="filterDateTo" class="form-label form-label-sm">{{ __('Hingga Tarikh') }}</label>
                    <input wire:model.live="filterDateTo" type="date" class="form-control form-control-sm" id="filterDateTo">
                </div>
                <div class="col-md-1">
                    <button wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100" type="button">{{ __('Reset') }}</button> {{-- Ensure btn-outline-secondary is MOTAC themed --}}
                </div>
            </div>

            <div class="table-responsive">
                @if ($reportData->isNotEmpty())
                    <table class="table table-hover table-striped table-sm mb-0"> {{-- Ensure table is MOTAC themed --}}
                        <thead class="table-light"> {{-- Ensure table-light uses MOTAC theme header bg --}}
                            <tr>
                                {{-- Ensure x-sort-icon uses Bootstrap Icons (bi-arrow-up/down) --}}
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('id')" style="cursor: pointer;">{{ __('ID') }} <x-sort-icon field="id" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('users.name')" style="cursor: pointer;">{{ __('Pemohon') }} <x-sort-icon field="users.name" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('proposed_email')" style="cursor: pointer;">{{ __('Emel Dicadang') }} <x-sort-icon field="proposed_email" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('final_assigned_email')" style="cursor: pointer;">{{ __('Emel Dilulus') }} <x-sort-icon field="final_assigned_email" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('status')" style="cursor: pointer;">{{ __('Status') }} <x-sort-icon field="status" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('created_at')" style="cursor: pointer;">{{ __('Tarikh Mohon') }} <x-sort-icon field="created_at" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('updated_at')" style="cursor: pointer;">{{ __('Tarikh Kemaskini') }} <x-sort-icon field="updated_at" :sortField="$sortBy" :sortDirection="$sortDirection" /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr wire:loading.class.delay="opacity-50">
                                <td colspan="8" class="p-0">
                                     <div wire:loading.flex class="progress" style="height: 3px; width: 100%;"> {{-- Ensure progress bar uses MOTAC primary color --}}
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($reportData as $application)
                                <tr wire:key="email-app-{{ $application->id }}">
                                    <td class="small align-middle">{{ $application->id }}</td>
                                    <td class="small align-middle">{{ $application->user?->name ?? __('N/A') }}</td>
                                    <td class="small align-middle">{{ $application->user?->department?->name ?? __('N/A')}}</td>
                                    <td class="small align-middle">{{ $application->proposed_email ?: '-' }}</td>
                                    <td class="small align-middle">{{ $application->final_assigned_email ?: '-' }}</td>
                                    <td class="small align-middle">
                                        {{-- Ensure Helpers::getStatusColorClass provides MOTAC themed badge classes --}}
                                        <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($application->status, 'bootstrap_badge') }} px-2 py-1">
                                            {{ $application->status_translated ?? __('N/A') }}
                                        </span>
                                    </td>
                                    <td class="small align-middle">{{ $application->created_at?->translatedFormat(config('motac.datetime_format_my', 'd/m/Y H:i A')) }}</td>
                                    <td class="small align-middle">{{ $application->updated_at?->translatedFormat(config('motac.datetime_format_my', 'd/m/Y H:i A')) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($reportData->hasPages())
                        <div class="card-footer bg-light border-top-0 d-flex justify-content-center pt-3 pb-2">
                            {{ $reportData->links() }} {{-- Ensure pagination is Bootstrap 5 styled and MOTAC themed --}}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning text-center" role="alert"> {{-- Ensure alert-warning is MOTAC themed --}}
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{-- Already Bootstrap Icon --}}
                        {{ __('Tiada data permohonan emel ditemui untuk kriteria ini.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
