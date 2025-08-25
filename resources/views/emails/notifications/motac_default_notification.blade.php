@component('mail::message')
@include('emails._partials.email-header', ['logoUrl' => $logoUrl ?? secure_asset('assets/img/logo/motac_logo_email.png')])

# {{ $greeting ?? __('Salam Sejahtera') }}@if(isset($notifiableName) && !empty($notifiableName)), {{ $notifiableName }}@endif

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

@if(isset($lines) && is_array($lines))
    @foreach ($lines as $line)
        {{ $line }}
    @endforeach
@endif

@if (isset($actionText) && isset($actionUrl) && $actionUrl)
@component('mail::button', ['url' => $actionUrl, 'color' => $actionColor ?? 'motac_primary'])
{{ __($actionText) }}
@endcomponent
@endif

@if(isset($outroLines) && is_array($outroLines))
    @foreach ($outroLines as $line)
        {{ $line }}
    @endforeach
@endif

{{ __('Sekian, terima kasih.') }}<br>
<br>
{{ __('Yang menjalankan amanah,') }}<br>
*{{ $senderName ?? __('Bahagian Pengurusan Maklumat') }}*<br>
*{{ $senderOrganization ?? __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}*

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
