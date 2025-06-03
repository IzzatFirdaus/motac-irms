{{-- resources/views/reports/loan-history.blade.php --}}
<x-app-layout>
    @section('title', __('Laporan Sejarah Pinjaman ICT'))

    <div class="container-fluid py-4">
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-header bg-light py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <h3 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-clock-history me-2"></i>
                        {{ __('Laporan Sejarah Transaksi Pinjaman ICT') }}
                    </h3>
                    @if (Route::has('reports.index')) {{-- Using reports.index for consistency --}}
                        <div class="mt-2 mt-sm-0 flex-shrink-0">
                            <a href="{{ route('reports.index') }}"
                               class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                                <i class="bi bi-arrow-left me-1"></i>
                                {{ __('Kembali ke Senarai Laporan') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-body">
                @include('_partials._alerts.alert-general')

                <div class="table-responsive">
                    @if ($loanTransactions->count())
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('ID Transaksi') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('ID Permohonan') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Peralatan') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Pengguna') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Jenis Transaksi') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Tarikh Transaksi') }}</th>
                                    <th scope="col" class="text-uppercase small text-muted fw-medium px-3 py-2">{{ __('Status Transaksi') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loanTransactions as $transaction)
                                    <tr>
                                        <td class="px-3 py-2 small fw-medium text-dark">#{{ $transaction->id }}</td>
                                        <td class="px-3 py-2 small">
                                            @if($transaction->loanApplication)
                                            <a href="{{ route('loan-applications.show', $transaction->loanApplication->id) }} ">
                                                #{{ $transaction->loan_application_id }}
                                            </a>
                                            @else
                                            #{{ $transaction->loan_application_id }} ({{ __('Data Permohonan Tidak Dijumpai') }})
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 small text-muted">
                                            {{-- Assuming $transaction->item_name accessor exists as defined previously --}}
                                            {{ $transaction->item_name ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 small text-muted">{{ $transaction->loanApplication->user->name ?? ($transaction->loanApplication->user->full_name ?? 'N/A') }}</td>
                                        <td class="px-3 py-2 small text-muted">
                                            <span class="badge rounded-pill {{ $transaction->type === \App\Models\LoanTransaction::TYPE_ISSUE ? 'bg-info-subtle text-info-emphasis' : 'bg-primary-subtle text-primary-emphasis' }} fw-normal">
                                                {{ __(optional($transaction)->type_label ?? Str::title(str_replace('_', ' ', $transaction->type))) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 small text-muted">{{ $transaction->transaction_date?->translatedFormat('d M Y, H:i A') ?? ($transaction->created_at?->translatedFormat('d M Y, H:i A')) }}</td>
                                        <td class="px-3 py-2 small">
                                            {{-- CORRECTED: Added 'loan_transaction' as the second argument --}}
                                            {{-- Ensure 'loan_transaction' type is defined in Helpers.php --}}
                                            <span class="badge rounded-pill {{ App\Helpers\Helpers::getStatusColorClass($transaction->status ?? 'default', 'loan_transaction') }} fw-normal">
                                                {{ $transaction->status_label ?? __(Str::title(str_replace('_', ' ', $transaction->status))) }}
                                            </span>
                                        </td>
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
                        <div class="alert alert-info d-flex align-items-center text-center" role="alert">
                           <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                {{ __('Tiada sejarah transaksi pinjaman ditemui.') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
