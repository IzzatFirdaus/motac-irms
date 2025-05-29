{{-- resources/views/emails/loan-application-issued.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peralatan Pinjaman ICT Telah Dikeluarkan</title>
    <style>
        body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; line-height: 1.6; color: #212529; background-color: #f8f9fa; margin: 0; padding: 20px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 25px 35px; border-radius: 0.375rem; border: 1px solid #dee2e6; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        h1 { color: #1A202C; margin-top: 0; margin-bottom: 0.75rem; font-size: 24px; }
        p { margin-bottom: 1rem; }
        .footer { margin-top: 25px; font-size: 0.875em; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 15px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px; }
        th, td { padding: 0.5rem 0.5rem; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background-color: #f8f9fa; font-weight: bold; color: #495057; }
        .alert-details { margin-top: 20px; padding: 1rem; border: 1px solid transparent; border-radius: 0.375rem; margin-bottom: 1rem; }
        .alert-info { color: #055160; background-color: #cff4fc; border-color: #9eeaf9; }
        /* Optional: Add button styles if you plan to include a button later */
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Notifikasi Pengeluaran Peralatan Pinjaman ICT</h1>
        <p>Salam sejahtera {{ $loanApplication->user->name ?? 'Pemohon' }},</p>
        <p>Merujuk kepada permohonan Pinjaman Peralatan ICT anda dengan nombor rujukan <strong>#{{ $loanApplication->id }}</strong>.</p>
        <p>Sukacita dimaklumkan bahawa peralatan yang diluluskan untuk permohonan anda telah <strong>Dikeluarkan</strong>.</p>

        <div class="alert-details alert-info">
            <p style="margin-top:0;"><strong>Butiran Peralatan yang Dikeluarkan:</strong></p>
            @if ($loanApplication->transactions->where('type', \App\Models\LoanTransaction::TYPE_ISSUE)->isNotEmpty())
                <table>
                    <thead>
                        <tr>
                            <th>Peralatan (Tag ID)</th>
                            <th>Aksesori Dikeluarkan</th>
                            <th>Tarikh Dikeluarkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loanApplication->transactions->where('type', \App\Models\LoanTransaction::TYPE_ISSUE) as $transaction)
                            <tr>
                                <td>
                                    {{ $transaction->loanTransactionItems->first()->equipment->asset_type_name ?? 'N/A' }} -
                                    {{ $transaction->loanTransactionItems->first()->equipment->brand ?? 'N/A' }}
                                    {{ $transaction->loanTransactionItems->first()->equipment->model ?? 'N/A' }}
                                    (Tag: {{ $transaction->loanTransactionItems->first()->equipment->tag_id ?? 'N/A' }})
                                </td>
                                <td>
                                    @php
                                        $accessories = json_decode($transaction->accessories_checklist_on_issue, true) ?? [];
                                        echo !empty($accessories) ? implode(', ', array_keys(array_filter($accessories))) : '-';
                                    @endphp
                                </td>
                                <td>{{ $transaction->transaction_date?->format(config('app.datetime_format_my','d/m/Y H:i A')) ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Tiada butiran peralatan dikeluarkan direkodkan untuk permohonan ini.</p>
            @endif
            <p style="margin-top: 1rem; margin-bottom:0;">Sila pastikan peralatan dipulangkan pada atau sebelum tarikh jangkaan pulangan: <strong>{{ $loanApplication->loan_end_date?->format(config('app.date_format_my','d/m/Y')) ?? 'N/A' }}</strong>.</p>
        </div>

        <p>Jika anda mempunyai sebarang pertanyaan mengenai peralatan yang dikeluarkan, sila hubungi bahagian BPM ICT.</p>
        <p>Terima kasih atas kerjasama anda.</p>
        <p>Yang benar,</p>
        <p>Pasukan BPM ICT MOTAC</p>
        <div class="footer">
            <p>Ini adalah e-mel automatik, sila jangan balas.</p>
            <p>&copy; {{ date('Y') }} MOTAC. Hak Cipta Terpelihara.</p>
        </div>
    </div>
</body>
</html>
