{{-- resources/views/emails/_partials/email-header.blade.php --}}
<div style="text-align: center; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; margin-bottom: 20px;">
    <img src="{{ $logoUrl ?? secure_asset('assets/img/logo/motac_logo_email.png') }}" alt="{{ __('Logo Kementerian Pelancongan, Seni dan Budaya Malaysia') }}" style="max-height: 60px; margin-bottom:10px;">
    <h1 style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 20px; color: #0055A4; margin:0;">
        {{ config('app.name', __('Sistem Pinjaman ICT & Meja Bantuan MOTAC')) }}
    </h1>
</div>
