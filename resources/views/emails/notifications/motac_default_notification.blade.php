{{-- resources/views/emails/notifications/motac_default_notification.blade.php --}}
@component('mail::message')
{{-- Email Header --}}
<div style="text-align: center; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; margin-bottom: 20px;">
    {{-- Ensure logoUrl is absolute or secure_asset resolves correctly in email context --}}
    <img src="{{ $logoUrl ?? secure_asset('assets/img/logo/motac_logo_email.png') }}" alt="{{ __('Logo Kementerian Pelancongan, Seni dan Budaya Malaysia') }}" style="max-height: 60px; margin-bottom:10px;">
    <h1 style="font-size: 20px; color: #0055A4; margin:0;">{{ $appName ?? config('app.name', __('Sistem Pengurusan Sumber MOTAC')) }}</h1> {{-- MOTAC Blue for title --}}
</div>

{{-- Greeting --}}
# {{ $greeting ?? __('Salam Sejahtera') }}@if(isset($notifiableName) && !empty($notifiableName)), {{ $notifiableName }}@endif,

{{-- Email Lines (Content) --}}
@if(isset($introLines) && is_array($introLines))
    @foreach ($introLines as $line)
        {{ $line }}
    @endforeach
@endif

@if(isset($contentLines) && is_array($contentLines))
    @foreach ($contentLines as $line)
        {{ $line }}
    @endforeach
@endif

@if(isset($lines) && is_array($lines)) {{-- Fallback for original 'lines' variable --}}
    @foreach ($lines as $line)
        {{ $line }}
    @endforeach
@endif


{{-- Action Button (if provided) --}}
@if (isset($actionText) && isset($actionUrl) && $actionUrl)
@component('mail::button', ['url' => $actionUrl, 'color' => $actionColor ?? 'motac_primary']) {{-- 'motac_primary' should be defined in mail.php theme to be MOTAC Blue #0055A4 --}}
{{ __($actionText) }}
@endcomponent
@endif

{{-- Outro Lines --}}
@if(isset($outroLines) && is_array($outroLines))
    @foreach ($outroLines as $line)
        {{ $line }}
    @endforeach
@endif

{{-- Salutation --}}
{{ __('Sekian, terima kasih.') }}<br>
<br>
{{ __('Yang menjalankan amanah,') }}<br>
*{{ $senderName ?? __('Bahagian Pengurusan Maklumat') }}*<br>
*{{ $senderOrganization ?? __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}*

{{-- Subcopy (Footer Note) --}}
@isset($subcopy)
    @slot('subcopy')
        {{ $subcopy }}
    @endslot
@else
    @slot('subcopy')
        <p style="text-align: center; font-size: 12px; color: #777777; line-height: 1.5em;">
            {{ __('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.') }}<br>
            &copy; {{ date('Y') }} {{ $footerAppName ?? __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}. {{ __('Hak Cipta Terpelihara.') }}
        </p>
    @endslot
@endisset
@endcomponent
