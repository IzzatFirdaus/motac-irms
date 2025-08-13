{{-- resources/views/livewire/resource-management/approval/approval-dashboard.blade.php --}}
{{-- Approval Dashboard: displays approval tasks for officers. Used by /approvals route. --}}

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
    @include('_partials._alerts.alert-general')

    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <h4 class="fw-bold mb-0 d-flex align-items-center">
            <i class="bi bi-check2-square me-2"></i>
            {{ __('approvals.table.title') }}
        </h4>
    </div>

    <div class="card mb-4 motac-card">
        <div class="card-body motac-card-body">
            {{-- Filters for approval tasks --}}
            <form wire:submit.prevent class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filterTypeApproval" class="form-label form-label-sm">{{ __('approvals.filter.by_type') }}</label>
                    <select wire:model.live="filterType" id="filterTypeApproval" class="form-select form-select-sm">
                        <option value="all">{{ __('common.all_types') }}</option>
                        <option value="loan_application">{{ __('common.loan_application') }}</option>
                        {{-- <option value="helpdesk_ticket">{{ __('common.helpdesk_ticket') }}</option> --}}
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
                    <input wire:model.live.debounce.300ms="searchTerm" id="searchApprovals" type="text"
                           placeholder="{{ __('approvals.filter.placeholder') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="button" wire:click="$set('searchTerm', '')" class="btn btn-sm btn-outline-secondary w-100">{{ __('common.reset') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Loading spinner while tasks load --}}
    <div wire:loading.delay.long class="w-100 text-center py-5">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">{{ __('common.loading') }}</span>
        </div>
        <p class="mt-2 fs-5">{{ __('approvals.loading_text') }}</p>
    </div>

    {{-- Approval tasks table --}}
    <div wire:loading.remove>
        @if ($this->approvalTasks->isEmpty())
            <div class="alert alert-info d-flex align-items-center">
                 <span class="alert-icon me-2"><i class="bi bi-info-circle-fill fs-4"></i></span>
                {{ __('approvals.no_tasks') }}
            </div>
        @else
            <div class="card motac-card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
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
                                        @if ($approvable instanceof \App\Models\LoanApplication)
                                            <i class="bi bi-laptop text-primary me-1"></i>{{ __('common.loan_application') }}
                                        {{-- @elseif ($approvable instanceof \App\Models\Ticket)
                                            <i class="bi bi-ticket-fill text-success me-1"></i>{{ __('common.helpdesk_ticket') }} --}}
                                        @else
                                            <i class="bi bi-file-earmark-text-fill text-secondary me-1"></i>{{ __('common.unknown') }}
                                        @endif
                                        @if ($approvable) - #{{ $approvable->id }} @endif
                                    </td>
                                    <td class="px-3 py-2 small">{{ optional(optional($approvable)->user)->name ?? (optional($approvable)->applicant_name ?? __('common.not_available')) }}</td>
                                    <td class="px-3 py-2 small">{{ $approvalTask->stage_translated }}</td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge {{ $approvalTask->status_color_class }}">{{ $approvalTask->status_translated }}</span>
                                    </td>
                                    <td class="px-3 py-2 small">{{ $approvalTask->created_at->translatedFormat(config('motac.datetime_format_my', 'd/m/Y H:i A')) }}</td>
                                    <td class="text-center px-3 py-2">
                                        <button type="button" class="btn btn-sm btn-primary"
                                                wire:click="openApprovalModal({{ $approvalTask->id }}, '{{ \App\Models\Approval::STATUS_PENDING }}')">
                                            <i class="bi bi-eye me-1"></i> {{ __('common.view_action') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($this->approvalTasks->hasPages())
                    <div class="card-footer bg-light border-top d-flex justify-content-center py-2 motac-card-footer">
                        {{ $this->approvalTasks->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Approval Action Modal --}}
    <div wire:ignore.self class="modal fade" id="approvalActionBootstrapModal" tabindex="-1" aria-labelledby="approvalActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="approvalActionModalLabel">{{ __('approvals.modal.title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($currentApprovalTask)
                        @php $modalApprovable = $currentApprovalTask->approvable; @endphp
                        <h6 class="fw-semibold mb-3">{{ __('approvals.modal.details') }}</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 small text-muted">{{ __('approvals.modal.application_type') }}:</p>
                                <p class="fw-medium text-dark">
                                    @if ($modalApprovable instanceof \App\Models\LoanApplication)
                                        <i class="bi bi-laptop text-primary me-1"></i>{{ __('common.loan_application') }}
                                    {{-- @elseif ($modalApprovable instanceof \App\Models\Ticket)
                                        <i class="bi bi-ticket-fill text-success me-1"></i>{{ __('common.helpdesk_ticket') }} --}}
                                    @else
                                        <i class="bi bi-file-earmark-text-fill text-secondary me-1"></i>{{ __('common.unknown') }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 small text-muted">{{ __('approvals.modal.applicant') }}:</p>
                                <p class="fw-medium text-dark">{{ optional(optional($modalApprovable)->user)->name ?? (optional($modalApprovable)->applicant_name ?? __('common.not_available')) }}</p>
                            </div>
                        </div>

                        {{-- Dynamic Content based on Approvable Type --}}
                        @if ($modalApprovable instanceof \App\Models\LoanApplication)
                            @livewire('resource-management.loan-application.loan-application-details', ['loanApplicationId' => $modalApprovable->id], key(['loan-app-details-'.$modalApprovable->id]))
                        {{-- @elseif ($modalApprovable instanceof \App\Models\Ticket)
                            @livewire('helpdesk.ticket-details', ['ticketId' => $modalApprovable->id, 'isModal' => true], key(['helpdesk-ticket-details', $modalApprovable->id])) --}}
                        @else
                            <div class="alert alert-warning small">{{ __('approvals.modal.no_details_available') }}</div>
                        @endif

                        <hr class="my-4">

                        <h6 class="fw-semibold mb-3">{{ __('approvals.modal.current_approval_stage') }}</h6>
                        <p class="mb-1 small text-muted">{{ __('approvals.modal.stage') }}: <span class="fw-medium text-dark">{{ $currentApprovalTask->stage_translated }}</span></p>
                        <p class="mb-1 small text-muted">{{ __('approvals.modal.status') }}: <span class="badge {{ $currentApprovalTask->status_color_class }}">{{ $currentApprovalTask->status_translated }}</span></p>
                        <p class="mb-3 small text-muted">{{ __('approvals.modal.received_on') }}: <span class="fw-medium text-dark">{{ $currentApprovalTask->created_at->translatedFormat(config('motac.datetime_format_my', 'd/m/Y H:i A')) }}</span></p>

                        @if ($currentApprovalTask->status === App\Models\Approval::STATUS_PENDING)
                            <div class="mb-3">
                                <label for="approvalNotes" class="form-label form-label-sm">{{ __('approvals.modal.notes') }} <span class="text-muted">({{ __('common.optional') }})</span></label>
                                <textarea wire:model="approvalNotes" id="approvalNotes" class="form-control form-control-sm" rows="3" placeholder="{{ __('approvals.modal.notes_placeholder') }}"></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" wire:click="processApproval" class="btn btn-success d-inline-flex align-items-center" wire:loading.attr="disabled" wire:target="processApproval">
                                    <span wire:loading wire:target="processApproval" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <i wire:loading.remove wire:target="processApproval" class="bi bi-check-circle me-1"></i>
                                    {{ __('common.approve') }}
                                </button>
                                <button type="button" wire:click="processApproval" class="btn btn-danger d-inline-flex align-items-center" wire:loading.attr="disabled" wire:target="processApproval">
                                    <span wire:loading wire:target="processApproval" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    <i wire:loading.remove wire:target="processApproval" class="bi bi-x-circle me-1"></i>
                                    {{ __('common.reject') }}
                                </button>
                            </div>
                        @else
                            <div class="alert alert-info small">{{ __('approvals.modal.task_already_processed') }}</div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">{{ __('approvals.modal.select_task_to_view') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
