@extends('layouts.email')

@section('title', 'Tindakan Kelulusan Diperlukan')

@section('content')
    @php
        $approvableItem = $notification->approvableItem;
        $approvalTask = $notification->approvalTask;
        $itemTypeDisplayName = $notification->getItemTypeDisplayName();
        $applicationId = $approvableItem->id ?? 'N/A';
        $stageName = \App\Models\Approval::getStageDisplayName($approvalTask->stage);
        $applicantName = $approvableItem->user?->name ?? 'Pemohon Tidak Dikenali';
        $actionUrl = $notification->getActionUrl();
    @endphp

    <h4 class="mb-3">Salam {{ $notifiable->name }},</h4>

    <p>
        Satu permohonan memerlukan perhatian dan tindakan anda untuk peringkat kelulusan
        <strong>"{{ $stageName }}"</strong>.
    </p>

    <div class="card mt-4">
        <div class="card-header">
            Butiran Permohonan
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><strong>Jenis Permohonan:</strong> {{ $itemTypeDisplayName }}</li>
            <li class="list-group-item"><strong>ID Permohonan:</strong> #{{ $applicationId }}</li>
            <li class="list-group-item"><strong>Pemohon:</strong> {{ $applicantName }}</li>
            @if ($approvableItem instanceof \App\Models\LoanApplication && $approvableItem->purpose)
                <li class="list-group-item"><strong>Tujuan:</strong> {{ $approvableItem->purpose }}</li>
            @elseif($approvableItem instanceof \App\Models\EmailApplication && $approvableItem->application_reason_notes)
                <li class="list-group-item"><strong>Tujuan/Catatan:</strong> {{ $approvableItem->application_reason_notes }}
                </li>
            @endif
        </ul>
    </div>

    <p class="mt-4">Sila log masuk ke sistem untuk menyemak butiran permohonan dan mengambil tindakan selanjutnya.</p>

    @if ($actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-primary">Lihat Tugasan Kelulusan</a>
        </div>
    @endif

    <p class="mt-4">Sekian, terima kasih.</p>
@endsection
