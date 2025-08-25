<div>
<<<<<<< HEAD
    @section('title', __('Permohonan Pinjaman Untuk Diproses'))

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-card-checklist me-2"></i>
            {{ __('Permohonan Sedia Untuk Pengeluaran') }}
        </h4>
    </div>

    <div class="card motac-card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label for="searchTerm" class="form-label">{{ __('Carian') }}</label>
                    <input type="text" id="searchTerm" wire:model.live.debounce.300ms="searchTerm" class="form-control" placeholder="{{ __('Cari ID, Tujuan, atau Pemohon...') }}">
=======
    {{-- The title is set in your Livewire component's render method using ->title() --}}
    {{-- The layout is set by #[Layout('layouts.app')] in your Livewire component --}}

    <div class="container py-4">
        <h2 class="h2 fw-bold text-dark mb-4">{{ __('Senarai Pinjaman Menunggu Pengeluaran') }}</h2>

        {{-- Include a general alert partial if you have one, or handle session messages directly --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Search Input (as per your Livewire component's $searchTerm property) --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="searchOutstandingLoansBPM"
                            class="form-label visually-hidden">{{ __('Carian Permohonan') }}</label>
                        <input wire:model.live.debounce.300ms="searchTerm" id="searchOutstandingLoansBPM" type="text"
                            placeholder="{{ __('Cari ID Permohonan, Tujuan, Nama Pemohon...') }}" class="form-control">
                    </div>
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                </div>
            </div>
        </div>

<<<<<<< HEAD
        {{-- Loading Indicator --}}
        <div wire:loading.delay.long class="w-100 text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">{{ __('Memuatkan...') }}</span>
            </div>
        </div>

        <div wire:loading.remove>
            @if ($this->outstandingApplications->isEmpty())
                <div class="text-center p-5">
                    <i class="bi bi-info-circle-fill fs-1 text-info"></i>
                    <p class="mt-3">{{ __('Tiada permohonan yang menunggu tindakan anda pada masa ini.') }}</p>
                </div>
            @else
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="sortBy('id')" style="cursor: pointer;" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    @lang('Permohonan #')
                                    @if($sortBy === 'id') <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    @lang('Pemohon')
                                </th>
                                <th wire:click="sortBy('purpose')" style="cursor: pointer;" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    @lang('Tujuan')
                                    @if($sortBy === 'purpose') <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                                <th wire:click="sortBy('updated_at')" style="cursor: pointer;" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    @lang('Tarikh Diluluskan')
                                    @if($sortBy === 'updated_at') <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">@lang('Item Diluluskan')</th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2">@lang('Tindakan')</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($this->outstandingApplications as $application)
                                <tr wire:key="app-{{ $application->id }}">
                                    <td class="px-3 py-2 small fw-medium">
                                        <a href="{{ route('loan-applications.show', $application->id) }}">#{{ $application->id }}</a>
                                    </td>
                                    <td class="px-3 py-2 small">{{ $application->user->name }}</td>
                                    <td class="px-3 py-2 small">{{ Str::limit($application->purpose, 50) }}</td>
                                    <td class="px-3 py-2 small">{{ $application->updated_at->translatedFormat('d M Y, g:i A') }}</td>
                                    <td class="px-3 py-2 small">
                                        @foreach($application->loanApplicationItems as $item)
                                            <div>{{ \App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? $item->equipment_type }} ({{ __('Qty') }}: {{ $item->quantity_approved }})</div>
                                        @endforeach
                                    </td>
                                    <td class="text-center px-3 py-2">
                                        {{-- THE FIX IS APPLIED ON THE NEXT LINE: Use the new route name --}}
                                        <a href="{{ route('loan-applications.issue.form', ['loanApplication' => $application->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-box-arrow-in-up-right me-1"></i> @lang('Proses Pengeluaran')
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($this->outstandingApplications->hasPages())
                    <div class="card-footer d-flex justify-content-between">
                        <div class="small text-muted">
                            @lang('Showing')
                            <strong>{{ $this->outstandingApplications->firstItem() }}</strong>
                            @lang('to')
                            <strong>{{ $this->outstandingApplications->lastItem() }}</strong>
                            @lang('of')
                            <strong>{{ $this->outstandingApplications->total() }}</strong>
                            @lang('results')
                        </div>
                        {{ $this->outstandingApplications->links() }}
=======
        <div wire:loading.delay.long class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">{{ __('Memuatkan...') }}</span>
            </div>
            <p class="mt-2">{{ __('Memuatkan senarai permohonan...') }}</p>
        </div>

        <div wire:loading.remove>
            @if ($applications->isEmpty())
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ __('Tiada permohonan pinjaman menunggu pengeluaran pada masa ini atau sepadan dengan carian anda.') }}
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body p-0"> {{-- Removed card-body padding to make table flush --}}
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0"> {{-- Removed mb-0 if card-body has p-0 --}}
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Permohonan #') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Pemohon') }}
                                        </th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Tujuan') }}
                                        </th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Tarikh Dijangka Pulang') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Item Diluluskan') }}</th>
                                        <th scope="col"
                                            class="small text-uppercase text-muted fw-medium text-end px-3 py-2">
                                            {{ __('Tindakan') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($applications as $application)
                                        <tr wire:key="bpm-outstanding-loan-{{ $application->id }}">
                                            <td class="align-middle px-3 py-2">
                                                {{-- Assuming 'loan-applications.show' is a defined route for viewing details --}}
                                                <a
                                                    href="{{ route('loan-applications.show', $application->id) }}">{{ $application->id }}</a>
                                            </td>
                                            <td class="align-middle px-3 py-2">
                                                @if ($application->user)
                                                    {{-- Assuming 'settings.users.show' or a similar admin route for user details --}}
                                                    <a
                                                        href="{{ route('settings.users.show', $application->user->id) }}">
                                                        {{ $application->user->name ?? 'N/A' }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="align-middle px-3 py-2"
                                                style="white-space: normal; min-width: 250px;">
                                                {{ Str::limit($application->purpose, 70) }}</td>
                                            <td class="align-middle px-3 py-2">
                                                {{ $application->loan_end_date?->translatedFormat(config('app.date_format_my_short', 'd M Y')) ?? 'N/A' }}
                                            </td>
                                            <td class="align-middle small px-3 py-2">
                                                {{-- Accessing applicationItems as per your Livewire component's eager loading --}}
                                                @if ($application->applicationItems->where('quantity_approved', '>', 0)->isNotEmpty())
                                                    <ul class="list-unstyled mb-0 ps-0">
                                                        @foreach ($application->applicationItems->where('quantity_approved', '>', 0) as $item)
                                                            <li>
                                                                {{-- CORRECTED LINE BELOW --}}
                                                                {{ $item->equipment_type ? \App\Models\Equipment::$ASSET_TYPES_LABELS[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type)) : 'N/A' }}
                                                                ({{ __('Diluluskan') }}:
                                                                {{ $item->quantity_approved ?? 'N/A' }})
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end align-middle px-3 py-2">
                                                {{-- Corrected route name based on typical structure from your web.php --}}
                                                @can('processIssuance', $application)
                                                    {{-- Assuming you have a policy --}}
                                                    <a href="{{ route('resource-management.bpm.loan-transactions.issue.form', $application->id) }}"
                                                        class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i>
                                                        {{ __('Keluarkan Peralatan') }}
                                                    </a>
                                                @else
                                                    <span class="text-muted fst-italic">{{ __('Tiada tindakan') }}</span>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($applications->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $applications->links() }}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
