@extends('layouts.email')

@section('title', 'Permohonan Diluluskan')

@section('content')
    @php
        $application = $notification->getApplication();
        $applicantName = $application->user?->name ?? 'Pemohon';
        // Removed $isLoanApp check as it will always be a LoanApplication
        $applicationTypeDisplay = 'Permohonan Pinjaman Peralatan ICT'; // Explicitly set for Loan Applications
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
            {{-- Now always treat as LoanApplication --}}
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
        </div>
    </div>

    @if ($actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-primary">Lihat Permohonan</a>
        </div>
    @endif

    <p class="mt-4">Sekian, terima kasih.</p>
@endsection
