{{-- resources/views/livewire/resource-management/approval/approval-history.blade.php --}}
{{--
    Approval History Component View
    Displays paginated history of approval decisions made by the current user
    Last updated: 2025-08-06 10:16:08 UTC by IzzatFirdaus
--}}

<div>
    {{-- Page Header with Title and Export Button --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4">
        <h1 class="h2 fw-semibold text-dark mb-2 mb-sm-0 d-flex align-items-center">
            <i class="bi bi-clock-history me-2"></i>
            {{ __('Sejarah Kelulusan') }}
        </h1>
        {{-- Future: Export functionality button --}}
        <button wire:click="exportHistory" class="btn btn-outline-primary d-inline-flex align-items-center text-uppercase small fw-semibold mt-2 mt-sm-0 px-3 py-2" disabled>
            <i class="bi bi-download me-2"></i>
            {{ __('Eksport Data') }}
        </button>
    </div>

    {{-- Session Messages --}}
    @if (session()->has('message'))
        <x-alert type="success" :message="session('message')" class="mb-4" :dismissible="true"/>
    @endif
    @if (session()->has('error'))
        <x-alert type="danger" :message="session('error')" class="mb-4" :dismissible="true"/>
    @endif

    {{-- Advanced Filters Card --}}
    <div class="card mb-4 motac-card">
        <div class="card-header py-3 motac-card-header">
            <h5 class="mb-0 fw-semibold d-flex align-items-center">
                <i class="bi bi-funnel-fill me-2"></i>{{ __('Saringan dan Carian') }}
            </h5>
        </div>
        <div class="card-body p-3 motac-card-body">
            <div class="row g-3 align-items-end">
                {{-- Application Type Filter --}}
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label for="filterType" class="form-label form-label-sm">{{ __('Jenis Permohonan') }}</label>
                    <select wire:model.live="filterType" id="filterType" class="form-select form-select-sm">
                        @foreach($applicationTypeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Decision Filter --}}
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label for="filterDecision" class="form-label form-label-sm">{{ __('Keputusan') }}</label>
                    <select wire:model.live="filterDecision" id="filterDecision" class="form-select form-select-sm">
                        @foreach($decisionOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From Filter --}}
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label for="dateFrom" class="form-label form-label-sm">{{ __('Tarikh Dari') }}</label>
                    <input wire:model.live="dateFrom" type="date" id="dateFrom" class="form-control form-control-sm">
                </div>

                {{-- Date To Filter --}}
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <label for="dateTo" class="form-label form-label-sm">{{ __('Tarikh Hingga') }}</label>
                    <input wire:model.live="dateTo" type="date" id="dateTo" class="form-control form-control-sm">
                </div>

                {{-- Search Input --}}
                <div class="col-lg-3 col-md-6">
                    <label for="search" class="form-label form-label-sm">{{ __('Carian (Nama, Tujuan, Tag)') }}</label>
                    <input wire:model.live.debounce.300ms="search" type="text" id="search"
                        placeholder="{{ __('Masukkan kata kunci...') }}"
                        class="form-control form-control-sm">
                </div>

                {{-- Reset Button --}}
                <div class="col-lg-1 col-md-2">
                    <button wire:click="resetFilters" type="button" class="btn btn-sm btn-outline-secondary w-100"
                        title="{{ __('Set Semula Semua Penapis') }}">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div wire:loading.delay.long class="w-100 text-center py-5">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">{{ __('Memuat data...') }}</span>
        </div>
        <p class="mt-2 fs-5">{{ __('Sedang mencari sejarah kelulusan...') }}</p>
    </div>

    {{-- History Results --}}
    <div wire:loading.remove>
        @if ($approvals->isEmpty())
            {{-- Empty State --}}
            <div class="card motac-card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h5 class="text-muted mb-2">{{ __('Tiada Sejarah Kelulusan') }}</h5>
                    <p class="text-muted mb-0">
                        @if($this->search || $this->filterType !== 'all' || $this->filterDecision !== 'all' || $this->dateFrom || $this->dateTo)
                            {{ __('Tiada rekod ditemui untuk kriteria carian semasa. Cuba ubah penapis atau kriteria carian.') }}
                        @else
                            {{ __('Anda belum membuat sebarang keputusan kelulusan lagi.') }}
                        @endif
                    </p>
                    @if($this->search || $this->filterType !== 'all' || $this->filterDecision !== 'all' || $this->dateFrom || $this->dateTo)
                        <button wire:click="resetFilters" class="btn btn-primary btn-sm mt-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>{{ __('Set Semula Penapis') }}
                        </button>
                    @endif
                </div>
            </div>
        @else
            {{-- Results Table --}}
            <div class="card motac-card">
                <div class="card-header bg-light py-3 motac-card-header">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <h5 class="mb-0 fw-medium text-dark d-flex align-items-center">
                            <i class="bi bi-list-ul me-2 text-primary"></i>{{ __('Rekod Kelulusan') }}
                        </h5>
                        <span class="text-muted small">
                            {{ __('Memaparkan :start - :end daripada :total rekod', [
                                'start' => $approvals->firstItem(),
                                'end' => $approvals->lastItem(),
                                'total' => $approvals->total(),
                            ]) }}
                        </span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('ID') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Jenis') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Pemohon') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tujuan/Butiran') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Keputusan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Keputusan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Catatan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loading progress bar --}}
                            <tr wire:loading.class.delay="opacity-50" class="transition-opacity">
                                <td colspan="7" class="p-0" style="border:none;">
                                    <div wire:loading.flex class="progress" style="height: 2px; width: 100%;" role="progressbar" aria-label="Loading...">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"></div>
                                    </div>
                                </td>
                            </tr>

                            {{-- Approval Records --}}
                            @foreach ($approvals as $approval)
                                @php
                                    $approvable = $approval->approvable;
                                    $applicant = $approvable?->user;
                                @endphp
                                <tr wire:key="approval-history-{{ $approval->id }}">
                                    {{-- Approval ID --}}
                                    <td class="px-3 py-2 small">
                                        <strong>#{{ $approval->id }}</strong>
                                        @if($approvable)
                                            <br><small class="text-muted">App #{{ $approvable->id }}</small>
                                        @endif
                                    </td>

                                    {{-- Application Type with Icon --}}
                                    <td class="px-3 py-2 small">
                                        @if ($approvable instanceof \App\Models\LoanApplication)
                                            <i class="bi bi-laptop text-primary me-1"></i>
                                            <span class="fw-medium">{{ __('Pinjaman') }}</span>
                                        @else
                                            <i class="bi bi-file-earmark-text text-secondary me-1"></i>
                                            <span class="fw-medium">{{ __('Lain-lain') }}</span>
                                        @endif
                                    </td>

                                    {{-- Applicant Information --}}
                                    <td class="px-3 py-2 small">
                                        @if($applicant)
                                            <div class="fw-medium text-dark">{{ $applicant->name }}</div>
                                            <div class="text-muted" style="font-size: 0.85em;">{{ $applicant->email }}</div>
                                            @if($applicant->department)
                                                <div class="text-muted" style="font-size: 0.8em;">{{ $applicant->department->name }}</div>
                                            @endif
                                        @else
                                            <span class="text-muted fst-italic">{{ __('Maklumat tidak tersedia') }}</span>
                                        @endif
                                    </td>

                                    {{-- Application Purpose/Details --}}
                                    <td class="px-3 py-2 small" style="max-width: 250px;">
                                        @if ($approvable instanceof \App\Models\LoanApplication)
                                            <div class="fw-medium">{{ Str::limit($approvable->purpose ?? 'N/A', 60) }}</div>
                                            @if($approvable->loanApplicationItems && $approvable->loanApplicationItems->count() > 0)
                                                <small class="text-muted">
                                                    {{ $approvable->loanApplicationItems->count() }} {{ __('item') }}
                                                    - {{ $approvable->loan_start_date?->format('d/m/Y') }} hingga {{ $approvable->loan_end_date?->format('d/m/Y') }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">{{ __('Butiran tidak tersedia') }}</span>
                                        @endif
                                    </td>

                                    {{-- Decision with Status Badge --}}
                                    <td class="px-3 py-2 small">
                                        @php
                                            $badgeClass = match($approval->decision) {
                                                \App\Models\Approval::STATUS_APPROVED => 'bg-success',
                                                \App\Models\Approval::STATUS_REJECTED => 'bg-danger',
                                                \App\Models\Approval::STATUS_PENDING => 'bg-warning text-dark',
                                                default => 'bg-secondary'
                                            };
                                            $decisionText = match($approval->decision) {
                                                \App\Models\Approval::STATUS_APPROVED => __('Diluluskan'),
                                                \App\Models\Approval::STATUS_REJECTED => __('Ditolak'),
                                                \App\Models\Approval::STATUS_PENDING => __('Menunggu'),
                                                default => __('Tidak Diketahui')
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} rounded-pill">{{ $decisionText }}</span>
                                    </td>

                                    {{-- Decision Date --}}
                                    <td class="px-3 py-2 small text-muted">
                                        {{ $approval->updated_at->translatedFormat('d M Y') }}
                                        <br><small>{{ $approval->updated_at->translatedFormat('H:i A') }}</small>
                                    </td>

                                    {{-- Approval Notes --}}
                                    <td class="px-3 py-2 small" style="max-width: 200px;">
                                        @if($approval->notes)
                                            <div class="text-truncate" title="{{ $approval->notes }}">
                                                {{ Str::limit($approval->notes, 50) }}
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">{{ __('Tiada catatan') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($approvals->hasPages())
                    <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                        {{ $approvals->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

{{-- Custom Styles for Better UX --}}
@push('page-style')
<style>
    /* Enhance table readability */
    .table tbody tr:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }

    /* Better badge styling */
    .badge {
        font-size: 0.75em;
        font-weight: 500;
    }

    /* Loading animation improvements */
    .transition-opacity {
        transition: opacity 0.3s ease-in-out;
    }

    /* Responsive text truncation */
    @media (max-width: 768px) {
        .table td {
            font-size: 0.85rem;
        }
    }
</style>
@endpush
