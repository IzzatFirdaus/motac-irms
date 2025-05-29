<div>
    {{-- @section('title', __('Laporan Akaun E-mel')) --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0">{{ __('Laporan Akaun E-mel') }}</h1>
        {{-- Optional: Add Export Button e.g., wire:click="exportReport" --}}
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <h5 class="card-title mb-3"><i class="ti ti-filter me-1"></i>{{ __('Saringan Laporan') }}</h5>
            <form wire:submit.prevent="applyFilters">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label for="repEmailSearchTerm" class="form-label">{{ __('Carian') }}</label>
                        <input type="text" wire:model.defer="searchTerm" id="repEmailSearchTerm" class="form-control form-control-sm" placeholder="{{__('Emel, Nama Pemohon, ID Mohon')}}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="repEmailFilterStatus" class="form-label">{{ __('Status Permohonan') }}</label>
                        <select wire:model.defer="filterStatus" id="repEmailFilterStatus" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Status') }}</option>
                            @foreach($statusOptions as $key => $label) {{-- Sourced from EmailApplication model statuses --}}
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="repEmailFilterServiceStatus" class="form-label">{{ __('Taraf Perkhidmatan') }}</label>
                        <select wire:model.defer="filterServiceStatus" id="repEmailFilterServiceStatus" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Taraf') }}</option>
                             @foreach($serviceStatusOptions as $key => $label) {{-- Sourced from User model service statuses --}}
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="repEmailFilterDateFrom" class="form-label">{{ __('Tarikh Mohon Dari') }}</label>
                        <input type="date" wire:model.defer="filterDateFrom" id="repEmailFilterDateFrom" class="form-control form-control-sm">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label for="repEmailFilterDateTo" class="form-label">{{ __('Tarikh Mohon Hingga') }}</label>
                        <input type="date" wire:model.defer="filterDateTo" id="repEmailFilterDateTo" class="form-control form-control-sm">
                    </div>
                    <div class="col-lg-1 col-md-12 mt-3 mt-lg-0">
                        <button type="submit" class="btn btn-primary btn-sm w-100">{{ __('Saring') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('id')" role="button" style="cursor:pointer;">{{ __('ID Mohon') }} @if($sortBy === 'id') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('user_name')" role="button" style="cursor:pointer;">{{ __('Pemohon') }} @if($sortBy === 'user_name') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Taraf Perkhidmatan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('proposed_email')" role="button" style="cursor:pointer;">{{ __('Emel Dicadang') }} @if($sortBy === 'proposed_email') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('final_assigned_email')" role="button" style="cursor:pointer;">{{ __('Emel Diluluskan') }} @if($sortBy === 'final_assigned_email') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('status')" role="button" style="cursor:pointer;">{{ __('Status') }} @if($sortBy === 'status') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('created_at')" role="button" style="cursor:pointer;">{{ __('Tarikh Mohon') }} @if($sortBy === 'created_at') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50 table-loading-row" class="transition-opacity">
                        <td colspan="8" class="p-0" style="border:none;">
                            <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar" aria-label="Loading...">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($reportData as $item)
                        <tr wire:key="email-report-{{ $item->id }}">
                            <td class="px-3 py-2 small">#{{ $item->id }}</td>
                            <td class="px-3 py-2 small text-dark fw-medium">{{ $item->user?->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $item->user?->department?->name ?? 'N/A' }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $serviceStatusOptions[$item->user?->service_status] ?? $item->user?->service_status ?? 'N/A' }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $item->proposed_email ?? '-' }}</td>
                            <td class="px-3 py-2 small text-muted">{{ $item->final_assigned_email ?? '-' }}</td>
                            <td class="px-3 py-2 small">
                                 <span class="badge rounded-pill {{ \App\Helpers\Helpers::getStatusColorClass($item->status) }}">
                                    {{ $statusOptions[$item->status] ?? __(Str::title(str_replace('_',' ',$item->status))) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 small text-muted">{{ $item->created_at->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="ti ti-report-analytics fs-1 mb-2 text-secondary"></i> {{-- Changed icon --}}
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
