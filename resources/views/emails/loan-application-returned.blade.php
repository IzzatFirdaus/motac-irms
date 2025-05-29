{{-- resources/views/emails/loan-application-returned.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peralatan Pinjaman ICT Telah Dipulangkan</title>
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
        .alert-secondary { color: #292f35; background-color: #e2e3e5; border-color: #d3d6d8; } /* Using secondary, adjust if needed */
        .text-danger { color: #dc3545 !important; font-weight: bold; } /* For damaged/lost status */
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Notifikasi Pulangan Peralatan Pinjaman ICT</h1>
        <p>Salam sejahtera {{ $loanApplication->user->name ?? 'Pemohon' }},</p>
        <p>Merujuk kepada permohonan Pinjaman Peralatan ICT anda dengan nombor rujukan <strong>#{{ $loanApplication->id }}</strong>.</p>
        <p>Sukacita dimaklumkan bahawa peralatan berikut telah berjaya <strong>Dipulangkan</strong> dan direkodkan.</p>

        <div class="alert-details alert-secondary">
            <p style="margin-top:0;"><strong>Butiran Pulangan Peralatan:</strong></p>
            <table>
                <thead>
                    <tr>
                        <th>Peralatan (Tag ID)</th>
                        <th>Status Pulangan</th>
                        <th>Tarikh Dipulangkan</th>
                        <th>Diterima Oleh</th>
                    </tr>
                </thead>
                <tbody>
                     @foreach($loanTransaction->loanTransactionItems as $item)
                    <tr>
                        <td>
                            {{ $item->equipment->asset_type_name ?? 'N/A' }} -
                            {{ $item->equipment->brand ?? 'N/A' }}
                            {{ $item->equipment->model ?? 'N/A' }}
                            (Tag: {{ $item->equipment->tag_id ?? 'N/A' }})
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $item->status_on_return ?? ($loanTransaction->status ?? 'N/A'))) }}</td>
                        <td>{{ $loanTransaction->transaction_date?->format(config('app.datetime_format_my','d/m/Y H:i A')) ?? 'N/A' }}</td>
                        <td>{{ $loanTransaction->returnAcceptingOfficer->name ?? ($loanTransaction->issuingOfficer->name ?? 'N/A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin-top: 1rem;">Aksesori yang Dipulangkan:
                @php
                    $accessoriesReturned = json_decode($loanTransaction->accessories_checklist_on_return, true) ?? [];
                     echo !empty($accessoriesReturned) ? implode(', ', array_keys(array_filter($accessoriesReturned))) : '-';
                @endphp
            </p>
            <p>Catatan Pulangan: {{ $loanTransaction->return_notes ?? '-' }}</p>

            @if (in_array($loanTransaction->status, ['returned_damaged', 'items_reported_lost']))
                <p style="margin-top: 1rem; margin-bottom:0;" class="text-danger">
                    @if($loanTransaction->status === 'returned_damaged')
                        Peralatan ini direkodkan sebagai ROSAK semasa pulangan.
                    @elseif($loanTransaction->status === 'items_reported_lost')
                        Peralatan ini direkodkan sebagai HILANG semasa pulangan.
                    @endif
                </p>
            @endif
        </div>

        <p>Jika anda mempunyai sebarang pertanyaan mengenai pulangan peralatan ini, sila hubungi bahagian BPM ICT.</p>
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
