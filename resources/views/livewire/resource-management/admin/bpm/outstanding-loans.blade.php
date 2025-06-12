<div>
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
                </div>
            </div>
        </div>

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
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
