{{-- resources/views/emails/team-invitation.blade.php --}}
@component('mail::message')
    {{-- MOTAC Header for Markdown (Optional, if you want to customize Markdown header further) --}}
    <div style="text-align: center; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; margin-bottom: 20px;">
        <img src="{{ secure_asset('assets/img/logo/motac_logo_email.png') }}" alt="{{ __('Logo MOTAC') }}"
            style="max-height: 60px; margin-bottom:10px;">
        <h1 style="font-size: 20px; color: #0055A4; margin:0;">{{ config('app.name', __('Sistem Pengurusan Sumber MOTAC')) }}
        </h1>
    </div>

    # {{ __('Jemputan Menyertai Pasukan') }}

    {{ __('Anda telah dijemput untuk menyertai pasukan :team!', ['team' => $invitation->team->name]) }}

    @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
        {{ __('Jika anda belum mempunyai akaun, anda boleh mencipta akaun dengan menekan butang di bawah. Selepas mencipta akaun, anda boleh menekan butang penerimaan jemputan dalam e-mel ini untuk menerima jemputan pasukan:') }}

        @component('mail::button', ['url' => route('register'), 'color' => 'motac_primary'])
            {{ __('Cipta Akaun') }}
        @endcomponent

        {{ __('Jika anda sudah mempunyai akaun, anda boleh menerima jemputan ini dengan menekan butang di bawah:') }}
    @else
        {{ __('Anda boleh menerima jemputan ini dengan menekan butang di bawah:') }}
    @endif


    @component('mail::button', ['url' => $acceptUrl, 'color' => 'motac_success'])
        {{-- Using success color for accept action --}}
        {{ __('Terima Jemputan') }}
    @endcomponent

    {{ __('Jika anda tidak menjangkakan untuk menerima jemputan ke pasukan ini, anda boleh mengabaikan e-mel ini.') }}

    {{ __('Sekian, terima kasih.') }}<br>
    {{ config('app.name') }}

    @slot('subcopy')
        <p style="text-align: center; font-size: 12px; color: #777777; line-height: 1.5em;">
            {{ __('Ini adalah e-mel janaan komputer. Sila jangan balas e-mel ini.') }}<br>
            &copy; {{ date('Y') }} {{ __('Kementerian Pelancongan, Seni dan Budaya Malaysia') }}.
            {{ __('Hak Cipta Terpelihara.') }}
        </p>
    @endslot
@endcomponent
