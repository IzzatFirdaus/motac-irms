{{-- resources/views/emails/notifications/motac_default_notification.blade.php --}}
@component('mail::message')
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
{{ __($actionText) }}
@endcomponent
@endif

{{-- Salutation --}}
{{ __('Sekian, terima kasih.') }}<br>
<br>
{{ __('Yang menjalankan amanah,') }}<br>
*{{ __('Bahagian Pengurusan Maklumat') }}*<br>
*{{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}*

{{-- Subcopy (Footer Note) --}}
@slot('subcopy')
    <p style="text-align: center; font-size: 12px; color: #777;">
        {{ __('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.') }}<br>
        &copy; {{ date('Y') }} {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia. Hak Cipta Terpelihara.') }}
    </p>
@endslot
@endcomponent
