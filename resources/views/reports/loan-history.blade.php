<x-app-layout>
    {{-- The outer container is managed by x-app-layout --}}

    <div class="card shadow-sm mb-4">
        {{-- Card Header --}}
        <div class="card-header">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <div class="mt-2">
                    <h3 class="h5 mb-0">
                        {{ __('Loan History Report') }}
                    </h3>
                </div>
                @if (Route::has('admin.reports.index'))
                    <div class="mt-2 flex-shrink-0">
                        <a href="{{ route('admin.reports.index') }}"
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                            <i class="ti ti-arrow-left me-1"></i>
                            {{ __('Back to Reports') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card Body --}}
        <div class="card-body">
            {{-- General alerts partial - ensure this partial is also Bootstrap styled --}}
            @include('_partials._alerts.alert-general')

            <div class="table-responsive">
                @if ($loanTransactions->count())
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-uppercase small">{{ __('ID') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Equipment') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('User') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Type') }}</th>
                                <th scope="col" class="text-uppercase small">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loanTransactions as $transaction)
                                <tr>
                                    <td class="fw-medium">{{ $transaction->id }}</td>
                                    <td>{{ $transaction->equipment->name ?? __('N/A') }}</td>
                                    <td>{{ $transaction->user->name ?? __('N/A') }}</td>
                                    <td>
                                        {{-- Consider mapping transaction_type to a more readable format if it's an enum/key --}}
                                        {{ $transaction->transaction_type }}
                                    </td>
                                    <td>{{ $transaction->issue_timestamp?->format('Y-m-d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if ($loanTransactions->hasPages())
                        <div class="mt-3 pt-3 border-top">
                            {{ $loanTransactions->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img"
                            aria-label="Warning:">
                            <use xlink:href="#exclamation-triangle-fill" />
                        </svg>
                        <div>
                            {{ __('No loan history found.') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
