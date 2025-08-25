@extends('layouts.email')

@section('title', __('Permohonan Ditolak'))

@section('content')
    @php
        $application = $application ?? $notification->application;
        $rejecter = $rejecter ?? $notification->rejecter;
        $reason = $reason ?? $notification->rejectionReason ?? $application->rejection_reason ?? '';
        $applicantName = $application->user?->name ?? $notifiable->name ?? __('Pemohon');
        $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        $applicationId = $application->id ?? 'N/A';
        $actionUrl = $actionUrl ?? route('loan-applications.show', $applicationId);
    @endphp

    <h4 class="mb-3">{{ __('Salam Sejahtera, :name,', ['name' => $applicantName]) }}</h4>

    <div class="alert alert-danger" role="alert">
        <h5 class="alert-heading">{{ __('Permohonan Ditolak') }}</h5>
        <p>
            {{ __('Dukacita dimaklumkan bahawa :type anda (ID: :id) telah DITOLAK.', [
                'type' => strtolower($applicationTypeDisplay),
                'id' => "#$applicationId"
            ]) }}
        </p>
    </div>

    @if($reason !== null && trim($reason) !== '')
        <div class="card mt-4">
            <div class="card-header">
                {{ __('Alasan Penolakan oleh :name', ['name' => $rejecter->name ?? '-']) }}
            </div>
            <div class="card-body">
                <p class="fst-italic">"{{ $reason }}"</p>
            </div>
        </div>
    @else
        <p class="mt-3">{{ __('Permohonan anda ditolak oleh :name. Tiada sebab khusus dinyatakan.', ['name' => $rejecter->name ?? '-']) }}</p>
    @endif

    <p class="mt-4">{{ __('Untuk maklumat lanjut atau pertanyaan, sila kemukakan semula permohonan dengan pembetulan yang diperlukan atau hubungi Bahagian Pengurusan Maklumat (BPM).') }}</p>

    @if($actionUrl && $actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-secondary">{{ __('Lihat Butiran Permohonan') }}</a>
        </div>
    @endif

    <p class="mt-4">{{ __('Sekian, terima kasih.') }}</p>
@endsection
