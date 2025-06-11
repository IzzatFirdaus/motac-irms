{{-- resources/views/emails/welcome.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Selamat Datang ke Sistem ICT MOTAC - Akaun E-mel Anda Telah Disediakan') }}</title>
    <style>
        body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; line-height: 1.6; color: #212529; background-color: #f8f9fa; margin: 0; padding: 20px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 25px 35px; border-radius: 0.375rem; border: 1px solid #dee2e6; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        h1 { color: #1A202C; margin-top: 0; margin-bottom: 0.75rem; font-size: 22px; }
        p { margin-bottom: 1rem; }
        .footer { margin-top: 25px; font-size: 0.875em; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 15px; text-align: center; }
        .alert-details { margin-top: 20px; padding: 1rem; border: 1px solid transparent; border-radius: 0.375rem; margin-bottom: 1rem; }
        .alert-success { color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
        .alert-success p { margin-bottom: 0.5rem; }
        .alert-success strong { display: inline-block; min-width: 140px; }
        .button { display: inline-block; font-weight: 600; line-height: 1.5; color: #ffffff !important; text-align: center; text-decoration: none; vertical-align: middle; cursor: pointer; border: 1px solid transparent; padding: 0.5rem 1rem; font-size: 1rem; border-radius: 0.375rem; }
        .button-primary { background-color: #0055A4; border-color: #0055A4; }
    </style>
</head>
<body>
    <div class="email-container">
        @include('emails._partials.email-header', [
            'logoUrl' => secure_asset('assets/img/logo/motac_logo_email.png'),
        ])

        <h1>{{ __('Selamat Datang ke Sistem ICT MOTAC!') }}</h1>
        <p>{{ __('Salam sejahtera') }} {{ $user->full_name ?? ($user->name ?? __('Pengguna Baru')) }},</p>
        <p>{{ __('Akaun e-mel rasmi MOTAC ICT anda telah berjaya dicipta dan sedia untuk digunakan.') }}</p>

        <div class="alert-details alert-success">
            <p style="margin-top:0;"><strong>{{ __('Alamat E-mel') }}:</strong> {{ $motacEmail }}</p>
            {{-- EDITED: CRITICAL SECURITY FIX - Plain text password removed. --}}
            <p style="margin-bottom:0.5rem;">
                <strong>{{ __('Langkah Seterusnya') }}:</strong> {{ __('Sila tetapkan kata laluan anda melalui pautan selamat di bawah.') }}
            </p>
        </div>

        {{-- EDITED: CRITICAL SECURITY FIX - Replaced password display with a secure setup link. --}}
        {{-- The Mailable class sending this view must now generate and pass a $passwordSetupUrl variable. --}}
        @if (isset($passwordSetupUrl) && $passwordSetupUrl)
            <p style="text-align: center; margin-top: 25px; margin-bottom: 20px;">
                <a href="{{ $passwordSetupUrl }}" class="button button-primary">{{ __('Tetapkan Kata Laluan Anda') }}</a>
            </p>
            <p style="text-align: center; font-size: 0.8em; color: #6c757d;">
                ({{ __('Pautan ini sah untuk tempoh yang terhad sahaja.') }})
            </p>
        @endif

        <p>{{ __('Jika anda mempunyai sebarang pertanyaan atau menghadapi masalah, sila hubungi Unit Sokongan Teknikal ICT, Bahagian Pengurusan Maklumat (BPM).') }}</p>
        <p>{{ __('Sekian, terima kasih.') }}</p>
        <p>
            {{ __('Yang menjalankan amanah,') }}<br>
            {{ __('Unit ICT') }}<br>
            {{ __('Bahagian Pengurusan Maklumat (BPM)') }}<br>
            {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}
        </p>

        <div class="footer">
            <p>{{ __('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.') }}</p>
            <p>&copy; {{ date('Y') }} {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}.
                {{ __('Hak Cipta Terpelihara.') }}</p>
        </div>
    </div>
</body>
</html>
