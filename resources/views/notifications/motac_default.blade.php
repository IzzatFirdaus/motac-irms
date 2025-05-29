{{-- resources/views/emails/notifications/motac_default.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : 'ltr' }}"> {{-- Simplified RTL check --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __($subject ?? __('Notifikasi Sistem MOTAC')) }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333; }
        .email-container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; }
        .email-header { background-color: #0050A0; /* MOTAC Primary Blue */ color: #ffffff; padding: 20px; text-align: center; }
        .email-header img { max-height: 50px; }
        .email-header h1 { margin: 10px 0 0; font-size: 24px; }
        .email-body { padding: 20px; line-height: 1.6; }
        .email-body p { margin-bottom: 15px; }
        .email-body .greeting { font-size: 18px; font-weight: bold; margin-bottom: 20px; }
        .email-action { text-align: center; margin: 20px 0; }
        .email-action .button { background-color: #0050A0; /* MOTAC Primary Blue */ color: #ffffff !important; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; }
        .email-footer { background-color: #f0f0f0; color: #777777; padding: 15px; text-align: center; font-size: 12px; border-top: 1px solid #ddd; }
        .email-footer p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            {{-- Ensure $logoUrl is an absolute path accessible publicly, e.g., using secure_asset() or a full URL from config --}}
            <img src="{{ $logoUrl ?? secure_asset('assets/img/logo/motac_logo_email.png') }}" alt="{{ __('Logo MOTAC') }}">
            <h1>{{ __($emailTitleString ?? ($subject ?? __('Notifikasi Sistem MOTAC'))) }}</h1> {{-- Allow passing a specific title string --}}
        </div>
        <div class="email-body">
            <p class="greeting">{{ __($greeting ?? 'Salam Sejahtera') }}{{ isset($notifiableName) && !empty($notifiableName) ? ', ' . $notifiableName : '' }},</p>

            @if(isset($introLines) && is_array($introLines))
                @foreach($introLines as $line)
                    <p>{!! __($line) !!}</p> {{-- Allow basic HTML, ensure content is safe --}}
                @endforeach
            @endif

            @if(isset($contentLines) && is_array($contentLines))
                @foreach($contentLines as $line)
                    <p>{!! __($line) !!}</p>
                @endforeach
            @endif

            {{-- Fallback for simple 'lines' array --}}
            @if(empty($introLines) && empty($contentLines) && isset($lines) && is_array($lines))
                @foreach($lines as $line)
                    <p>{!! __($line) !!}</p>
                @endforeach
            @endif

            @if(isset($actionUrl) && !empty($actionUrl) && isset($actionText) && !empty($actionText))
            <div class="email-action">
                <a href="{{ $actionUrl }}" class="button">{{ __($actionText) }}</a>
            </div>
            @endif

            @if(isset($outroLines) && is_array($outroLines))
                @foreach($outroLines as $line)
                    <p>{!! __($line) !!}</p>
                @endforeach
            @endif

            <p>{{ __('Terima kasih.') }}</p>
            <p><em>{{ __('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.') }}</em></p>
        </div>
        <div class="email-footer">
            <p>{{ __($appName ?? config('app.name', 'Sistem Pengurusan Sumber MOTAC')) }}</p>
            <p>&copy; {{ date('Y') }} {{ __('Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia.') }}</p>
        </div>
    </div>
</body>
</html>
