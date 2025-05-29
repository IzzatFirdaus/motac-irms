<div>
    {{-- @section('title', __('Laporan Aktiviti Pengguna')) --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0">{{ __('Laporan Aktiviti Pengguna') }}</h1>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body p-3">
            <h5 class="card-title mb-3"><i class="ti ti-filter me-1"></i>{{ __('Saringan Laporan') }}</h5>
            <form wire:submit.prevent="applyFilters">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label for="repUsrActSearch" class="form-label">{{ __('Carian (Nama Pengguna, Emel)') }}</label>
                        <input type="text" wire:model.defer="searchTerm" id="repUsrActSearch" class="form-control form-control-sm" placeholder="{{__('Masukkan kata kunci...')}}">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="repUsrActDept" class="form-label">{{ __('Jabatan') }}</label>
                        <select wire:model.defer="filterDepartmentId" id="repUsrActDept" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Jabatan') }}</option>
                            @foreach($departmentOptions as $id => $name) {{-- From Departments table --}}
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label for="repUsrActRole" class="form-label">{{ __('Peranan') }}</label>
                        <select wire:model.defer="filterRoleName" id="repUsrActRole" class="form-select form-select-sm">
                            <option value="">{{ __('Semua Peranan') }}</option>
                             @foreach($roleOptions as $name => $displayName) {{-- From Spatie Roles --}}
                                <option value="{{ $name }}">{{ $displayName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12 mt-3 mt-lg-0">
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
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('name')" role="button" style="cursor:pointer;">{{ __('Nama Pengguna') }} @if($sortBy === 'name') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2" wire:click="setSortBy('email')" role="button" style="cursor:pointer;">{{ __('Emel') }} @if($sortBy === 'email') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jabatan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Peranan') }}</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center" wire:click="setSortBy('email_applications_count')" role="button" style="cursor:pointer;">{{ __('Bil. Mohon E-mel') }} @if($sortBy === 'email_applications_count') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center" wire:click="setSortBy('loan_applications_as_applicant_count')" role="button" style="cursor:pointer;">{{ __('Bil. Mohon Pinjaman') }} @if($sortBy === 'loan_applications_as_applicant_count') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                        <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center" wire:click="setSortBy('approvals_made_count')" role="button" style="cursor:pointer;">{{ __('Bil. Kelulusan Dibuat') }} @if($sortBy === 'approvals_made_count') <i class="ti ti-arrows-sort"></i> @else <i class="ti ti-arrow-autofit-up text-muted"></i> @endif</th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50 table-loading-row" class="transition-opacity">
                        <td colspan="7" class="p-0" style="border:none;">
                            <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar" aria-label="Loading...">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
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
                                    <span class="badge bg-secondary me-1 fw-normal">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted fst-italic small">{{__('Tiada peranan')}}</span>
                                @endforelse
                            </td>
                            <td class="px-3 py-2 small text-muted text-center">{{ $item->email_applications_count }}</td>
                            <td class="px-3 py-2 small text-muted text-center">{{ $item->loan_applications_as_applicant_count }}</td>
                            <td class="px-3 py-2 small text-muted text-center">{{ $item->approvals_made_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="ti ti-report-analytics fs-1 mb-2 text-secondary"></i>
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
