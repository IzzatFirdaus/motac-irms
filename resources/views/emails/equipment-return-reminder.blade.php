@extends('layouts.email')

@section('title', __('Peringatan Pulangan Peralatan ICT'))

@section('content')
    @php
        $loanApplication = $loanApplication ?? null;
        $daysUntilReturn = $daysUntilReturn ?? 0;
        $applicantName = $loanApplication->user?->name ?? ($notifiable->name ?? 'Pemohon');
        $expectedReturnDate = $loanApplication->loan_end_date?->translatedFormat(config('app.date_format_my', 'd/m/Y'));
        $isOverdue = $daysUntilReturn < 0;
        $actionUrl = $actionUrl ?? route('loan-applications.show', $loanApplication->id ?? 0);
    @endphp

    <h4 class="mb-3">{{ __('Salam Sejahtera, :name,', ['name' => $applicantName]) }}</h4>

    @if ($isOverdue)
        <div class="alert alert-danger" role="alert">
            <h5 class="alert-heading">{{ __('PERHATIAN: Peralatan Lewat Dipulangkan') }}</h5>
            <p>{{ __('Peralatan yang dipinjam di bawah Permohonan #:id telah LEWAT DIPULANGKAN selama :days hari.', [
                'id' => $loanApplication->id ?? 'N/A',
                'days' => abs($daysUntilReturn)
            ]) }}</p>
        </div>
    @elseif ($daysUntilReturn === 0)
        <div class="alert alert-warning" role="alert">
            <h5 class="alert-heading">{{ __('Peringatan: Tarikh Akhir Pulangan Hari Ini') }}</h5>
            <p>{{ __('Ini adalah peringatan bahawa peralatan yang dipinjam di bawah Permohonan #:id perlu dipulangkan HARI INI.', [
                'id' => $loanApplication->id ?? 'N/A'
            ]) }}</p>
        </div>
    @else
        <p>{{ __('Ini adalah peringatan mesra bahawa peralatan yang dipinjam di bawah Permohonan Pinjaman Peralatan ICT #:id perlu dipulangkan dalam masa :days hari lagi.', [
            'id' => $loanApplication->id ?? 'N/A',
            'days' => $daysUntilReturn
        ]) }}</p>
    @endif

    <div class="card mt-4">
        <div class="card-header">{{ __('Butiran Pinjaman') }}</div>
        <div class="card-body">
            <p class="mb-2"><strong>{{ __('Tujuan') }}:</strong> {{ $loanApplication->purpose ?? 'N/A' }}</p>
            <p class="mb-0"><strong>{{ __('Tarikh Pemulangan Dijangka') }}:</strong>
                <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">{{ $expectedReturnDate ?? 'N/A' }}</span>
            </p>
        </div>
    </div>

    @if ($isOverdue)
        <p class="mt-4">{{ __('Sila pulangkan peralatan tersebut dengan kadar SEGERA ke Unit ICT, Bahagian Pengurusan Maklumat. Kegagalan memulangkan peralatan boleh menyebabkan tindakan selanjutnya diambil.') }}</p>
    @else
        <p class="mt-4">{{ __('Sila pastikan peralatan dipulangkan di Unit ICT, Bahagian Pengurusan Maklumat pada atau sebelum tarikh tersebut.') }}</p>
    @endif

    @if ($actionUrl && $actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-primary">{{ __('Lihat Butiran Permohonan') }}</a>
        </div>
    @endif

    <p class="mt-4">{{ __('Kerjasama anda amat dihargai. Sekian, terima kasih.') }}</p>
@endsection
