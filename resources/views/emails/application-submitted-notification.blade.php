{{-- EDITED: This view now extends a layout for consistency --}}
@extends('layouts.email')

@section('title', __('Tindakan Diperlukan: Permohonan Baru Dihantar'))

@section('content')
    {{-- The main content from the original file is placed here --}}
    <h2>{{ __('Tindakan Kelulusan Diperlukan') }}</h2>
    <p>{{ __('Salam Sejahtera') }} {{ $approverName ?? '' }},</p>
    <p>{{ __('Satu permohonan baharu telah dihantar dan memerlukan semakan serta tindakan kelulusan daripada pihak tuan/puan.') }}</p>

    <div class="alert-details alert-info" style="margin-top: 20px; padding: 1rem; border: 1px solid #b6d4fe; border-radius: 0.375rem; margin-bottom: 1rem; color: #004085; background-color: #cfe2ff;">
        <h3 style="margin-top:0; color: #1A202C; font-size: 18px;">{{ __('Maklumat Permohonan') }}</h3>
        <p><strong>{{ __('Jenis Permohonan') }}:</strong>
            @if ($application instanceof \App\Models\EmailApplication)
                {{ __('Permohonan E-mel / ID Pengguna') }}
            @elseif ($application instanceof \App\Models\LoanApplication)
                {{ __('Permohonan Pinjaman Peralatan ICT') }}
            @else
                {{ __('Jenis Tidak Diketahui') }}
            @endif
        </p>
        <p><strong>{{ __('ID Permohonan') }}:</strong> #{{ $application->id ?? 'N/A' }}</p>
        <p><strong>{{ __('Pemohon') }}:</strong> {{ $application->user->name ?? 'N/A' }}</p>
        <p><strong>{{ __('Tarikh Hantar') }}:</strong> {{ $application->created_at?->translatedFormat(config('app.datetime_format_my','d/m/Y H:i A')) ?? 'N/A' }}</p>

        @if ($application instanceof \App\Models\LoanApplication)
            <p><strong>{{ __('Tarikh Pinjaman') }}:</strong> {{ $application->loan_start_date?->translatedFormat(config('app.date_format_my','d/m/Y')) ?? 'N/A' }} - {{ $application->loan_end_date?->translatedFormat(config('app.date_format_my','d/m/Y')) ?? 'N/A' }}</p>
            @if ($application->loanApplicationItems->isNotEmpty())
                <p><strong>{{ __('Peralatan yang Dimohon') }}:</strong></p>
                <ul style="padding-left: 20px; margin: 0;">
                    @foreach ($application->loanApplicationItems as $item)
                        <li style="margin-bottom: 5px;">
                            {{ $item->equipment_type ?? __('Peralatan Tidak Diketahui') }}
                            ({{ __('Kuantiti') }}: {{ $item->quantity_requested ?? 1 }})
                            @if($item->notes) <span style="font-style: italic; color: #555;">- {{ $item->notes }}</span> @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif

        @if (isset($reviewUrl) && $reviewUrl)
            <p style="text-align: center; margin-top: 25px; margin-bottom: 10px;">
                <a href="{{ $reviewUrl }}" class="button button-primary" style="display: inline-block; font-weight: 600; color: #ffffff !important; text-align: center; text-decoration: none; padding: 0.5rem 1rem; border-radius: 0.375rem; background-color: #0055A4; border-color: #0055A4;">{{ __('Semak dan Ambil Tindakan') }}</a>
            </p>
        @else
            <p style="text-align: center; margin-top: 20px;">
                {{ __('Sila log masuk ke Sistem Pengurusan Sumber MOTAC untuk menyemak permohonan ini.') }}
            </p>
        @endif
    </div>

    <p>{{ __('Sila semak permohonan ini dengan kadar segera.') }}</p>
    <p>{{ __('Sekian, terima kasih.') }}</p>
@endsection
