{{-- resources/views/loan-applications/loan-application-show.blade.php --}}
{{-- Show details for a single ICT loan application --}}
{{-- Code unchanged; only filename is updated, and this comment documents the file purpose. --}}

@extends('layouts.app')

@section('title', __('loan-applications.title_with_id', ['id' => $loanApplication->id]))

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-3 border-bottom">
                <div>
                    <h1 class="h2 fw-bold text-body mb-1 d-flex align-items-center">
                        <i class="bi bi-file-earmark-medical-fill text-primary me-2"></i>{{ __('loan-applications.title') }}
                    </h1>
                    <p class="text-muted mb-0">{{ __('loan-applications.labels.application_id') }}: #{{ $loanApplication->id }}</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <x-resource-status-panel :resource="$loanApplication" statusAttribute="status" type="loan-application"
                        class="fs-5 px-3 py-2 shadow-sm" :showIcon="true" />
                </div>
            </div>

            @include('_partials._alerts.alert-general')

            @if ($loanApplication->isRejected() && $loanApplication->rejection_reason)
                <div class="alert alert-danger bg-danger-subtle border-danger-subtle p-3 mb-4 shadow-sm rounded-3">
                    <h5 class="alert-heading h6 text-danger-emphasis fw-semibold">
                        <i class="bi bi-x-octagon-fill me-2"></i>{{ __('loan-applications.labels.rejection_reason') }}:
                    </h5>
                    <p class="mb-0 text-danger-emphasis small" style="white-space: pre-wrap;">{{ $loanApplication->rejection_reason }}</p>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body p-sm-4 p-3 vstack gap-4">
                    {{-- Section 1: Applicant Information --}}
                    <section aria-labelledby="applicant-information">
                        <h2 id="applicant-information" class="h5 fw-semibold text-body mb-3 border-bottom pb-2">{{ __('loan-applications.sections.applicant') }}</h2>
                        @if ($loanApplication->user)
                            <x-user-info-card :user="$loanApplication->user" :application="$loanApplication" title="" />
                        @else
                            <p class="text-muted small">N/A</p>
                        @endif
                    </section>

                    {{-- Loan Details --}}
                    <section aria-labelledby="loan-details">
                        <h2 id="loan-details" class="h5 fw-semibold text-body mb-3 mt-4 border-bottom pb-2">{{ __('loan-applications.sections.application_details') }}</h2>
                        <dl class="row g-3 small">
                            <dt class="col-sm-12 fw-medium text-muted">{{ __('loan-applications.labels.purpose') }}</dt>
                            <dd class="col-sm-12 text-body bg-body-secondary p-2 rounded-2 border" style="white-space: pre-wrap;">{{ e($loanApplication->purpose ?? 'N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.usage_location') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ e($loanApplication->location ?? 'N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.return_location') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ e($loanApplication->effective_return_location ?? 'N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.loan_datetime') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ optional($loanApplication->loan_start_date)->translatedFormat('d M Y, h:i A') ?? __('N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.return_datetime') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ optional($loanApplication->loan_end_date)->translatedFormat('d M Y, h:i A') ?? __('N/A') }}</dd>
                            <dt class="col-md-4 col-lg-3 fw-medium text-muted">{{ __('loan-applications.labels.submitted_date') }}</dt>
                            <dd class="col-md-8 col-lg-9 text-body">{{ optional($loanApplication->submitted_at)->translatedFormat('d M Y, h:i A') ?? optional($loanApplication->created_at)->translatedFormat('d M Y, h:i A') . ($loanApplication->isDraft() ? ' (' . $loanApplication->status_label . ')' : '') }}</dd>
                        </dl>
                    </section>

                    {{-- Section 3: Requested Equipment --}}
                    <section aria-labelledby="equipment-requested">
                        <h2 id="equipment-requested" class="h5 fw-semibold text-body mb-3 mt-4 border-bottom pb-2">{{ __('loan-applications.sections.equipment_details') }}</h2>
                        @if ($loanApplication->loanApplicationItems->count() > 0)
                            <div class="table-responsive shadow-sm border rounded-3">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="small text-uppercase text-muted fw-semibold ps-3 py-2">#</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('loan-applications.labels.equipment_type') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('loan-applications.labels.requested_qty') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('loan-applications.labels.approved_qty') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2 text-center">{{ __('loan-applications.labels.issued_qty') }}</th>
                                            <th class="small text-uppercase text-muted fw-semibold py-2">{{ __('loan-applications.labels.applicant_notes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($loanApplication->loanApplicationItems as $item)
                                            <tr>
                                                <td class="small text-muted ps-3">{{ $loop->iteration }}.</td>
                                                <td class="small text-body">{{ e(\App\Models\Equipment::getAssetTypeOptions()[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type))) }}</td>
                                                <td class="small text-body text-center">{{ $item->quantity_requested ?? '0' }}</td>
                                                <td class="small text-body text-center">{{ $item->quantity_approved ?? '-' }}</td>
                                                <td class="small text-body text-center">{{ $item->quantity_issued ?? '0' }}</td>
                                                <td class="small text-muted" style="white-space: pre-wrap;">{{ e($item->notes ?? '-') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted small"><i class="bi bi-info-circle me-1"></i>{{ __('loan-applications.labels.no_equipment_requested') }}</p>
                        @endif
                    </section>

                    {{-- Approval History --}}
                    @if($loanApplication->approvals->isNotEmpty())
                    <section aria-labelledby="approval-history">
                        <h2 id="approval-history" class="h5 fw-semibold text-body mb-3 mt-4 border-bottom pb-2">{{ __('loan-applications.sections.approval_history') }}</h2>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="small">{{ __('loan-applications.labels.stage') }}</th>
                                        <th class="small">{{ __('loan-applications.labels.officer') }}</th>
                                        <th class="small">{{ __('loan-applications.labels.status') }}</th>
                                        <th class="small">{{ __('loan-applications.labels.action_date') }}</th>
                                        <th class="small">{{ __('loan-applications.labels.comments') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loanApplication->approvals as $approval)
                                        <tr>
                                            <td class="small">{{ $approval->stage_translated }}</td>
                                            <td class="small">{{ $approval->officer->name ?? '-' }}</td>
                                            <td class="small"><span class="badge {{ $approval->status_color_class }}">{{ $approval->status_translated }}</span></td>
                                            <td class="small">{{ optional($approval->approval_timestamp)->translatedFormat('d/m/Y h:i A') }}</td>
                                            <td class="small text-muted">{{ $approval->comments ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </section>
                    @endif

                    {{-- Loan Transaction History --}}
                    @if($loanApplication->loanTransactions->isNotEmpty())
                    <section aria-labelledby="transaction-history">
                        <h2 id="transaction-history" class="h5 fw-semibold text-body mb-3 mt-4 border-bottom pb-2">{{ __('loan-applications.sections.transaction_history') }}</h2>
                        <div class="vstack gap-3">
                            @foreach($loanApplication->loanTransactions as $transaction)
                            <div class="p-3 border rounded">
                                <h6 class="fw-semibold"><i class="bi {{ $transaction->isIssue() ? 'bi-box-arrow-up' : 'bi-box-arrow-down' }} me-2"></i>{{ __('loan-applications.labels.transaction') }} #{{$transaction->id}} ({{$transaction->type_label}})</h6>
                                <dl class="row mb-0 small">
                                    <dt class="col-sm-4">{{ __('loan-applications.labels.transaction_date') }}</dt>
                                    <dd class="col-sm-8">{{ $transaction->transaction_date->translatedFormat('d M Y, h:i A') }}</dd>

                                    @if($transaction->isIssue())
                                        <dt class="col-sm-4">{{ __('loan-applications.labels.issuing_officer') }}</dt>
                                        <dd class="col-sm-8">{{ $transaction->issuingOfficer->name ?? '-' }}</dd>
                                        <dt class="col-sm-4">{{ __('loan-applications.labels.receiving_officer') }}</dt>
                                        <dd class="col-sm-8">{{ $transaction->receivingOfficer->name ?? '-' }}</dd>
                                    @else
                                        <dt class="col-sm-4">{{ __('loan-applications.labels.returning_officer') }}</dt>
                                        <dd class="col-sm-8">{{ $transaction->returningOfficer->name ?? '-' }}</dd>
                                        <dt class="col-sm-4">{{ __('loan-applications.labels.return_receiving_officer') }}</dt>
                                        <dd class="col-sm-8">{{ $transaction->returnAcceptingOfficer->name ?? '-' }}</dd>
                                    @endif
                                </dl>
                            </div>
                            @endforeach
                        </div>
                    </section>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="mt-4 pt-4 border-top d-flex flex-wrap justify-content-center gap-2">
                        @can('update', $loanApplication)
                            <a href="{{ route('loan-applications.edit', $loanApplication) }}" class="btn btn-secondary"><i class="bi bi-pencil-fill me-1"></i> {{ __('loan-applications.update_draft') }}</a>
                        @endcan
                        @can('submit', $loanApplication)
                            <form action="{{ route('loan-applications.submit', $loanApplication) }}" method="POST" onsubmit="return confirm('{{ __('loan-applications.submit_confirm_message') }}')">
                                @csrf
                                <button type="submit" class="btn btn-primary"><i class="bi bi-send-check-fill me-1"></i> {{ __('loan-applications.submit_application') }}</button>
                            </form>
                        @endcan

                        @if ($loanApplication->latest_issue_transaction)
                            @can('processReturn', $loanApplication->latest_issue_transaction)
                                <a href="{{ route('loan-transactions.return.form', $loanApplication->latest_issue_transaction) }}" class="btn btn-success"><i class="bi bi-arrow-down-left-circle-fill me-1"></i> {{ __('loan-applications.process_return') }}</a>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
