{{-- resources/views/notifications/motac-default-notification.blade.php --}}
{{-- Default MOTAC notification email template (renamed for new convention) --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa']) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __($subject ?? __('Notifikasi Sistem MOTAC')) }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; color: #333333; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;}
        .email-wrapper { padding: 20px 0; }
        .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border: 1px solid #dddddd; border-radius: 8px; overflow: hidden; }
        .email-header { background-color: #0055A4; color: #ffffff; padding: 25px; text-align: center; }
        .email-header img { max-height: 50px; margin-bottom: 10px; }
        .email-header h1 { margin: 0; font-size: 22px; font-weight: 600; }
        .email-body { padding: 25px 30px; line-height: 1.65; font-size: 15px; }
        .email-body p { margin-bottom: 16px; }
        .email-body .greeting { font-size: 17px; font-weight: bold; margin-bottom: 20px; color: #2c3e50; }
        .email-action { text-align: center; margin: 25px 0; }
        .email-action .button { background-color: #0055A4; color: #ffffff !important; padding: 12px 30px; text-decoration: none !important; border-radius: 5px; font-weight: bold; display: inline-block; font-size: 15px; }
        .email-footer { background-color: #f0f0f0; color: #777777; padding: 20px; text-align: center; font-size: 12px; border-top: 1px solid #dddddd; }
        .email-footer p { margin: 5px 0; line-height: 1.4em; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            {{-- Email header with logo and title --}}
            <div class="email-header">
                @if(isset($logoUrl) && !empty($logoUrl))
                    <img src="{{ $logoUrl }}" alt="{{ __('Logo MOTAC') }}">
                @else
                    <img src="{{ secure_asset('assets/img/logo/motac_logo_email.png') }}" alt="{{ __('Logo MOTAC') }}">
                @endif
                <h1>{{ __($emailTitleString ?? ($subject ?? __('Notifikasi Sistem MOTAC'))) }}</h1>
            </div>
            {{-- Main body of the email --}}
            <div class="email-body">
                <p class="greeting">{{ __($greeting ?? 'Salam Sejahtera') }}{{ isset($notifiableName) && !empty($notifiableName) ? ', ' . $notifiableName : '' }},</p>

                {{-- Intro lines --}}
                @if(isset($introLines) && is_array($introLines))
                    @foreach($introLines as $line)
                        <p>{!! __($line) !!}</p>
                    @endforeach
                @endif

                {{-- Content lines --}}
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

                {{-- Action button --}}
                @if(isset($actionUrl) && !empty($actionUrl) && isset($actionText) && !empty($actionText))
                <div class="email-action">
                    <a href="{{ $actionUrl }}" class="button">{{ __($actionText) }}</a>
                </div>
                @endif

                {{-- Outro lines --}}
                @if(isset($outroLines) && is_array($outroLines))
                    @foreach($outroLines as $line)
                        <p>{!! __($line) !!}</p>
                    @endforeach
                @endif

                <p>{{ __('Sekian, harap maklum.') }}</p>
                <p><em>{{ __('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.') }}</em></p>
            </div>
            {{-- Email footer --}}
            <div class="email-footer">
                <p>{{ __($appName ?? config('app.name', __('Sistem Pengurusan Sumber MOTAC'))) }}</p>
                <p>&copy; {{ date('Y') }} {{ __('Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya Malaysia.') }}<br>{{__('Hak Cipta Terpelihara.')}}</p>
            </div>
        </div>
    </div>
</body>
</html>
