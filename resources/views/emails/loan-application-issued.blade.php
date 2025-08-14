{{-- resources/views/emails/loan-application-issued.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Peralatan Pinjaman ICT Telah Dikeluarkan') }}</title>
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

        .alert-info {
            color: #004085;
            background-color: #cfe2ff;
            border-color: #b6d4fe;
        }

        .alert-info p {
            margin-bottom: 0.5rem;
        }

        .alert-info strong {
            display: inline-block;
            min-width: 150px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        @include('emails._partials.email-header', [
            'logoUrl' => secure_asset('assets/img/logo/motac_logo_email.png'),
        ])

        <h1>{{ __('Notifikasi Pengeluaran Peralatan Pinjaman ICT') }}</h1>
        <p>{{ __('Salam sejahtera') }} {{ $loanApplication->user->name ?? __('Pemohon') }},</p>
        <p>{{ __('Merujuk kepada permohonan Pinjaman Peralatan ICT anda dengan nombor rujukan') }}
            <strong>#{{ $loanApplication->id }}</strong>.</p>
        <p>{{ __('Sukacita dimaklumkan bahawa peralatan yang diluluskan untuk permohonan anda telah') }}
            <strong>{{ __('Dikeluarkan') }}</strong>.</p>

        <div class="alert-details alert-info">
            <p style="margin-top:0;"><strong>{{ __('Butiran Peralatan yang Dikeluarkan') }}:</strong></p>
            {{-- Use the $issueTransactions variable passed from the Mailable --}}
            @if (isset($issueTransactions) && $issueTransactions->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Peralatan (Tag ID)') }}</th>
                            <th>{{ __('Aksesori Dikeluarkan') }}</th>
                            <th>{{ __('Tarikh Dikeluarkan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($issueTransactions as $transaction)
                            @foreach ($transaction->loanTransactionItems as $loanItem)
                                {{-- Iterate through items in each transaction --}}
                                <tr>
                                    <td>
                                        {{ $loanItem->equipment->asset_type_name ?? 'N/A' }} -
                                        {{ $loanItem->equipment->brand ?? 'N/A' }}
                                        {{ $loanItem->equipment->model ?? 'N/A' }}
                                        (Tag: {{ $loanItem->equipment->tag_id ?? 'N/A' }})
                                    </td>
                                    <td>
                                        @php
                                            $accessories =
                                                json_decode($transaction->accessories_checklist_on_issue, true) ?? [];
                                            echo !empty($accessories)
                                                ? implode(', ', array_keys(array_filter($accessories)))
                                                : '-';
                                        @endphp
                                    </td>
                                    <td>{{ $transaction->transaction_date?->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) ?? 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>{{ __('Tiada butiran peralatan dikeluarkan direkodkan untuk permohonan ini.') }}</p>
            @endif
            <p style="margin-top: 1rem; margin-bottom:0;">
                {{ __('Sila pastikan peralatan dipulangkan pada atau sebelum tarikh jangkaan pulangan:') }}
                <strong>{{ $loanApplication->loan_end_date?->translatedFormat(config('app.date_format_my', 'd/m/Y')) ?? 'N/A' }}</strong>.
            </p>
        </div>

        <p>{{ __('Jika anda mempunyai sebarang pertanyaan mengenai peralatan yang dikeluarkan, sila hubungi Bahagian Pengurusan Maklumat (BPM).') }}
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
