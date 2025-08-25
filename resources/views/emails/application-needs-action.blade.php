@extends('layouts.email')

@section('title', __('Tindakan Kelulusan Diperlukan'))

@section('content')
    @php
        $approvalTask = $approvalTask ?? $notification->approvalTask;
        $application = $application ?? $approvalTask->approvable;
        $approver = $approver ?? $notification->approver ?? $notifiable;
        $itemTypeDisplayName = __('Permohonan Pinjaman Peralatan ICT');
        $applicationId = $application->id ?? 'N/A';
        $stageName = \App\Models\Approval::getStageDisplayName($approvalTask->stage);
        $applicantName = $application->user?->name ?? __('Pemohon Tidak Dikenali');
        $reviewUrl = $reviewUrl ?? route('approvals.show', $approvalTask->id);
    @endphp

    <h4 class="mb-3">{{ __('Salam :name,', ['name' => $approver->name]) }}</h4>

    <p>
        {{ __('Satu permohonan memerlukan perhatian dan tindakan anda untuk peringkat kelulusan ":stage".', [
            'stage' => $stageName
        ]) }}
    </p>

    <div class="card mt-4">
        <div class="card-header">
            {{ __('Butiran Permohonan') }}
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>{{ __('Jenis Permohonan') }}:</strong> {{ $itemTypeDisplayName }}</li>
            <li class="list-group-item"><strong>{{ __('ID Permohonan') }}:</strong> #{{ $applicationId }}</li>
            <li class="list-group-item"><strong>{{ __('Pemohon') }}:</strong> {{ $applicantName }}</li>
            @if ($application->purpose)
                <li class="list-group-item"><strong>{{ __('Tujuan') }}:</strong> {{ $application->purpose }}</li>
            @endif
        </ul>
    </div>

    <p class="mt-4">{{ __('Sila log masuk ke sistem untuk menyemak butiran permohonan dan mengambil tindakan selanjutnya.') }}</p>

    @if ($reviewUrl && $reviewUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $reviewUrl }}" class="btn btn-primary">{{ __('Lihat Tugasan Kelulusan') }}</a>
        </div>
    @endif

    <p class="mt-4">{{ __('Sekian, terima kasih.') }}</p>
@endsection
