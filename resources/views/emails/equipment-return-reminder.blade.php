@extends('layouts.email')

@section('title', 'Peringatan Pulangan Peralatan ICT')

@section('content')
    @php
        $loanApplication = $notification->loanApplication;
        $daysUntilReturn = $notification->daysUntilReturn;
        $applicantName = $loanApplication->user?->name ?? ($notifiable->name ?? 'Pemohon');
        $expectedReturnDate = $loanApplication->loan_end_date?->translatedFormat(config('app.date_format_my', 'd/m/Y'));
        $isOverdue = $daysUntilReturn < 0;
    @endphp

    <h4 class="mb-3">Salam Sejahtera, {{ $applicantName }},</h4>

    @if ($isOverdue)
        <div class="alert alert-danger" role="alert">
            <h5 class="alert-heading">PERHATIAN: Peralatan Lewat Dipulangkan</h5>
            <p>Peralatan yang dipinjam di bawah Permohonan <strong>#:{{ $loanApplication->id }}</strong> telah <strong>LEWAT
                    DIPULANGKAN</strong> selama <strong>{{ abs($daysUntilReturn) }} hari</strong>.</p>
        </div>
    @elseif ($daysUntilReturn === 0)
        <div class="alert alert-warning" role="alert">
            <h5 class="alert-heading">Peringatan: Tarikh Akhir Pulangan Hari Ini</h5>
            <p>Ini adalah peringatan bahawa peralatan yang dipinjam di bawah Permohonan
                <strong>#:{{ $loanApplication->id }}</strong> perlu dipulangkan <strong>HARI INI</strong>.</p>
        </div>
    @else
        <p>Ini adalah peringatan mesra bahawa peralatan yang dipinjam di bawah Permohonan Pinjaman Peralatan ICT
            <strong>#:{{ $loanApplication->id }}</strong> perlu dipulangkan dalam masa <strong>{{ $daysUntilReturn }} hari
                lagi</strong>.</p>
    @endif


    <div class="card mt-4">
        <div class="card-header">Butiran Pinjaman</div>
        <div class="card-body">
            <p class="mb-2"><strong>Tujuan:</strong> {{ $loanApplication->purpose ?? 'N/A' }}</p>
            <p class="mb-0"><strong>Tarikh Pemulangan Dijangka:</strong> <span
                    class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">{{ $expectedReturnDate ?? 'N/A' }}</span></p>
        </div>
    </div>

    @if ($isOverdue)
        <p class="mt-4">Sila pulangkan peralatan tersebut dengan kadar <strong>SEGERA</strong> ke Unit ICT, Bahagian
            Pengurusan Maklumat. Kegagalan memulangkan peralatan boleh menyebabkan tindakan selanjutnya diambil.</p>
    @else
        <p class="mt-4">Sila pastikan peralatan dipulangkan di Unit ICT, Bahagian Pengurusan Maklumat pada atau sebelum
            tarikh tersebut.</p>
    @endif

    @if (isset($actionUrl) && $actionUrl !== '#')
        <div class="text-center mt-4">
            <a href="{{ $actionUrl }}" class="btn btn-primary">Lihat Butiran Permohonan</a>
        </div>
    @endif

    <p class="mt-4">Kerjasama anda amat dihargai. Sekian, terima kasih.</p>
@endsection
