@extends('layouts.email')

@section('title', 'Permohonan Ditolak')

@section('content')
    @php
        $application = $notification->application;
        $rejecter = $notification->rejecter;
        $reason = $notification->rejectionReason;
        $applicantName = $application->user?->name ?? $notifiable->name ?? 'Pemohon';
        $isLoanApp = $application instanceof \App\Models\LoanApplication;
        $applicationTypeDisplay = $isLoanApp
            ? 'Permohonan Pinjaman Peralatan ICT'
            : 'Permohonan Akaun E-mel/ID Pengguna';
        $applicationId = $application->id ?? 'N/A';
        $actionUrl = $notification->getActionUrl();
    @endphp

    <h4 class="mb-3">Salam Sejahtera, {{ $applicantName }},</h4>

    <div class="alert alert-danger" role="alert">
        <h5 class="alert-heading">Permohonan Ditolak</h5>
        <p>
            Dukacita dimaklumkan bahawa {{ strtolower($applicationTypeDisplay) }} anda (ID: <strong>#:{{ $applicationId }}</strong>) telah <strong>DITOLAK</strong>.
        </p>
    </div>

    @if($reason !== null && trim($reason) !== '')
        <div class="card mt-4">
            <div class="card-header">
                Alasan Penolakan oleh {{ $rejecter->name }}
            </div>
            <div class="card-body">
                <p class="fst-italic">"{{ $reason }}"</p>
            </div>
        </div>
    @else
        <p class="mt-3">Permohonan anda ditolak oleh {{ $rejecter->name }}. Tiada sebab khusus dinyatakan.</p>
    @endif

    <p class="mt-4">Untuk maklumat lanjut atau pertanyaan, sila kemukakan semula permohonan dengan pembetulan yang diperlukan atau hubungi Bahagian Pengurusan Maklumat (BPM).</p>

    @if($actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-secondary">Lihat Butiran Permohonan</a>
        </div>
    @endif

    <p class="mt-4"><strong>Sekian, harap maklum.</strong></p>
@endsection
