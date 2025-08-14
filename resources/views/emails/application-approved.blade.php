@extends('layouts.email')

@section('title', 'Permohonan Diluluskan')

@section('content')
    @php
        $application = $notification->getApplication();
        $applicantName = $application->user?->name ?? 'Pemohon';
        $isLoanApp = $application instanceof \App\Models\LoanApplication;
        $applicationTypeDisplay = $isLoanApp
            ? 'Permohonan Pinjaman Peralatan ICT'
            : 'Permohonan Akaun E-mel/ID Pengguna';
        $applicationId = $application->id ?? 'N/A';
        $actionUrl = $notification->getActionUrl();
    @endphp

    <h4 class="mb-3">Salam Sejahtera, {{ $applicantName }},</h4>

    <p>
        Berita baik! {{ $applicationTypeDisplay }} anda dengan nombor rujukan
        <strong>#:{{ $applicationId }}</strong> telah <strong>DILULUSKAN</strong>.
    </p>

    <div class="card mt-4">
        <div class="card-header">
            Butiran Permohonan
        </div>
        <div class="card-body">
            @if ($isLoanApp)
                @php $loanApp = $application; @endphp
                @if ($loanApp->purpose)
                    <p><strong>Tujuan:</strong><br>{{ $loanApp->purpose }}</p>
                @endif
                <p>
                    <strong>Tempoh Pinjaman:</strong><br>
                    Dari {{ $notification->formatDate($loanApp->loan_start_date) }} hingga
                    {{ $notification->formatDate($loanApp->loan_end_date) }}
                </p>
                <hr>
                <p class="mt-3">
                    Sila berhubung dengan pegawai berkaitan di Bahagian Pengurusan Maklumat (BPM) untuk urusan pengambilan
                    peralatan.
                </p>
            @else
                @php $emailApp = $application; @endphp
                @if ($emailApp->application_reason_notes)
                    <p><strong>Tujuan/Catatan:</strong><br>{{ $emailApp->application_reason_notes }}</p>
                @endif
                <hr>
                <p class="mt-3">
                    Pihak BPM akan memproses permohonan anda dan akan memaklumkan setelah akaun/ID pengguna anda sedia untuk
                    digunakan.
                </p>
            @endif
        </div>
    </div>

    @if ($actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-primary">Lihat Permohonan</a>
        </div>
    @endif

    <p class="mt-4">Terima kasih.</p>
    <p><strong>Sekian, harap maklum.</strong></p>
@endsection
