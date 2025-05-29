{{-- resources/views/emails/welcome.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang ke MOTAC ICT - Akaun E-mel Anda Disediakan</title>
    <style>
        body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; line-height: 1.6; color: #212529; background-color: #f8f9fa; margin: 0; padding: 20px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 25px 35px; border-radius: 0.375rem; border: 1px solid #dee2e6; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        h1 { color: #1A202C; margin-top: 0; margin-bottom: 0.75rem; font-size: 24px; }
        p { margin-bottom: 1rem; }
        .footer { margin-top: 25px; font-size: 0.875em; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 15px; text-align: center; }
        .alert-details { margin-top: 20px; padding: 1rem; border: 1px solid transparent; border-radius: 0.375rem; margin-bottom: 1rem; }
        .alert-success { color: #0a3622; background-color: #d1e7dd; border-color: #badbcc; } /* Using success for credentials box */
        .alert-success p { margin-bottom: 0.5rem; }
        .alert-success strong { display: inline-block; min-width: 130px; /* Adjust as needed */ }

        .button { display: inline-block; font-weight: 400; line-height: 1.5; color: #ffffff !important; text-align: center; text-decoration: none; vertical-align: middle; cursor: pointer; border: 1px solid transparent; padding: 0.375rem 0.75rem; font-size: 1rem; border-radius: 0.375rem; }
        .button-primary { background-color: #0d6efd; border-color: #0d6efd; }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Selamat Datang ke MOTAC ICT!</h1>
        <p>Salam sejahtera {{ $user->full_name ?? ($user->name ?? 'Pengguna') }},</p>
        <p>Akaun e-mel rasmi MOTAC ICT anda telah berjaya disediakan.</p>

        <div class="alert-details alert-success"> {{-- Changed class from credentials --}}
            <p style="margin-top:0;"><strong>Alamat E-mel:</strong> {{ $motacEmail }}</p>
            <p style="margin-bottom:0;"><strong>Kata Laluan Awal:</strong> {{ $password }}</p>
        </div>

        <p>Sila log masuk ke webmail MOTAC di {{ config('app.motac_webmail_url', '[Alamat Webmail Anda]') }} dan tukar kata laluan anda dengan segera untuk keselamatan akaun anda.</p>

        <p style="text-align: center; margin-top: 20px;">
            <a href="{{ config('app.motac_webmail_url', '#') }}" class="button button-primary">Log Masuk Webmail MOTAC</a>
        </p>

        <p>Jika anda mempunyai sebarang pertanyaan atau menghadapi masalah log masuk, sila hubungi Unit Sokongan Teknikal ICT MOTAC.</p>
        <p>Terima kasih,</p>
        <p>Unit ICT MOTAC</p>
        <div class="footer">
            <p>Ini adalah e-mel automatik dari Sistem Pengurusan Sumber MOTAC, sila jangan balas.</p>
            <p>&copy; {{ date('Y') }} MOTAC. Hak Cipta Terpelihara.</p>
        </div>
    </div>
</body>
</html>
