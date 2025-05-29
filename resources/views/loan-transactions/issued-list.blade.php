{{-- resources/views/loan-transactions/issued-list.blade.php --}}
@extends('layouts.app') {{-- Or your main layout file --}}

@section('title', 'Senarai Pinjaman Telah Dikeluarkan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@yield('title')</h3>
                    {{-- Add any filter or search forms here if needed --}}
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($issuedTransactions->isEmpty())
                        <div class="alert alert-info">
                            Tiada rekod pinjaman yang telah dikeluarkan ditemui.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>ID Permohonan</th>
                                        <th>Peminjam</th>
                                        <th>Pegawai Bertanggungjawab</th>
                                        <th>Tarikh Keluar</th>
                                        <th>Tarikh Jangka Pulang</th>
                                        <th>Status Transaksi</th>
                                        <th>Bil. Item</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($issuedTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>
                                                <a href="{{ route('loan-applications.show', $transaction->loanApplication) }}">
                                                    {{ $transaction->loan_application_id }}
                                                </a>
                                            </td>
                                            <td>{{ $transaction->loanApplication->user->full_name ?? 'N/A' }}</td>
                                            <td>{{ $transaction->loanApplication->responsibleOfficer->full_name ?? ($transaction->loanApplication->user->full_name ?? 'N/A') }}</td>
                                            <td>{{ $transaction->issue_timestamp ? $transaction->issue_timestamp->format('d/m/Y H:i A') : 'N/A' }}</td>
                                            <td>{{ $transaction->due_date ? \Carbon\Carbon::parse($transaction->due_date)->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $transaction->status === \App\Models\LoanTransaction::STATUS_ISSUED ? 'success' : 'secondary' }}">
                                                    {{ Str::title(str_replace('_', ' ', $transaction->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $transaction->loanTransactionItems->count() }}</td>
                                            <td>
                                                <a href="{{ route('loan-transactions.show', $transaction) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                                @can('createReturn', $transaction) {{-- Assuming LoanTransactionPolicy has createReturn --}}
                                                    @if(!$transaction->isFullyClosedOrReturned())
                                                        <a href="{{ route('loan-transactions.return.form', $transaction) }}" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-undo"></i> Proses Pulangan
                                                        </a>
                                                    @endif
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $issuedTransactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Add any page-specific JavaScript here --}}
@endpush
