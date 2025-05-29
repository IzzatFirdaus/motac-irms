{{-- resources/views/emails/loan-application-rejected.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Pinjaman Peralatan ICT Ditolak</title>
    <style>
        body { font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; line-height: 1.6; color: #212529; background-color: #f8f9fa; margin: 0; padding: 20px; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; padding: 25px 35px; border-radius: 0.375rem; border: 1px solid #dee2e6; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        h1 { color: #1A202C; margin-top: 0; margin-bottom: 0.75rem; font-size: 24px; }
        p { margin-bottom: 1rem; }
        .footer { margin-top: 25px; font-size: 0.875em; color: #6c757d; border-top: 1px solid #dee2e6; padding-top: 15px; text-align: center; }
        .alert-details { margin-top: 20px; padding: 1rem; border: 1px solid transparent; border-radius: 0.375rem; margin-bottom: 1rem; }
        .alert-danger { color: #58151c; background-color: #f8d7da; border-color: #f1aeb5; }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Notifikasi Permohonan Pinjaman Peralatan ICT</h1>
        <p>Salam sejahtera {{ $loanApplication->user->name ?? 'Pemohon' }},</p>
        <p>Merujuk kepada permohonan Pinjaman Peralatan ICT anda dengan nombor rujukan <strong>#{{ $loanApplication->id }}</strong>.</p>
        <p>Dukacita dimaklumkan bahawa permohonan anda telah <strong>Ditolak</strong>.</p>

        @if ($loanApplication->rejection_reason)
            <div class="alert-details alert-danger">
                <p style="margin-top:0;"><strong>Sebab Penolakan:</strong></p>
                <p style="margin-bottom:0;">{{ $loanApplication->rejection_reason }}</p>
            </div>
        @endif

        <p>Untuk maklumat lanjut, sila hubungi bahagian BPM ICT.</p>
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
