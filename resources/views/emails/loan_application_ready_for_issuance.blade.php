<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@lang('Tindakan Diperlukan: Permohonan Pinjaman Sedia Untuk Pengeluaran')</title>
    <style>
        /* Basic responsive styles */
        @media screen and (max-width: 600px) {
            .container-table {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #e9ecef;">

    <table class="container-table" align="center" width="600" cellpadding="0" cellspacing="0" role="presentation" style="width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
        <tr>
            <td style="padding: 20px; background-color: #0d6efd; color: #ffffff; text-align: center;">
                <h1 style="margin: 0; font-size: 24px;">{{ config('app.name', 'MOTAC IRMS') }}</h1>
            </td>
        </tr>

        <tr>
            <td style="padding: 30px 25px;">
                <div style="border: 1px solid #dee2e6; border-radius: 6px;">
                    <div style="background-color: #f8f9fa; padding: 12px 20px; border-bottom: 1px solid #dee2e6;">
                        <h2 style="margin: 0; font-size: 18px; color: #212529;">@lang('Permohonan Sedia Untuk Dikeluarkan')</h2>
                    </div>
                    <div style="padding: 20px;">
                        <p style="margin-top: 0; color: #495057;">@lang('Salam Sejahtera, Staf BPM,')</p>
                        <p style="color: #495057;">
                            @lang('Permohonan pinjaman peralatan ICT berikut oleh :applicantName (ID #:id) telah diluluskan dan sedia untuk proses pengeluaran peralatan.', ['applicantName' => $loanApplication->user->name, 'id' => $loanApplication->id])
                        </p>

                        <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 20px 0;">

                        <h3 style="font-size: 16px; color: #212529; margin-top: 0;">@lang('Butiran Permohonan')</h3>
                        <table width="100%" cellpadding="5" style="color: #495057; font-size: 14px;">
                            <tr>
                                <td width="35%" style="font-weight: bold;">@lang('Tujuan Pinjaman')</td>
                                <td>{{ $loanApplication->purpose }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">@lang('Tempoh Pinjaman')</td>
                                <td>{{ $loanApplication->loan_start_date->format('d M Y') }} - {{ $loanApplication->loan_end_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">@lang('Pegawai Bertanggungjawab')</td>
                                <td>{{ $loanApplication->responsibleOfficer->name ?? $loanApplication->user->name }}</td>
                            </tr>
                        </table>

                        <h4 style="font-size: 15px; color: #212529; margin-top: 20px; margin-bottom: 10px;">@lang('Peralatan Diluluskan:')</h4>
                        <ul style="padding-left: 20px; margin: 0; color: #495057;">
                            @foreach($loanApplication->loanApplicationItems as $item)
                                @php
                                    $quantityToIssue = $item->quantity_approved ?? $item->quantity_requested;
                                @endphp
                                <li style="margin-bottom: 5px;">
                                    <strong>{{ $item->equipment_type }}</strong> - @lang('Kuantiti'): {{ $quantityToIssue }}
                                    @if($item->notes)
                                        <em style="display: block; font-size: 12px; color: #6c757d;">(@lang('Catatan'): {{ $item->notes }})</em>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        <hr style="border: 0; border-top: 1px solid #dee2e6; margin: 25px 0;">

                        <div style="text-align: center;">
                            <a href="{{ route('loan-applications.show', $loanApplication->id) }}" style="display: inline-block; background-color: #0d6efd; color: #ffffff; padding: 12px 25px; font-size: 16px; font-weight: bold; text-decoration: none; border-radius: 5px;" target="_blank">
                                @lang('Lihat & Proses Pengeluaran')
                            </a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px; text-align: center; font-size: 12px; color: #6c757d;">
                <p style="margin: 0;">@lang('Ini adalah notifikasi automatik dari Sistem Pengurusan Sumber MOTAC.')</p>
                <p style="margin: 5px 0 0 0;">&copy; {{ date('Y') }} {{ config('app.name') }}. @lang('Hak Cipta Terpelihara.')</p>
            </td>
        </tr>
    </table>

</body>
</html>
