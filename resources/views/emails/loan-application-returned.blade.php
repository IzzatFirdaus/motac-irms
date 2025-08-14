{{-- resources/views/emails/loan-application-returned.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Peralatan Pinjaman ICT Telah Dipulangkan') }}</title>
    <style>
        body {
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            line-height: 1.6;
            color: #212529;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 25px 35px;
            border-radius: 0.375rem;
            border: 1px solid #dee2e6;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        h1 {
            color: #1A202C;
            margin-top: 0;
            margin-bottom: 0.75rem;
            font-size: 22px;
        }

        p {
            margin-bottom: 1rem;
        }

        .footer {
            margin-top: 25px;
            font-size: 0.875em;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 15px;
            font-size: 0.9em;
        }

        th,
        td {
            padding: 0.5rem 0.5rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background-color: #e9ecef;
            font-weight: bold;
            color: #495057;
        }

        .alert-details {
            margin-top: 20px;
            padding: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        /* MOTAC Secondary/Neutral Alert Colors */
        .alert-secondary {
            color: #41464b;
            background-color: #e2e3e5;
            border-color: #d3d6d8;
        }

        .alert-secondary p {
            margin-bottom: 0.5rem;
        }

        .alert-secondary strong {
            display: inline-block;
            min-width: 150px;
        }

        .text-danger {
            color: #DC3545 !important;
            font-weight: bold;
        }

        /* MOTAC Critical Red */
    </style>
</head>

<body>
    <div class="email-container">
        @include('emails._partials.email-header', [
            'logoUrl' => secure_asset('assets/img/logo/motac_logo_email.png'),
        ])

        <h1>{{ __('Notifikasi Pulangan Peralatan Pinjaman ICT') }}</h1>
        <p>{{ __('Salam sejahtera') }} {{ $loanApplication->user->name ?? __('Pemohon') }},</p>
        <p>{{ __('Merujuk kepada permohonan Pinjaman Peralatan ICT anda dengan nombor rujukan') }}
            <strong>#{{ $loanApplication->id }}</strong>.</p>
        <p>{{ __('Sukacita dimaklumkan bahawa peralatan berikut telah berjaya') }}
            <strong>{{ __('Dipulangkan') }}</strong> {{ __('dan direkodkan dalam sistem.') }}</p>

        <div class="alert-details alert-secondary">
            <p style="margin-top:0;"><strong>{{ __('Butiran Pulangan Peralatan') }}:</strong></p>
            @if ($loanTransaction && $loanTransaction->loanTransactionItems->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Peralatan (Tag ID)') }}</th>
                            <th>{{ __('Status Semasa Pulangan') }}</th>
                            <th>{{ __('Tarikh Dipulangkan') }}</th>
                            <th>{{ __('Diterima Oleh') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loanTransaction->loanTransactionItems as $item)
                            <tr>
                                <td>
                                    {{ $item->equipment->asset_type_name ?? 'N/A' }} -
                                    {{ $item->equipment->brand ?? 'N/A' }}
                                    {{ $item->equipment->model ?? 'N/A' }}
                                    (Tag: {{ $item->equipment->tag_id ?? 'N/A' }})
                                </td>
                                {{-- Assuming status_on_return is from LoanTransactionItem and loanTransaction->status is fallback --}}
                                <td>{{ $item->status_on_return_translated ?? ($loanTransaction->status_translated ?? __('N/A')) }}
                                </td>
                                <td>{{ $loanTransaction->transaction_date?->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) ?? 'N/A' }}
                                </td>
                                <td>{{ $loanTransaction->returnAcceptingOfficer->name ?? ($loanTransaction->issuingOfficer->name ?? 'N/A') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p style="margin-top: 1rem;"><strong>{{ __('Aksesori yang Dipulangkan') }}:</strong>
                    @php
                        $accessoriesReturned =
                            json_decode($loanTransaction->accessories_checklist_on_return, true) ?? [];
                        echo !empty($accessoriesReturned)
                            ? implode(', ', array_keys(array_filter($accessoriesReturned)))
                            : '-';
                    @endphp
                </p>
                <p><strong>{{ __('Catatan Pulangan') }}:</strong> {{ $loanTransaction->return_notes ?? '-' }}</p>

                @if (in_array($loanTransaction->status, [
                        \App\Models\LoanTransaction::STATUS_RETURNED_DAMAGED,
                        \App\Models\LoanTransaction::STATUS_ITEMS_REPORTED_LOST,
                    ]))
                    <p style="margin-top: 1rem; margin-bottom:0;" class="text-danger">
                        @if ($loanTransaction->status === \App\Models\LoanTransaction::STATUS_RETURNED_DAMAGED)
                            <strong>{{ __('Amaran: Peralatan ini direkodkan sebagai ROSAK semasa pulangan.') }}</strong>
                        @elseif($loanTransaction->status === \App\Models\LoanTransaction::STATUS_ITEMS_REPORTED_LOST)
                            <strong>{{ __('Amaran: Peralatan ini direkodkan sebagai HILANG semasa pulangan.') }}</strong>
                        @endif
                        {{ __('Tindakan lanjut mungkin diperlukan.') }}
                    </p>
                @endif
            @else
                <p>{{ __('Tiada butiran item pulangan ditemui untuk transaksi ini.') }}</p>
            @endif
        </div>

        <p>{{ __('Jika anda mempunyai sebarang pertanyaan mengenai pulangan peralatan ini, sila hubungi Bahagian Pengurusan Maklumat (BPM).') }}
        </p>
        <p>{{ __('Sekian, terima kasih.') }}</p>
        <p>{{ __('Yang menjalankan amanah,') }}<br>
            {{ __('Pasukan Pentadbir Sistem') }}<br>
            {{ __('Bahagian Pengurusan Maklumat (BPM)') }}<br>
            {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}</p>

        <div class="footer">
            <p>{{ __('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.') }}</p>
            <p>&copy; {{ date('Y') }} {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}.
                {{ __('Hak Cipta Terpelihara.') }}</p>
        </div>
    </div>
</body>

</html>
