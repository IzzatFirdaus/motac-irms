{{-- resources/views/admin/equipment/equipment-show.blade.php --}}
@extends('layouts.app')

@section('title', __('transaction.show_title') . ' #' . $loanTransaction->id)

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
                    <h1 class="h2 fw-bold text-dark mb-0">
                        {{ __('transaction.show_title') }} #{{ $loanTransaction->id }}
                    </h1>
                    <a href="{{ route('resource-management.bpm.loan-transactions.index') }}"
                        class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-list-ul me-1"></i> {{ __('transaction.back_to_list') }}
                    </a>
                </div>

                @include('_partials._alerts.alert-general')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light py-3">
                        <h2 class="h5 card-title mb-0 fw-semibold">{{ __('transaction.basic_info') }}</h2>
                    </div>
                    <div class="card-body p-4">
                        <dl class="row g-3 small">
                            <dt class="col-sm-4 text-muted">{{ __('transaction.related_loan_app_id') }}</dt>
                            <dd class="col-sm-8"><a
                                    href="{{ route('loan-applications.show', $loanTransaction->loan_application_id) }}"
                                    class="text-decoration-none fw-medium">#{{ $loanTransaction->loan_application_id }}</a>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('transaction.transaction_type') }}</dt>
                            <dd class="col-sm-8">
                                {{-- Using the new accessor for a high-contrast badge --}}
                                <span
                                    class="badge rounded-pill {{ $loanTransaction->type_color_class }}">{{ $loanTransaction->type_label }}</span>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('transaction.transaction_status') }}</dt>
                            <dd class="col-sm-8"><span
                                    class="badge rounded-pill {{ $loanTransaction->status_color_class }}">{{ $loanTransaction->status_label }}</span>
                            </dd>

                            <dt class="col-sm-4 text-muted">{{ __('transaction.transaction_datetime') }}</dt>
                            <dd class="col-sm-8">
                                {{ $loanTransaction->transaction_date?->translatedFormat('d M Y, h:i A') }}</dd>
                        </dl>

                        <h3 class="h6 fw-semibold mt-4 mb-2 pt-2 border-top">{{ __('transaction.involved_items') }}</h3>
                        <ul class="list-group list-group-flush">
                            @forelse ($loanTransaction->loanTransactionItems as $item)
                                <li class="list-group-item px-0 d-flex justify-content-between align-items-start py-2">
                                    <div>
                                        <a href="{{ route('resource-management.equipment-admin.show', $item->equipment_id) }}"
                                            class="text-decoration-none fw-medium">{{ $item->equipment->name ?? __('Item Tidak Dikenali') }}</a>
                                        <small class="d-block text-muted">Tag:
                                            {{ $item->equipment->tag_id ?? 'N/A' }}</small>
                                    </div>
                                    <span class="text-muted small">{{ __('transaction.quantity') }}:
                                        {{ $item->quantity_transacted }}</span>
                                </li>
                            @empty
                                <li class="list-group-item px-0 text-muted small">
                                    {{ __('Tiada item dalam transaksi ini.') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                @if ($loanTransaction->isIssue())
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light py-3">
                            <h2 class="h5 card-title mb-0 fw-semibold">{{ __('transaction.issue_details') }}</h2>
                        </div>
                        <div class="card-body p-4 small">
                            <dl class="row g-3">
                                <dt class="col-sm-4 text-muted">{{ __('transaction.issuing_officer') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $loanTransaction->issuingOfficer?->name ?? __('common.not_available') }}</dd>
                                <dt class="col-sm-4 text-muted">{{ __('transaction.receiver') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $loanTransaction->receivingOfficer?->name ?? __('common.not_available') }}</dd>
                                <dt class="col-sm-4 text-muted">{{ __('transaction.actual_issue_datetime') }}</dt>
                                <dd class="col-sm-8">
                                    {{ $loanTransaction->issue_timestamp?->translatedFormat('d M Y, h:i A') ?? __('common.not_available') }}
                                </dd>
                                <dt class="col-sm-4 text-muted">{{ __('transaction.accessories_issued') }}:</dt>
                                <dd class="col-sm-8">
                                    {{ $loanTransaction->accessories_checklist_on_issue ? implode(', ', $loanTransaction->accessories_checklist_on_issue) : '-' }}
                                </dd>
                                <dt class="col-sm-4 text-muted">{{ __('transaction.issue_notes') }}</dt>
                                <dd class="col-sm-8" style="white-space: pre-wrap;">
                                    {{ $loanTransaction->issue_notes ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                @endif

                <div class="text-center mt-4">
                    <a href="{{ route('loan-applications.show', $loanTransaction->loan_application_id) }}"
                        class="btn btn-outline-secondary d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left-circle me-1"></i> {{ __('transaction.back_to_application') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
