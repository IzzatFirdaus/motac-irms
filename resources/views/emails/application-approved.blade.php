@extends('layouts.email')

@section('title', __('Permohonan Diluluskan'))

@section('content')
    @php
        $application = $application ?? $notification->getApplication();
        $applicantName = $application->user?->name ?? 'Pemohon';
        $applicationTypeDisplay = __('Permohonan Pinjaman Peralatan ICT');
        $applicationId = $application->id ?? 'N/A';
        $actionUrl = $actionUrl ?? ($notification->getActionUrl() ?? route('loan-applications.show', $applicationId));
    @endphp

    <h4 class="mb-3">{{ __('Salam Sejahtera, :name,', ['name' => $applicantName]) }}</h4>

    <p>
        {{ __('Berita baik! :type anda dengan nombor rujukan :id telah DILULUSKAN.', [
            'type' => $applicationTypeDisplay,
            'id' => "#$applicationId"
        ]) }}
    </p>

    <div class="card mt-4">
        <div class="card-header">
            {{ __('Butiran Permohonan') }}
        </div>
        <div class="card-body">
            @if ($application->purpose)
                <p><strong>{{ __('Tujuan') }}:</strong><br>{{ $application->purpose }}</p>
            @endif
            <p>
                <strong>{{ __('Tempoh Pinjaman') }}:</strong><br>
                {{ __('Dari') }} {{ $application->loan_start_date?->format('d/m/Y') }} {{ __('hingga') }} {{ $application->loan_end_date?->format('d/m/Y') }}
            </p>
            <hr>
            <p class="mt-3">
                {{ __('Sila berhubung dengan pegawai berkaitan di Bahagian Pengurusan Maklumat (BPM) untuk urusan pengambilan peralatan.') }}
            </p>
        </div>
    </div>

    @if ($actionUrl && $actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-primary">{{ __('Lihat Permohonan') }}</a>
        </div>
    @endif

    <p class="mt-4">{{ __('Sekian, terima kasih.') }}</p>
@endsection
