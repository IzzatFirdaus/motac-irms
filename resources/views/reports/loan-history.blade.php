@extends('layouts.app')

@section('title', __('reports.loan_history.title'))

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4 motac-card">
        <div class="card-header bg-light py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-clock-history me-2"></i>
                    {{ __('reports.loan_history.page_header') }}
                </h3>
                <div class="mt-2 mt-sm-0 flex-shrink-0">
                    <a href="{{ route('reports.index') }}"
                       class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                        <i class="bi bi-arrow-left me-1"></i>
                        {{ __('reports.back_to_list') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <div class="card-body border-bottom">
            <form action="{{ route('reports.loan-history') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="user_id" class="form-label form-label-sm">{{ __('reports.filters.user') }}</label>
                    <select name="user_id" id="user_id" class="form-select form-select-sm">
                        <option value="">{{ __('reports.filters.all_users') }}</option>
                        @foreach ($usersFilter as $id => $name)
                            <option value="{{ $id }}" @selected(request('user_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label form-label-sm">{{ __('reports.filters.transaction_type') }}</label>
                    <select name="type" id="type" class="form-select form-select-sm">
                        <option value="">{{ __('reports.filters.all_types') }}</option>
                        @foreach ($transactionTypes as $key => $label)
                            <option value="{{ $key }}" @selected(request('type') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label form-label-sm">{{ __('reports.filters.date_from') }}</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label form-label-sm">{{ __('reports.filters.date_to') }}</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100">{{ __('reports.filters.filter_button') }}</button>
                </div>
            </form>
        </div>

        {{-- Results Table --}}
        <div class="card-body">
            @include('_partials._alerts.alert-general')

            <div class="table-responsive">
                @if ($loanTransactions->count())
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3 py-2 small text-uppercase text-muted">{{ __('reports.loan_history.table.transaction_id') }}</th>
                                <th class="px-3 py-2 small text-uppercase text-muted">{{ __('reports.loan_history.table.application_id') }}</th>
                                <th class="px-3 py-2 small text-uppercase text-muted">{{ __('reports.loan_history.table.equipment') }}</th>
                                <th class="px-3 py-2 small text-uppercase text-muted">{{ __('reports.loan_history.table.user') }}</th>
                                <th class="px-3 py-2 small text-uppercase text-muted">{{ __('reports.loan_history.table.type') }}</th>
                                <th class="px-3 py-2 small text-uppercase text-muted">{{ __('reports.loan_history.table.date') }}</th>
                                <th class="px-3 py-2 small text-uppercase text-muted">{{ __('reports.loan_history.table.officer') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loanTransactions as $transaction)
                                <tr>
                                    <td class="px-3 py-2 small fw-medium text-dark">#{{ $transaction->id }}</td>
                                    <td class="px-3 py-2 small">
                                        @if ($transaction->loanApplication)
                                            <a href="{{ route('loan-applications.show', $transaction->loan_application_id) }}">#{{ $transaction->loan_application_id }}</a>
                                        @else
                                            #{{ $transaction->loan_application_id }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ $transaction->items->pluck('equipment.tag_id')->implode(', ') ?: '-' }}
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $transaction->loanApplication->user->name ?? __('common.not_available') }}</td>
                                    <td class="px-3 py-2 small">
                                        <span class="badge rounded-pill {{ $transaction->type === \App\Models\LoanTransaction::TYPE_ISSUE ? 'bg-info-subtle text-info-emphasis' : 'bg-primary-subtle text-primary-emphasis' }}">
                                            {{ $transaction->type_label }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 small text-muted">{{ $transaction->transaction_date?->translatedFormat('d M Y, g:i A') }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ $transaction->officer_name ?? __('common.not_available') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($loanTransactions->hasPages())
                        <div class="mt-3 pt-3 border-top d-flex justify-content-center">
                            {{ $loanTransactions->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-info text-center" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ __('reports.loan_history.no_results') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
