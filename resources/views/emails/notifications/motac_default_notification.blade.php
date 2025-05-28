{{-- resources/views/emails/notifications/motac_default_notification.blade.php --}}
@component('mail::message')
<<<<<<< HEAD
{{-- EDITED: Use the shared partial for the header to ensure consistency. --}}
@include('emails._partials.email-header', ['logoUrl' => $logoUrl ?? secure_asset('assets/img/logo/motac_logo_email.png')])

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
@component('mail::button', ['url' => $actionUrl, 'color' => $actionColor ?? 'motac_primary'])
=======
{{-- Email Header --}}
<div style="text-align: center; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; margin-bottom: 20px;">
    {{-- Replace with your actual MOTAC logo path --}}
    <img src="{{ asset('assets/img/logo/motac_logo_email.png') }}" alt="{{ __('Logo Kementerian Pelancongan, Seni dan Budaya Malaysia') }}" style="max-height: 60px; margin-bottom:10px;">
    <h1 style="font-size: 20px; color: #0050A0; margin:0;">{{ config('app.name', __('Sistem Pengurusan Sumber MOTAC')) }}</h1>
</div>

{{-- Greeting --}}
# {{ $greeting ?? __('Salam Sejahtera') }} {{ $notifiableName ?? '' }},

{{-- Email Lines (Content) --}}
@foreach ($lines as $line)
{{ $line }}
@endforeach

{{-- Action Button (if provided) --}}
@if (isset($actionText) && isset($actionUrl) && $actionUrl)
@component('mail::button', ['url' => $actionUrl, 'color' => 'motac_primary']) {{-- 'motac_primary' to be defined in theme --}}
>>>>>>> bb90b6b (file edits 280525)
{{ __($actionText) }}
@endcomponent
@endif

<<<<<<< HEAD
{{-- Outro Lines --}}
@if(isset($outroLines) && is_array($outroLines))
    @foreach ($outroLines as $line)
        {{ $line }}
    @endforeach
@endif

=======
>>>>>>> bb90b6b (file edits 280525)
{{-- Salutation --}}
{{ __('Sekian, terima kasih.') }}<br>
<br>
{{ __('Yang menjalankan amanah,') }}<br>
<<<<<<< HEAD
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
=======
*{{ __('Bahagian Pengurusan Maklumat') }}*<br>
*{{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}*

{{-- Subcopy (Footer Note) --}}
@slot('subcopy')
    <p style="text-align: center; font-size: 12px; color: #777;">
        {{ __('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.') }}<br>
        &copy; {{ date('Y') }} {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia. Hak Cipta Terpelihara.') }}
    </p>
@endslot
>>>>>>> bb90b6b (file edits 280525)
@endcomponent
