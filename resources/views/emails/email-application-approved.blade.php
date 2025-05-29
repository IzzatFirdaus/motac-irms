{{-- resources/views/emails/email-application-approved.blade.php --}}
<!DOCTYPE html>
<html>
<<<<<<< HEAD

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Permohonan Akaun E-mel ICT Diluluskan') }}</title>
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

        /* MOTAC Success Alert Colors */
        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border-color: #badbcc;
        }

        .alert-success p {
            margin-bottom: 0.5rem;
        }

        .alert-success strong {
            display: inline-block;
            min-width: 130px;
        }

        .button {
            display: inline-block;
            font-weight: 600;
            line-height: 1.5;
            color: #ffffff !important;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            border: 1px solid transparent;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 0.375rem;
        }

        /* MOTAC Success Button Color */
        .button-success {
            background-color: #28A745;
            border-color: #28A745;
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
        <p>{{ __('Sukacita dimaklumkan bahawa permohonan anda telah') }} <strong>{{ __('Diluluskan') }}</strong>.</p>

        @if (
            $emailApplication->status === \App\Models\EmailApplication::STATUS_COMPLETED &&
                $emailApplication->final_assigned_email)
            <div class="alert-details alert-success">
                <p style="margin-top:0;"><strong>{{ __('Maklumat Akaun E-mel Anda:') }}</strong></p>
                <p>{{ __('E-mel Rasmi MOTAC') }}: <strong>{{ $emailApplication->final_assigned_email }}</strong></p>
                <p>{{ __('ID Pengguna') }}:
                    <strong>{{ $emailApplication->final_assigned_user_id ?? __('Sila rujuk e-mel berasingan atau hubungi BPM ICT') }}</strong>
                </p>
                {{-- Password should never be sent in the same email as username/email for security. This part should ideally be removed or handled differently. --}}
                {{-- <p>Kata Laluan Awal: <strong>Sila rujuk e-mel berasingan atau hubungi BPM ICT</strong></p> --}}
                <p style="margin-bottom:0;">
                    {{ __('Anda kini boleh log masuk ke akaun e-mel rasmi MOTAC anda. Anda dinasihatkan untuk menukar kata laluan awal anda dengan segera.') }}
                </p>
            </div>
        @elseif ($emailApplication->status === \App\Models\EmailApplication::STATUS_APPROVED)
            <div class="alert-details alert-success">
                <p style="margin-top:0; margin-bottom: 0.5rem;">
                    {{ __('Permohonan anda telah diluluskan dan sedang dalam proses penyediaan akaun.') }}</p>
                <p style="margin-bottom:0;">
                    {{ __('Anda akan dimaklumkan semula setelah akaun anda berjaya disediakan.') }}</p>
            </div>
        @endif

        @if (isset($applicationUrl) && $applicationUrl)
            <p style="text-align: center; margin-top: 20px; margin-bottom: 10px;">
                <a href="{{ $applicationUrl }}" class="button button-success">{{ __('Lihat Butiran Permohonan') }}</a>
            </p>
        @endif

        <p>{{ __('Jika anda mempunyai sebarang pertanyaan, sila hubungi Bahagian Pengurusan Maklumat (BPM).') }}</p>
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

=======
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Akaun Emel ICT Diluluskan</title>
    <style>
        body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; line-height: 1.6; color: #212529; background-color: #f8f9fa; margin: 0; padding: 20px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 25px 35px; border-radius: 0.375rem; border: 1px solid #dee2e6; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        h1 { color: #1A202C; margin-top: 0; margin-bottom: 0.75rem; font-size: 24px; }
        p { margin-bottom: 1rem; }
        .footer { margin-top: 25px; font-size: 0.875em; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 15px; text-align: center; }
        .alert-details { margin-top: 20px; padding: 1rem; border: 1px solid transparent; border-radius: 0.375rem; margin-bottom: 1rem; }
        .alert-success { color: #0a3622; background-color: #d1e7dd; border-color: #badbcc; }
        .button { display: inline-block; font-weight: 400; line-height: 1.5; color: #ffffff !important; text-align: center; text-decoration: none; vertical-align: middle; cursor: pointer; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; border-radius: 0.375rem; }
        .button-success { background-color: #198754; border-color: #198754; }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Notifikasi Permohonan Akaun Emel ICT</h1>
        <p>Salam sejahtera {{ $emailApplication->user->name ?? 'Pemohon' }},</p>
        <p>Merujuk kepada permohonan Akaun Emel / ID Pengguna ICT MOTAC anda dengan nombor rujukan <strong>#{{ $emailApplication->id }}</strong>.</p>
        <p>Sukacita dimaklumkan bahawa permohonan anda telah <strong>Diluluskan</strong>.</p>

        @if ($emailApplication->status === 'completed' && $emailApplication->final_assigned_email)
            <div class="alert-details alert-success">
                <p style="margin-top:0;"><strong>Maklumat Akaun E-mel Anda:</strong></p>
                <p>E-mel Rasmi MOTAC: <strong>{{ $emailApplication->final_assigned_email }}</strong></p>
                <p>ID Pengguna: <strong>{{ $emailApplication->final_assigned_user_id ?? 'Sila rujuk e-mel berasingan atau hubungi BPM ICT' }}</strong></p>
                <p>Kata Laluan Awal: <strong>Sila rujuk e-mel berasingan atau hubungi BPM ICT</strong></p>
                <p style="margin-bottom:0;">Anda kini boleh log masuk ke akaun e-mel rasmi MOTAC anda.</p>
            </div>
        @elseif ($emailApplication->status === 'approved')
            <div class="alert-details alert-success">
                <p style="margin-top:0; margin-bottom: 0.5rem;">Permohonan anda telah diluluskan dan sedang dalam proses penyediaan akaun e-mel.</p>
                <p style="margin-bottom:0;">Anda akan dimaklumkan semula setelah akaun e-mel anda berjaya disediakan.</p>
            </div>
        @endif

        {{-- @if (isset($applicationUrl))
            <p style="text-align: center; margin-top: 20px;">
                <a href="{{ $applicationUrl }}" class="button button-success">Lihat Butiran Permohonan</a>
            </p>
        @endif --}}

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
>>>>>>> b3ca845 (code additions and edits)
</html>
