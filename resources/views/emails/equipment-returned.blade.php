@extends('layouts.email')

@section('title', 'Peralatan Pinjaman Telah Dipulangkan')

@section('content')
    {{-- This data is now passed directly from the Mailable --}}
    @php
        $loanApplication = $notification->loanApplication;
        $loanTransaction = $notification->returnTransaction;
    @endphp

    <h4 class="mb-3">Salam sejahtera, {{ $loanApplication->user->name ?? 'Pemohon' }},</h4>

    <p>Merujuk kepada permohonan Pinjaman Peralatan ICT anda <strong>#{{ $loanApplication->id }}</strong>, sukacita
        dimaklumkan bahawa peralatan berikut telah berjaya <strong>dipulangkan</strong> dan direkodkan dalam sistem.</p>

    <div class="card mt-4">
        <div class="card-header">
            Butiran Pulangan Peralatan
        </div>
        @if ($loanTransaction && $loanTransaction->loanTransactionItems->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Peralatan (Tag ID)</th>
                            <th>Keadaan Semasa Pulangan</th> {{-- CORRECTED: Changed header from "Status" to "Keadaan" --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loanTransaction->loanTransactionItems as $item)
                            <tr>
                                <td>
                                    {{-- CORRECTED: asset_type_label is the correct accessor on the Equipment model --}}
                                    {{ $item->equipment->asset_type_label ?? 'N/A' }}
                                    ({{ $item->equipment->tag_id ?? 'N/A' }})
                                </td>
                                <td>
                                    {{-- CORRECTED: condition_on_return_translated is the correct accessor on the LoanTransactionItem model --}}
                                    {{ $item->condition_on_return_translated ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top">
                <p class="mb-2"><strong>Diterima Oleh:</strong>
                    {{ $loanTransaction->returnAcceptingOfficer->name ?? 'N/A' }}</p>
                <p class="mb-2"><strong>Tarikh Dipulangkan:</strong>
                    {{ $loanTransaction->transaction_date?->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) ?? 'N/A' }}
                </p>
                @if ($loanTransaction->return_notes)
                    <p class="mb-0"><strong>Catatan Pulangan:</strong> {{ $loanTransaction->return_notes }}</p>
                @endif
            </div>
        @else
            <div class="card-body">
                <p>Tiada butiran item pulangan ditemui untuk transaksi ini.</p>
            </div>
        @endif
    </div>

    @if (isset($actionUrl) && $actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-primary">Lihat Status Permohonan</a>
        </div>
    @endif

    <p class="mt-4">Sekian, terima kasih.</p>
@endsection
