@extends('layouts.email')

@section('title', __('Peralatan Pinjaman Telah Dipulangkan'))

@section('content')
    @php
        $loanApplication = $loanApplication ?? null;
        $loanTransaction = $returnTransaction ?? null;
        $actionUrl = $actionUrl ?? route('loan-applications.show', $loanApplication->id ?? 0);
    @endphp

    <h4 class="mb-3">{{ __('Salam sejahtera, :name,', ['name' => $loanApplication->user->name ?? 'Pemohon']) }}</h4>

    <p>{{ __('Merujuk kepada permohonan Pinjaman Peralatan ICT anda #:id, sukacita dimaklumkan bahawa peralatan berikut telah berjaya dipulangkan dan direkodkan dalam sistem.', [
        'id' => $loanApplication->id ?? 'N/A'
    ]) }}</p>

    <div class="card mt-4">
        <div class="card-header">
            {{ __('Butiran Pulangan Peralatan') }}
        </div>
        @if ($loanTransaction && $loanTransaction->loanTransactionItems->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Peralatan (Tag ID)') }}</th>
                            <th>{{ __('Keadaan Semasa Pulangan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loanTransaction->loanTransactionItems as $item)
                            <tr>
                                <td>
                                    {{ $item->equipment->asset_type_label ?? 'N/A' }}
                                    ({{ $item->equipment->tag_id ?? 'N/A' }})
                                </td>
                                <td>
                                    {{ $item->condition_on_return_translated ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top">
                <p class="mb-2"><strong>{{ __('Diterima Oleh') }}:</strong>
                    {{ $loanTransaction->returnAcceptingOfficer->name ?? 'N/A' }}</p>
                <p class="mb-2"><strong>{{ __('Tarikh Dipulangkan') }}:</strong>
                    {{ $loanTransaction->transaction_date?->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) ?? 'N/A' }}
                </p>
                @if ($loanTransaction->return_notes)
                    <p class="mb-0"><strong>{{ __('Catatan Pulangan') }}:</strong> {{ $loanTransaction->return_notes }}</p>
                @endif
            </div>
        @else
            <div class="card-body">
                <p>{{ __('Tiada butiran item pulangan ditemui untuk transaksi ini.') }}</p>
            </div>
        @endif
    </div>

    @if ($actionUrl && $actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-primary">{{ __('Lihat Status Permohonan') }}</a>
        </div>
    @endif

    <p class="mt-4">{{ __('Sekian, terima kasih.') }}</p>
@endsection
