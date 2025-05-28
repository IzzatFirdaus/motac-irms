<<<<<<< HEAD
@push('page-style')
    <style>
        #approvalActionBootstrapModal .modal-dialog {
            max-height: 90vh;
        }
        #approvalActionBootstrapModal .modal-body {
            overflow-y: auto;
        }
    </style>
@endpush

<div>
    {{-- The @section('title') is handled by the component's title() method --}}

    @include('_partials._alerts.alert-general')

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-check2-square me-2"></i>
            {{-- ADJUSTED: Using standardized translation key --}}
            {{ __('approvals.table.title') }}
        </h4>
    </div>

    <div class="card mb-4 motac-card">
        <div class="card-body motac-card-body">
            <form wire:submit.prevent class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filterTypeApproval" class="form-label form-label-sm">{{ __('approvals.filter.by_type') }}</label>
                    <select wire:model.live="filterType" id="filterTypeApproval" class="form-select form-select-sm">
                        <option value="all">{{ __('common.all_types') }}</option>
                        <option value="email_application">{{ __('common.email_id_application') }}</option>
                        <option value="loan_application">{{ __('common.loan_application') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatusApproval" class="form-label form-label-sm">{{ __('approvals.filter.by_status') }}</label>
                    <select wire:model.live="filterStatus" id="filterStatusApproval" class="form-select form-select-sm">
                        <option value="all">{{ __('common.all_statuses') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_PENDING }}">{{ __('common.pending') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_APPROVED }}">{{ __('common.approved') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_REJECTED }}">{{ __('common.rejected') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchApprovals" class="form-label form-label-sm">{{ __('approvals.filter.advanced_search') }}</label>
                    <input wire:model.live.debounce.300ms="search" id="searchApprovals" type="text"
                           placeholder="{{ __('approvals.filter.placeholder') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="$set('search', '')" class="btn btn-sm btn-outline-secondary w-100">{{ __('common.reset') }}</button>
=======
{{-- resources/views/livewire/resource-management/approval/dashboard.blade.php --}}
<div>
    @php
        $configData = \App\Helpers\Helpers::appClasses();
    @endphp

    @section('title', __('Papan Pemuka Kelulusan'))

    @include('layouts._partials._alerts.alert-general')

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0">{{ __('Senarai Tugasan Kelulusan') }}</h4>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form wire:submit.prevent class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filterTypeApproval" class="form-label">{{ __('Tapis Mengikut Jenis') }}</label>
                    <select wire:model.live="filterType" id="filterTypeApproval" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Jenis') }}</option>
                        <option value="{{ addslashes(App\Models\EmailApplication::class) }}">{{ __('Permohonan E-mel/ID') }}</option>
                        <option value="{{ addslashes(App\Models\LoanApplication::class) }}">{{ __('Permohonan Pinjaman ICT') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterStatusApproval" class="form-label">{{ __('Tapis Mengikut Status') }}</label>
                    <select wire:model.live="filterStatus" id="filterStatusApproval" class="form-select form-select-sm">
                        <option value="">{{ __('Semua Status') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_PENDING }}">{{ __('Menunggu Tindakan') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_APPROVED }}">{{ __('Diluluskan') }}</option>
                        <option value="{{ App\Models\Approval::STATUS_REJECTED }}">{{ __('Ditolak') }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="searchApprovals" class="form-label">{{ __('Carian Terperinci') }}</label>
                    <input wire:model.live.debounce.300ms="search" id="searchApprovals" type="text"
                           placeholder="{{ __('Cari ID Permohonan, Nama Pemohon...') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100">{{ __('Set Semula') }}</button>
>>>>>>> bb90b6b (file edits 280525)
                </div>
            </form>
        </div>
    </div>

    <div wire:loading.delay.long class="w-100 text-center py-5">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
<<<<<<< HEAD
            <span class="visually-hidden">{{ __('common.loading') }}</span>
        </div>
        <p class="mt-2 fs-5">{{ __('approvals.loading_text') }}</p>
    </div>

    <div wire:loading.remove>
        @if ($this->approvalTasks->isEmpty())
            <div class="alert alert-info d-flex align-items-center">
                 <span class="alert-icon me-2"><i class="bi bi-info-circle-fill fs-4"></i></span>
                {{ __('approvals.no_tasks') }}
            </div>
        @else
            <div class="card motac-card">
=======
            <span class="visually-hidden">{{ __('Memuatkan...') }}</span>
        </div>
        <p class="mt-2 fs-5">{{ __('Sedang memuatkan senarai kelulusan...') }}</p>
    </div>

    <div wire:loading.remove>
        @if ($this->pendingApprovalTasks->isEmpty())
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <span class="alert-icon me-2"><i class="ti ti-info-circle ti-md"></i></span>
                <div>{{ __('Tiada tugasan kelulusan yang sepadan dengan tapisan semasa.') }}</div>
            </div>
        @else
            <div class="card">
>>>>>>> bb90b6b (file edits 280525)
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
<<<<<<< HEAD
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('approvals.table.task_id') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('approvals.table.application_type') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('approvals.table.applicant') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('approvals.table.stage') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('approvals.table.status') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('approvals.table.date_received') }}</th>
                                <th class="text-center small text-uppercase text-muted fw-medium px-3 py-2">{{ __('approvals.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($this->approvalTasks as $approvalTask)
                                @php $approvable = $approvalTask->approvable; @endphp
                                <tr wire:key="approval-task-{{ $approvalTask->id }}">
                                    <td class="px-3 py-2 small"><strong>#{{ $approvalTask->id }}</strong></td>
                                    <td class="px-3 py-2 small">
                                        @if ($approvable instanceof \App\Models\EmailApplication) <i class="bi bi-envelope-fill text-info me-1"></i>{{ __('common.email_id_application') }}
                                        @elseif ($approvable instanceof \App\Models\LoanApplication) <i class="bi bi-laptop text-primary me-1"></i>{{ __('common.loan_application') }}
                                        @else <i class="bi bi-file-earmark-text-fill text-secondary me-1"></i>{{ __('common.unknown') }}
                                        @endif
                                        @if ($approvable) - #{{ $approvable->id }} @endif
                                    </td>
                                    <td class="px-3 py-2 small">{{ optional(optional($approvable)->user)->name ?? (optional($approvable)->applicant_name ?? __('common.not_available')) }}</td>
                                    <td class="px-3 py-2 small">{{ $approvalTask->stage_translated }}</td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge {{ $approvalTask->status_color_class }}">{{ $approvalTask->status_translated }}</span>
                                    </td>
                                    <td class="px-3 py-2 small">{{ $approvalTask->created_at->translatedFormat(config('app.date_format_my_short', 'd M Y')) }}</td>
                                    <td class="text-center px-3 py-2">
                                        @if ($approvalTask->status === \App\Models\Approval::STATUS_PENDING)
                                            @can('update', $approvalTask)
                                                <button type="button" wire:click="openApprovalModal({{ $approvalTask->id }})" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil-square me-1"></i>{{ __('approvals.actions.review') }}
                                                </button>
                                            @else
                                                @can('view', $approvalTask)
                                                <a href="{{ route('approvals.show', $approvalTask->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye-fill me-1"></i>{{ __('approvals.actions.view_details') }}
                                                </a>
                                                @else
                                                <span class="text-muted small"><em>{{ __('approvals.actions.no_permission') }}</em></span>
                                                @endcan
                                            @endcan
                                        @else
                                             @if(method_exists($this, 'getViewApplicationRoute') && $this->getViewApplicationRoute($approvalTask))
                                                 <a href="{{ $this->getViewApplicationRoute($approvalTask) }}" class="btn btn-sm btn-outline-secondary">
                                                     <i class="bi bi-eye-fill me-1"></i>{{ __('approvals.actions.view_details') }}
                                                 </a>
                                             @else
                                                <a href="{{ route('approvals.show', $approvalTask->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye-fill me-1"></i>{{ __('approvals.actions.view_task') }}
                                                </a>
                                             @endif
=======
                                <th>{{ __('ID Tugasan') }}</th>
                                <th>{{ __('Jenis & ID Permohonan') }}</th>
                                <th>{{ __('Pemohon') }}</th>
                                <th>{{ __('Peringkat Kelulusan') }}</th>
                                <th>{{ __('Status Tugasan') }}</th>
                                <th>{{ __('Tarikh Diterima') }}</th>
                                <th class="text-center">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($this->pendingApprovalTasks as $approvalTask)
                                @php $approvable = $approvalTask->approvable; @endphp
                                <tr wire:key="approval-task-{{ $approvalTask->id }}">
                                    <td><strong>#{{ $approvalTask->id }}</strong></td>
                                    <td>
                                        @if ($approvable instanceof \App\Models\EmailApplication) <i class="ti ti-mail text-info me-1"></i>{{ __('E-mel/ID') }}
                                        @elseif ($approvable instanceof \App\Models\LoanApplication) <i class="ti ti-device-laptop text-primary me-1"></i>{{ __('Pinjaman ICT') }}
                                        @else <i class="ti ti-file-invoice text-secondary me-1"></i>{{ __('Tidak Diketahui') }}
                                        @endif
                                        @if ($approvable) - #{{ $approvable->id }} @endif
                                    </td>
                                    <td>{{ optional(optional($approvable)->user)->name ?? (optional($approvable)->applicant_name ?? __('N/A')) }}</td>
                                    <td>{{ __(App\Models\Approval::getStageDisplayName($approvalTask->stage)) }}</td>
                                    <td><span class="badge {{ \App\Helpers\Helpers::getBootstrapStatusColorClass('approval_'.$approvalTask->status) }}">{{ __(Str::title(str_replace('_', ' ', $approvalTask->status))) }}</span></td>
                                    <td>{{ $approvalTask->created_at->translatedFormat(config('app.date_format_my_short', 'd M Y')) }}</td>
                                    <td class="text-center">
                                        @if ($approvalTask->status === \App\Models\Approval::STATUS_PENDING)
                                            @can('update', $approvalTask)
                                                <button type="button" wire:click="openApprovalModal({{ $approvalTask->id }})"
                                                        class="btn btn-sm btn-primary">
                                                    <i class="ti ti-edit ti-xs me-1"></i>{{ __('Semak & Bertindak') }}
                                                </button>
                                            @else
                                                <span class="text-muted small"><em>{{ __('Tiada tindakan') }}</em></span>
                                            @endcan
                                        @else
                                             <a href="{{ $this->getViewApplicationRoute($approvalTask) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="ti ti-eye ti-xs me-1"></i>{{ __('Lihat Butiran') }}
                                            </a>
>>>>>>> bb90b6b (file edits 280525)
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
<<<<<<< HEAD
                @if ($this->approvalTasks->hasPages())
                    <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                        {{ $this->approvalTasks->links() }}
=======
                @if ($this->pendingApprovalTasks->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $this->pendingApprovalTasks->links() }}
>>>>>>> bb90b6b (file edits 280525)
                    </div>
                @endif
            </div>
        @endif
    </div>

<<<<<<< HEAD
    {{-- Approval Action Modal --}}
=======
>>>>>>> bb90b6b (file edits 280525)
    @if ($showApprovalModal && $this->currentApprovalDetails)
        <div class="modal fade @if($showApprovalModal) show @endif" id="approvalActionBootstrapModal" tabindex="-1"
             style="display: @if($showApprovalModal) block; background-color: rgba(0,0,0,0.5); @else none; @endif"
             aria-labelledby="approvalActionModalLabel" @if($showApprovalModal) aria-modal="true" role="dialog" @else aria-hidden="true" @endif wire:ignore.self>
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <form wire:submit.prevent="recordDecision">
                        <div class="modal-header">
<<<<<<< HEAD
                            <h5 class="modal-title d-flex align-items-center" id="approvalActionModalLabel">
                                <i class="bi bi-clipboard-check-fill me-2"></i>
                                {{ __('approvals.modal.title') }} #{{ $this->currentApprovalDetails->id }}
                                <small class="d-block text-muted fw-normal mt-1 ms-2">({{ $this->currentApprovalDetails->stage_translated }})</small>
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeApprovalModal" aria-label="{{__('common.close')}}"></button>
                        </div>
                        <div class="modal-body">
                            @php $approvableItemFromDetails = $this->currentApprovalDetails->approvable; @endphp
                            @if ($approvableItemFromDetails)
                                <div class="mb-3 p-3 border rounded bg-light-subtle">
                                    <h6 class="mb-2 fw-semibold">{{ __('approvals.modal.app_details') }}</h6>
                                    <dl class="row mb-0 small">
                                        <dt class="col-sm-4">{{ __('approvals.modal.app_type') }}</dt>
                                        <dd class="col-sm-8">
                                            @if ($approvableItemFromDetails instanceof \App\Models\EmailApplication) <i class="bi bi-envelope-fill text-info me-1"></i>{{ __('common.email_id_application') }}
                                            @elseif ($approvableItemFromDetails instanceof \App\Models\LoanApplication) <i class="bi bi-laptop text-primary me-1"></i>{{ __('common.loan_application') }}
                                            @endif
                                            (#{{ $approvableItemFromDetails->id }})
                                        </dd>
                                        <dt class="col-sm-4">{{ __('approvals.modal.applicant') }}</dt>
                                        <dd class="col-sm-8">{{ optional($approvableItemFromDetails->user)->name ?? (optional($approvableItemFromDetails)->applicant_name ?? __('common.not_available')) }}</dd>

                                        @if ($approvableItemFromDetails instanceof \App\Models\EmailApplication)
                                            <dt class="col-sm-4">{{ __('approvals.modal.proposed_email') }}</dt>
                                            <dd class="col-sm-8">{{ $approvableItemFromDetails->proposed_email ?? __('common.not_available') }}</dd>
                                            <dt class="col-sm-4">{{ __('approvals.modal.purpose_notes') }}</dt>
                                            <dd class="col-sm-8">{{ $approvableItemFromDetails->application_reason_notes ?? __('common.not_available') }}</dd>
                                        @elseif ($approvableItemFromDetails instanceof \App\Models\LoanApplication)
                                            <dt class="col-sm-4">{{ __('approvals.modal.loan_purpose') }}</dt>
                                            <dd class="col-sm-8" style="white-space: pre-wrap;">{{ $approvableItemFromDetails->purpose ?? __('common.not_available') }}</dd>
                                            <dt class="col-sm-4">{{ __('approvals.modal.usage_location') }}</dt>
                                            <dd class="col-sm-8">{{ $approvableItemFromDetails->location ?? __('common.not_available') }}</dd>
                                            <dt class="col-sm-4">{{ __('approvals.modal.loan_period') }}</dt>
                                            <dd class="col-sm-8">
                                                {{ optional($approvableItemFromDetails->loan_start_date)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }} -
                                                {{ optional($approvableItemFromDetails->loan_end_date)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }}
                                            </dd>
                                            @if ($this->approvalDecision === \App\Models\Approval::STATUS_APPROVED && $approvableItemFromDetails->loanApplicationItems->isNotEmpty())
                                                <dt class="col-sm-12 mt-3 fw-semibold">{{ __('approvals.modal.quantity_adjustment') }}:</dt>
                                                <dd class="col-sm-12">
                                                    @foreach ($this->approvalItems as $index => $approvalItemData)
                                                        <div class="mb-2 p-2 border rounded-1 bg-white">
                                                            @php
                                                                $originalLoanItem = $approvableItemFromDetails->loanApplicationItems->firstWhere('id', $approvalItemData['loan_application_item_id']);
                                                            @endphp
                                                            @if($originalLoanItem)
                                                            <p class="mb-1 small">
                                                                <strong>{{ __('approvals.modal.item') }}:</strong> {{ e(optional(\App\Models\Equipment::getAssetTypeOptions())[$originalLoanItem->equipment_type] ?? $originalLoanItem->equipment_type) }} <br>
                                                                <strong>{{ __('approvals.modal.quantity_requested') }}:</strong> {{ $originalLoanItem->quantity_requested }}
                                                            </p>
                                                            <label for="approvalItems_{{ $index }}_quantity_approved" class="form-label form-label-sm">{{ __('approvals.modal.quantity_approved', ['qty' => $originalLoanItem->quantity_requested]) }}:</label>
                                                            <input type="number" wire:model.lazy="approvalItems.{{ $index }}.quantity_approved" id="approvalItems_{{ $index }}_quantity_approved" class="form-control form-control-sm @error('approvalItems.'.$index.'.quantity_approved') is-invalid @enderror" min="0" max="{{ $originalLoanItem->quantity_requested }}">
                                                            @error('approvalItems.'.$index.'.quantity_approved')
                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                            @enderror
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </dd>
                                            @elseif ($approvableItemFromDetails->loanApplicationItems->isNotEmpty())
                                                <dt class="col-sm-12 mt-2">{{ __('approvals.modal.requested_items') }}</dt>
                                                <dd class="col-sm-12">
                                                    <ul class="list-unstyled ps-3 mb-0">
                                                        @foreach ($approvableItemFromDetails->loanApplicationItems as $loanItem)
                                                            <li>- {{ $loanItem->equipment_type ? (\App\Models\Equipment::getAssetTypeOptions()[$loanItem->equipment_type] ?? Str::title(str_replace('_', ' ', $loanItem->equipment_type))) : 'N/A' }}
                                                                ({{ __('approvals.modal.quantity_requested') }}: {{ $loanItem->quantity_requested }})
                                                            </li>
=======
                            <h5 class="modal-title" id="approvalActionModalLabel">
                                {{ __('Semak Tugasan Kelulusan') }} #{{ $this->currentApprovalDetails->id }}
                                <small class="d-block text-muted">{{ __(App\Models\Approval::getStageDisplayName($this->currentApprovalDetails->stage)) }}</small>
                            </h5>
                            <button type="button" class="btn-close" wire:click="closeApprovalModal" aria-label="{{__('Tutup')}}"></button>
                        </div>
                        <div class="modal-body">
                            @php $item = $this->currentApprovable; @endphp
                            @if ($item)
                                <div class="mb-3 p-3 border rounded bg-light-subtle">
                                    <h6 class="mb-2 fw-semibold">{{ __('Butiran Permohonan') }}</h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4">{{ __('Jenis Permohonan') }}</dt>
                                        <dd class="col-sm-8">
                                            @if ($item instanceof \App\Models\EmailApplication) {{ __('Permohonan E-mel/ID') }}
                                            @elseif ($item instanceof \App\Models\LoanApplication) {{ __('Permohonan Pinjaman ICT') }}
                                            @endif
                                            (#{{ $item->id }})
                                        </dd>
                                        <dt class="col-sm-4">{{ __('Pemohon') }}</dt>
                                        <dd class="col-sm-8">{{ optional($item->user)->name ?? (optional($item)->applicant_name ?? __('N/A')) }}</dd>
                                        @if ($item instanceof \App\Models\EmailApplication)
                                            <dt class="col-sm-4">{{ __('E-mel Dicadang') }}</dt>
                                            <dd class="col-sm-8">{{ $item->proposed_email ?? __('N/A') }}</dd>
                                        @elseif ($item instanceof \App\Models\LoanApplication)
                                            <dt class="col-sm-4">{{ __('Tujuan Pinjaman') }}</dt>
                                            <dd class="col-sm-8">{{ Str::limit($item->purpose, 150) ?? __('N/A') }}</dd>
                                            <dt class="col-sm-4">{{ __('Lokasi Penggunaan') }}</dt>
                                            <dd class="col-sm-8">{{ $item->location ?? __('N/A') }}</dd>
                                            <dt class="col-sm-4">{{ __('Tempoh Pinjaman') }}</dt>
                                            <dd class="col-sm-8">
                                                {{ optional($item->loan_start_date)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }} -
                                                {{ optional($item->loan_end_date)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }}
                                            </dd>
                                            @if ($item->applicationItems && $item->applicationItems->count() > 0)
                                                <dt class="col-sm-12 mt-2">{{ __('Item Dipohon:') }}</dt>
                                                <dd class="col-sm-12">
                                                    <ul class="list-unstyled ps-3">
                                                        @foreach ($item->applicationItems as $loanItem)
                                                            <li>- {{ $loanItem->equipment_type }} ({{ __('Kuantiti') }}: {{ $loanItem->quantity_requested }})</li>
>>>>>>> bb90b6b (file edits 280525)
                                                        @endforeach
                                                    </ul>
                                                </dd>
                                            @endif
                                        @endif
<<<<<<< HEAD
                                        <dt class="col-sm-4 mt-2">{{ __('approvals.modal.date_applied') }}</dt>
                                        <dd class="col-sm-8 mt-2">{{ optional($approvableItemFromDetails->created_at)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }}</dd>
                                    </dl>
                                    @if(method_exists($this, 'getViewApplicationRoute') && $this->getViewApplicationRoute($this->currentApprovalDetails))
                                        <a href="{{ $this->getViewApplicationRoute($this->currentApprovalDetails) }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('approvals.actions.view_full_app') }}
                                        </a>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted">{{ __('approvals.notifications.load_error') }}</p>
                            @endif
                            <hr>
                            <div class="mb-3">
                                <label for="approvalDecisionModal" class="form-label fw-semibold">{{ __('approvals.modal.your_decision') }} <span class="text-danger">*</span></label>
                                <select wire:model.live="approvalDecision" id="approvalDecisionModal" class="form-select @error('approvalDecision') is-invalid @enderror">
                                    <option value="">-- {{ __('approvals.modal.select_decision') }} --</option>
                                    @foreach(\App\Models\Approval::getDecisionStatuses() as $statusValue => $statusLabel)
                                        <option value="{{ $statusValue }}">{{ __($statusLabel) }}</option>
                                    @endforeach
=======
                                        <dt class="col-sm-4 mt-2">{{ __('Tarikh Mohon') }}</dt>
                                        <dd class="col-sm-8 mt-2">{{ optional($item->created_at)->translatedFormat(config('app.datetime_format_my', 'd M Y, g:i A')) }}</dd>
                                    </dl>
                                    <a href="{{ $this->getViewApplicationRoute($this->currentApprovalDetails) }}" target="_blank" class="btn btn-sm btn-outline-info mt-2">
                                        <i class="ti ti-external-link ti-xs me-1"></i>{{ __('Lihat Permohonan Penuh') }}
                                    </a>
                                </div>
                            @else
                                <p class="text-muted">{{ __('Butiran permohonan tidak dapat dimuatkan.') }}</p>
                            @endif
                            <hr>
                            <div class="mb-3">
                                <label for="approvalDecisionModal" class="form-label fw-semibold">{{ __('Keputusan Anda') }} <span class="text-danger">*</span></label>
                                <select wire:model.lazy="approvalDecision" id="approvalDecisionModal" class="form-select @error('approvalDecision') is-invalid @enderror">
                                    <option value="">-- {{ __('Pilih Keputusan') }} --</option>
                                    <option value="{{ \App\Models\Approval::STATUS_APPROVED }}">{{ __('Luluskan') }}</option>
                                    <option value="{{ \App\Models\Approval::STATUS_REJECTED }}">{{ __('Tolak') }}</option>
>>>>>>> bb90b6b (file edits 280525)
                                </select>
                                @error('approvalDecision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-0">
<<<<<<< HEAD
                                <label for="approvalCommentsModal" class="form-label fw-semibold">
                                    {{ __('approvals.modal.comments') }}
                                    @if ($approvalDecision === \App\Models\Approval::STATUS_REJECTED) <span class="text-danger">* {{ __('approvals.modal.comments_required_if_rejected') }}</span>@endif
                                </label>
                                <textarea wire:model.defer="approvalComments" id="approvalCommentsModal" rows="3" class="form-control @error('approvalComments') is-invalid @enderror" placeholder="{{ __('approvals.modal.comments_placeholder') }}"></textarea>
=======
                                <label for="approvalCommentsModal" class="form-label fw-semibold">{{ __('Ulasan') }} @if ($approvalDecision === \App\Models\Approval::STATUS_REJECTED) <span class="text-danger">*</span>@endif</label>
                                <textarea wire:model.lazy="approvalComments" id="approvalCommentsModal" rows="3" class="form-control @error('approvalComments') is-invalid @enderror" placeholder="{{ __('Sila berikan ulasan anda...') }}"></textarea>
>>>>>>> bb90b6b (file edits 280525)
                                @error('approvalComments') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div wire:loading wire:target="recordDecision" class="text-muted small mt-2">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
<<<<<<< HEAD
                                {{ __('approvals.modal.processing') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeApprovalModal">{{ __('common.cancel') }}</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="recordDecision">
                                <i class="bi bi-send-check-fill me-1"></i>
                                {{ __('approvals.actions.submit_decision') }}
=======
                                {{ __('Memproses keputusan...') }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeApprovalModal">{{ __('Batal') }}</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="recordDecision">
                                {{ __('Hantar Keputusan') }}
>>>>>>> bb90b6b (file edits 280525)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
<<<<<<< HEAD
=======
        <script>
            document.addEventListener('livewire:initialized', () => { // Changed from livewire:load
                const approvalModalElement = document.getElementById('approvalActionBootstrapModal');
                let approvalModalInstance = null;
                if (approvalModalElement) {
                     approvalModalInstance = new bootstrap.Modal(approvalModalElement);
                }

                @this.on('showApprovalModalJs', () => {
                    if(approvalModalInstance) approvalModalInstance.show();
                });
                @this.on('hideApprovalModalJs', () => {
                    if(approvalModalInstance) approvalModalInstance.hide();
                });
            });
        </script>
>>>>>>> bb90b6b (file edits 280525)
    @endif
</div>
