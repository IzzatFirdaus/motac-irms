<div>
    @section('title', __('Status Permohonan Pinjaman Saya'))

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
        <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-card-checklist me-2"></i>
            {{ __('Senarai Permohonan Pinjaman Saya') }}
        </h1>
        @can('create', App\Models\LoanApplication::class)
            <a href="{{ route('loan-applications.create') }}"
                class="btn btn-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2 motac-btn-primary">
                <i class="bi bi-plus-circle-fill {{ app()->getLocale() === 'ar' ? 'ms-2' : 'me-2' }}"></i>
                {{ __('Mohon Pinjaman Baru') }}
            </a>
        @endcan
    </div>

    @include('_partials._alerts.alert-general')

    {{-- Filters and Search Card --}}
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3 motac-card-header d-flex align-items-center">
            <i class="bi bi-funnel-fill me-2 text-primary"></i>
            <h5 class="mb-0 fw-medium text-dark">{{ __('Penapisan & Carian Permohonan') }}</h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5 col-sm-12">
                    <label for="loanSearchTerm" class="form-label motac-form-label small fw-medium">{{ __('Carian (ID, Tujuan, Lokasi)') }}</label>
                    <input wire:model.live.debounce.300ms="searchTerm" type="text" id="loanSearchTerm"
                        placeholder="{{ __('Taip kata kunci carian...') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-5 col-sm-12">
                    <label for="loanFilterStatus" class="form-label motac-form-label small fw-medium">{{ __('Tapis mengikut Status') }}</label>
                    <select wire:model.live="filterStatus" id="loanFilterStatus" class="form-select form-select-sm">
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}">{{ __($label) }}</option>
                        @endforeach
                    </select>
                </div>
                 <div class="col-md-2 col-sm-12">
                    <button wire:click="resetFilters" wire:loading.attr="disabled"
                        class="btn btn-sm btn-outline-secondary w-100 motac-btn-outline"
                        title="{{ __('Set Semula Penapis') }}">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        <span wire:loading wire:target="resetFilters" class="spinner-border spinner-border-sm"
                            role="status" aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="resetFilters">{{ __('Set Semula') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loan Applications Table Card --}}
    <div class="card shadow-sm motac-card">
        <div class="card-header bg-light py-3 d-flex flex-wrap justify-content-between align-items-center motac-card-header">
            <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Rekod Permohonan Dikemukakan') }}
            </h5>
            @if ($applications->total() > 0)
                <span class="text-muted small">
                    {{ __('Memaparkan :start - :end daripada :total rekod', [
                        'start' => $applications->firstItem(),
                        'end' => $applications->lastItem(),
                        'total' => $applications->total(),
                    ]) }}
                </span>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tujuan') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Mula Pinjam') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Hantar Balik') }}</th>
                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Status') }}</th>
                        <th scope="col" class="text-center small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tindakan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                        <td colspan="6" class="p-0 border-0">
                            <div wire:loading.flex class="progress bg-transparent rounded-0" style="height: 3px; width: 100%;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" style="width: 100%"
                                    aria-label="{{ __('Memuatkan Data Permohonan...') }}"></div>
                            </div>
                        </td>
                    </tr>
                    @forelse ($applications as $application)
                        <tr wire:key="loan-app-{{ $application->id }}">
                            <td class="px-3 py-2 align-middle small text-dark fw-medium">#{{ $application->id }}</td>
                            <td class="px-3 py-2 align-middle small text-muted" style="max-width: 300px; white-space: normal;">
                                {{ Str::limit($application->purpose, 70) }}
                            </td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                {{ $application->loan_start_date ? Carbon\Carbon::parse($application->loan_start_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) : 'N/A' }}
                            </td>
                            <td class="px-3 py-2 align-middle small text-muted">
                                {{ $application->loan_end_date ? Carbon\Carbon::parse($application->loan_end_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) : 'N/A' }}
                            </td>
                            <td class="px-3 py-2 align-middle small text-center">
                                {{-- ======================================================== --}}
                                {{-- == THE FIX IS APPLIED IN THE LINE BELOW == --}}
                                {{-- ======================================================== --}}
                                <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($application->status ?? 'default', 'loan') }}">
                                    {{ $application->status_label }}
                                </span>
                            </td>
                            <td class="px-3 py-2 align-middle text-center">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @can('view', $application)
                                    <a href="{{ route('loan-applications.show', $application->id) }}"
                                        class="btn btn-sm btn-outline-info border-0 p-1 motac-btn-icon"
                                        title="{{ __('Lihat Detail') }}">
                                        <i class="bi bi-eye-fill fs-6"></i>
                                    </a>
                                    @endcan

                                    {{-- Action buttons for approve/reject --}}
                                    @if ($application->can_act_on)
                                        <button wire:click="showApproveModal({{ $application->id }})"
                                                class="btn btn-sm btn-outline-success border-0 p-1 motac-btn-icon"
                                                title="{{ __('Luluskan Permohonan') }}">
                                            <i class="bi bi-check-circle-fill fs-6"></i>
                                        </button>
                                        <button wire:click="showRejectModal({{ $application->id }})"
                                                class="btn btn-sm btn-outline-danger border-0 p-1 motac-btn-icon"
                                                title="{{ __('Tolak Permohonan') }}">
                                            <i class="bi bi-x-circle-fill fs-6"></i>
                                        </button>
                                    @endif

                                    @if ($application->status === \App\Models\LoanApplication::STATUS_DRAFT)
                                        @can('update', $application)
                                            <a href="{{ route('loan-applications.edit', $application->id) }}"
                                                class="btn btn-sm btn-outline-primary border-0 p-1 ms-1 motac-btn-icon"
                                                title="{{ __('Kemaskini Draf') }}">
                                                <i class="bi bi-pencil-square fs-6"></i>
                                            </a>
                                        @endcan
                                        @can('delete', $application)
                                            <button
                                                wire:click="$dispatch('open-delete-modal', { id: {{ $application->id }}, modelClass: 'App\\Models\\LoanApplication', itemDescription: '{{ __('Permohonan Pinjaman #') . $application->id }}', deleteMethod: 'deleteLoanApplication' })"
                                                type="button" class="btn btn-sm btn-outline-danger border-0 p-1 ms-1 motac-btn-icon"
                                                title="{{ __('Padam Draf') }}">
                                                <i class="bi bi-trash3-fill fs-6"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-5 text-center">
                                <div class="d-flex flex-column align-items-center text-muted small">
                                    <i class="bi bi-journal-x fs-1 mb-2 text-secondary"></i>
                                     @if (empty($searchTerm) && ($filterStatus === '' || $filterStatus === 'all'))
                                        <p>{{ __('Tiada rekod permohonan pinjaman ditemui.') }}</p>
                                        @can('create', App\Models\LoanApplication::class)
                                            <p>{{ __('Sila') }} <a href="{{ route('loan-applications.create') }}">{{ __('buat permohonan baharu') }}</a>.</p>
                                        @endcan
                                    @else
                                        <p>{{ __('Tiada rekod permohonan ditemui yang sepadan dengan kriteria carian/penapisan anda.') }}</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         @if ($applications->hasPages())
            <div class="card-footer bg-light border-top py-3 motac-card-footer d-flex justify-content-center">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

    {{-- Approval Action Modal --}}
    @if($showApprovalActionModal)
    <div class="modal fade show" id="approvalActionModal" tabindex="-1" aria-labelledby="approvalActionModalLabel" style="display: block;" aria-modal="true" role="dialog" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalActionModalLabel">
                        @if($approvalActionType === 'approve')
                            {{ __('Luluskan Permohonan') }} #{{ $selectedApplicationId }}
                        @else
                            {{ __('Tolak Permohonan') }} #{{ $selectedApplicationId }}
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeApprovalActionModal" aria-label="{{__('Tutup')}}"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="submitApprovalAction">
                        @csrf
                        <div class="mb-3">
                            <label for="approvalCommentsModal" class="form-label">{{ __('Komen') }} @if($approvalActionType === 'reject') <span class="text-danger">*</span> @endif</label>
                            <textarea wire:model="approvalComments" id="approvalCommentsModal" class="form-control @error('approvalComments') is-invalid @enderror" rows="3" placeholder="{{ __('Masukkan komen anda di sini...') }}"></textarea>
                            @error('approvalComments') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" wire:click="closeApprovalActionModal">{{ __('Batal') }}</button>
                            <button type="submit" class="btn btn-{{ $approvalActionType === 'approve' ? 'success' : 'danger' }}">
                                <span wire:loading wire:target="submitApprovalAction" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span wire:loading.remove wire:target="submitApprovalAction">
                                    @if($approvalActionType === 'approve')
                                        {{ __('Luluskan') }}
                                    @else
                                        {{ __('Tolak') }}
                                    @endif
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" wire:ignore.self style="{{ $showApprovalActionModal ? '' : 'display:none;' }}"></div>
    @endif
</div>
