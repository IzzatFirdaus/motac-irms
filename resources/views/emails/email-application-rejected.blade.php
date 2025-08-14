{{-- resources/views/emails/email-application-rejected.blade.php --}}
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Permohonan Akaun E-mel ICT Ditolak') }}</title>
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

        .alert-details {
            margin-top: 20px;
            padding: 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        /* MOTAC Danger Alert Colors */
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
        }

        /* Adjusted for MOTAC Danger #DC3545 derivatives */
        .alert-danger p {
            margin-bottom: 0.5rem;
        }

        .alert-danger strong {
            display: inline-block;
            min-width: 130px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        @include('emails._partials.email-header', [
            'logoUrl' => secure_asset('assets/img/logo/motac_logo_email.png'),
        ])

        <h1>{{ __('Notifikasi Permohonan Akaun E-mel ICT') }}</h1>
        <p>{{ __('Salam sejahtera') }} {{ $emailApplication->user->name ?? __('Pemohon') }},</p>
        <p>{{ __('Merujuk kepada permohonan Akaun E-mel / ID Pengguna ICT MOTAC anda dengan nombor rujukan') }}
            <strong>#{{ $emailApplication->id }}</strong>.</p>
        <p>{{ __('Dukacita dimaklumkan bahawa permohonan anda telah') }} <strong>{{ __('Ditolak') }}</strong>.</p>

        @if ($emailApplication->rejection_reason)
            <div class="alert-details alert-danger">
                <p style="margin-top:0;"><strong>{{ __('Sebab Penolakan') }}:</strong></p>
                <p style="margin-bottom:0; white-space: pre-wrap;">{{ $emailApplication->rejection_reason }}</p>
            </div>
        @endif

        <p>{{ __('Untuk maklumat lanjut atau pertanyaan, sila kemukakan semula permohonan dengan pembetulan yang diperlukan atau hubungi Bahagian Pengurusan Maklumat (BPM).') }}
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
