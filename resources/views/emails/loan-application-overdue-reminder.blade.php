{{-- resources/views/emails/loan-application-overdue-reminder.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan: Pinjaman Peralatan ICT Lewat Dipulangkan</title>
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
        .alert-warning { color: #664d03; background-color: #fff3cd; border-color: #ffecb5; }
        .text-danger { color: #dc3545 !important; font-weight: bold; }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Peringatan Pinjaman Peralatan ICT</h1>
        <p>Salam sejahtera {{ $loanApplication->user->name ?? 'Pemohon' }},</p>
        <p>Merujuk kepada permohonan Pinjaman Peralatan ICT anda dengan nombor rujukan <strong>#{{ $loanApplication->id }}</strong>.</p>
        <p>Rekod kami menunjukkan bahawa peralatan yang dipinjam di bawah permohonan ini telah <strong class="text-danger">Lewat Dipulangkan</strong>.</p>

        <div class="alert-details alert-warning">
            <p style="margin-top:0;"><strong>Butiran Pinjaman:</strong></p>
            <p>Tujuan Permohonan: {{ $loanApplication->purpose ?? 'N/A' }}</p>
            <p>Tarikh Dijangka Pulang: <span class="text-danger">{{ $loanApplication->loan_end_date?->format(config('app.date_format_my','d/m/Y')) ?? 'N/A' }}</span></p>

            @php
                // Filter for items that are part of an 'issue' transaction and not yet part of a 'return' transaction for this loan application
                $issuedItems = collect();
                foreach ($loanApplication->transactions->where('type', \App\Models\LoanTransaction::TYPE_ISSUE) as $issueTransaction) {
                    foreach ($issueTransaction->loanTransactionItems as $item) {
                         // Check if this item has been returned in a subsequent transaction
                        $isReturned = $loanApplication->transactions
                            ->where('type', \App\Models\LoanTransaction::TYPE_RETURN)
                            ->where('created_at', '>', $issueTransaction->created_at) // only subsequent returns
                            ->whereHas('loanTransactionItems', function ($query) use ($item) {
                                $query->where('equipment_id', $item->equipment_id);
                            })->exists();

                        if (!$isReturned) {
                            $issuedItems->push($item);
                        }
                    }
                }
            @endphp

            @if ($issuedItems->isNotEmpty())
                <p style="margin-top: 1rem;">Peralatan yang Belum Dipulangkan:</p>
                <table>
                    <thead>
                        <tr>
                            <th>Peralatan (Tag ID)</th>
                            <th>Tarikh Dikeluarkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($issuedItems as $item)
                            <tr>
                                <td>
                                    {{ $item->equipment->asset_type_name ?? 'N/A' }} -
                                    {{ $item->equipment->brand ?? 'N/A' }}
                                    {{ $item->equipment->model ?? 'N/A' }}
                                    (Tag: {{ $item->equipment->tag_id ?? 'N/A' }})
                                </td>
                                <td>{{ $item->transaction->transaction_date?->format(config('app.datetime_format_my','d/m/Y H:i A')) ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Tiada butiran peralatan lewat dipulangkan direkodkan untuk permohonan ini atau semua telah dipulangkan.</p>
            @endif
            <p style="margin-top: 1rem; margin-bottom:0;">Sila pulangkan peralatan tersebut ke bahagian BPM ICT dengan kadar segera.</p>
        </div>

        <p>Jika anda telah memulangkan peralatan tersebut baru-baru ini, sila abaikan e-mel ini atau hubungi kami untuk pengesahan.</p>
        <p>Jika anda mempunyai sebarang pertanyaan, sila hubungi bahagian BPM ICT.</p>
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
