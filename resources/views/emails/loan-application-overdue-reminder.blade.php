{{-- resources/views/emails/loan-application-overdue-reminder.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Peringatan: Pinjaman Peralatan ICT Lewat Dipulangkan') }}</title>
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

        .alert-warning {
            color: #664d03;
            background-color: #fff3cd;
            border-color: #ffda6a;
        }

        .alert-warning p {
            margin-bottom: 0.5rem;
        }

        .alert-warning strong {
            display: inline-block;
            min-width: 150px;
        }

        .text-danger {
            color: #DC3545 !important;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="email-container">
        @include('emails._partials.email-header', [
            'logoUrl' => secure_asset('assets/img/logo/motac_logo_email.png'),
        ])

        <h1>{{ __('Peringatan Pinjaman Peralatan ICT Lewat Tempoh') }}</h1>
        <p>{{ __('Salam sejahtera') }} {{ $loanApplication->user->name ?? __('Pemohon') }},</p>
        <p>{{ __('Merujuk kepada permohonan Pinjaman Peralatan ICT anda dengan nombor rujukan') }}
            <strong>#{{ $loanApplication->id }}</strong>.</p>
        <p>{{ __('Rekod kami menunjukkan bahawa peralatan yang dipinjam di bawah permohonan ini telah') }} <strong
                class="text-danger">{{ __('LEWAT DIPULANGKAN') }}</strong>.</p>

        <div class="alert-details alert-warning">
            <p style="margin-top:0;"><strong>{{ __('Butiran Pinjaman') }}:</strong></p>
            <p><strong>{{ __('Tujuan Permohonan') }}:</strong> {{ $loanApplication->purpose ?? __('N/A') }}</p>
            <p><strong>{{ __('Tarikh Dijangka Pulang') }}:</strong> <span
                    class="text-danger">{{ $loanApplication->loan_end_date?->translatedFormat(config('app.date_format_my', 'd/m/Y')) ?? 'N/A' }}</span>
            </p>

            {{-- Use the $overdueItems variable passed from the Mailable --}}
            @if (isset($overdueItems) && $overdueItems->isNotEmpty())
                <p style="margin-top: 1rem;"><strong>{{ __('Peralatan yang Masih Belum Dipulangkan') }}:</strong></p>
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('Peralatan (Tag ID)') }}</th>
                            <th>{{ __('Tarikh Dikeluarkan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overdueItems as $item)
                            <tr>
                                <td>
                                    {{ $item->equipment->asset_type_name ?? 'N/A' }} -
                                    {{ $item->equipment->brand ?? 'N/A' }}
                                    {{ $item->equipment->model ?? 'N/A' }}
                                    (Tag: {{ $item->equipment->tag_id ?? 'N/A' }})
                                </td>
                                {{-- The $item is from an issue transaction, so $item->transaction is that issue transaction --}}
                                <td>{{ $item->transaction->transaction_date?->translatedFormat(config('app.datetime_format_my', 'd/m/Y H:i A')) ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>{{ __('Tiada butiran spesifik peralatan yang lewat tempoh dikenal pasti berdasarkan rekod terkini atau kesemua item telah direkodkan sebagai dipulangkan.') }}
                </p>
            @endif
            <p style="margin-top: 1rem; margin-bottom:0;">
                <strong>{{ __('Sila pulangkan peralatan tersebut ke Bahagian Pengurusan Maklumat (BPM) dengan kadar segera.') }}</strong>
            </p>
        </div>

        <p>{{ __('Jika anda telah memulangkan peralatan tersebut baru-baru ini, sila abaikan e-mel ini atau hubungi kami untuk pengesahan.') }}
        </p>
        <p>{{ __('Kegagalan memulangkan peralatan mengikut tempoh yang ditetapkan boleh menyebabkan tindakan selanjutnya diambil.') }}
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
