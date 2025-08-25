{{-- resources/views/livewire/reports/email-accounts-report.blade.php --}}
<div>
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-envelope-check-fill me-2"></i>{{ __('Laporan Akaun E-mel') }}
        </h1>
    </div>

    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-body p-3 p-md-4">
            <h5 class="card-title mb-3 fw-medium d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2 text-primary"></i>{{ __('Saringan Laporan') }}
            </h5>
            <form wire:submit.prevent="applyFilters">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label for="repEmailSearchTerm" class="form-label small">{{ __('Carian') }}</label>
                        <input type="text" wire:model.lazy="searchTerm" id="repEmailSearchTerm"
                            class="form-control form-control-sm" placeholder="{{ __('Emel, Nama Pemohon, ID Mohon') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="repEmailFilterStatus" class="form-label small">{{ __('Status Permohonan') }}</label>
                        <select wire:model.lazy="filterStatus" id="repEmailFilterStatus"
                            class="form-select form-select-sm">
                            <option value="">{{ __('Semua Status') }}</option>
                            @foreach ($statusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="repEmailFilterServiceStatus"
                            class="form-label small">{{ __('Taraf Perkhidmatan') }}</label>
                        <select wire:model.lazy="filterServiceStatus" id="repEmailFilterServiceStatus"
                            class="form-select form-select-sm">
                            <option value="">{{ __('Semua Taraf') }}</option>
                            @foreach ($serviceStatusOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="repEmailFilterDateFrom"
                            class="form-label small">{{ __('Tarikh Mohon Dari') }}</label>
                        <input type="date" wire:model.lazy="filterDateFrom" id="repEmailFilterDateFrom"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="repEmailFilterDateTo"
                            class="form-label small">{{ __('Tarikh Mohon Hingga') }}</label>
                        <input type="date" wire:model.lazy="filterDateTo" id="repEmailFilterDateTo"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-lg-1 col-md-12 mt-3 mt-lg-0 d-flex gap-2">
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
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('id')"
                            role="button" style="cursor:pointer;">
                            {{ __('ID Mohon') }} @if ($sortBy === 'id')
                                <i class="bi {{ $sortDirection === 'asc' ? 'bi-sort-numeric-down' : 'bi-sort-numeric-up-alt' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2"
                            wire:click="setSortBy('user_name')" role="button" style="cursor:pointer;">
                            {{ __('Pemohon') }} @if ($sortBy === 'user_name')
                                <i class="bi {{ $sortDirection === 'asc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up-alt' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Taraf Perkhidmatan') }}
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2"
                            wire:click="setSortBy('proposed_email')" role="button" style="cursor:pointer;">
                            {{ __('Emel Dicadang') }} @if ($sortBy === 'proposed_email')
                                <i class="bi {{ $sortDirection === 'asc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up-alt' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2"
                            wire:click="setSortBy('final_assigned_email')" role="button" style="cursor:pointer;">
                            {{ __('Emel Diluluskan') }} @if ($sortBy === 'final_assigned_email')
                                <i class="bi {{ $sortDirection === 'asc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up-alt' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('status')"
                            role="button" style="cursor:pointer;">
                            {{ __('Status') }} @if ($sortBy === 'status')
                                <i class="bi {{ $sortDirection === 'asc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up-alt' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2"
                            wire:click="setSortBy('created_at')" role="button" style="cursor:pointer;">
                            {{ __('Tarikh Mohon') }} @if ($sortBy === 'created_at')
                                <i class="bi {{ $sortDirection === 'asc' ? 'bi-sort-down' : 'bi-sort-up' }}"></i>
                            @else
                                <i class="bi bi-arrow-down-up text-muted opacity-50" style="font-size: 0.8em;"></i>
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50 table-loading-row" class="transition-opacity">
                        <td colspan="8" class="p-0" style="border:none;">
                             <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar" aria-label="{{__('Memuatkan...')}}">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($reportData as $item)
                        <tr wire:key="email-report-{{ $item->id }}">
                            <td class="px-3 py-2 small">#{{ $item->id }}</td>
                            <td class="px-3 py-2 small text-dark fw-medium">
                                {{ $item->user?->name ?? ($item->applicant_name ?? 'N/A') }}</td>
                            <td class="px-3 py-2 small text-muted">
                                {{ $item->user?->department?->name ?? ($item->applicant_bahagian_unit ?? 'N/A') }}</td>
                            <td class="px-3 py-2 small text-muted">
                                {{ $serviceStatusOptions[$item->user?->service_status ?? $item->service_status] ?? ($item->user?->service_status ?? ($item->service_status ?? 'N/A')) }}
                            </td>
                            <td class="px-3 py-2 small text-muted">{{ $item->proposed_email ?? '-' }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $item->final_assigned_email ?? '-' }}</td>
                            <td class="px-3 py-2 small">
                                {{-- CORRECTED: Added 'email_application' as the second argument --}}
                                <span class="badge rounded-pill {{ \App\Helpers\Helpers::getStatusColorClass($item->status ?? 'default', 'email_application') }}">
                                    {{ $statusOptions[$item->status] ?? __(Str::title(str_replace('_', ' ', $item->status))) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 small text-muted">
                                {{ $item->created_at->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-5 text-center">
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
