{{-- resources/views/emails/email-application-approved.blade.php --}}
<!DOCTYPE html>
<html>
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
</html>
