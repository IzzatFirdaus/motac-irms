{{-- resources/views/approvals/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Tugasan Kelulusan Menunggu Tindakan'))

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-2 mb-sm-0">
                {{ __('Tugasan Kelulusan Menunggu Tindakan Saya') }}
            </h1>
            <div>
                <a href="{{ route('approvals.dashboard') }}" {{-- System Design reference for approver dashboard --}}
                    class="btn btn-sm btn-outline-primary text-decoration-none d-inline-flex align-items-center me-2">
                    <i class="bi bi-speedometer2 me-1"></i> {{ __('Papan Pemuka Kelulusan') }}
                </a>
                <a href="{{ route('approvals.history') }}"
                    class="btn btn-sm btn-outline-secondary text-decoration-none d-inline-flex align-items-center">
                    <i class="bi bi-clock-history me-1"></i> {{ __('Sejarah Kelulusan Saya') }}
                </a>
            </div>
        </div>

        {{-- Corrected path to your general alert partial --}}
        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('Senarai Menunggu Kelulusan') }}</h3>
            </div>
            @if ($approvals->isEmpty())
                <div class="card-body text-center text-muted p-5">
                    <i class="bi bi-inbox-fill fs-1 text-secondary mb-2"></i>
                    <h5 class="mb-1">{{ __('Tiada Tugasan Baru') }}</h5>
                    <p class="small">{{ __('Tiada tugasan kelulusan yang menunggu tindakan anda pada masa ini.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('ID Tugasan') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Jenis Permohonan') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Pemohon') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Peringkat') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Dihantar Pada') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">
                                    {{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvals as $approvalTask)
                                @php $approvableItem = $approvalTask->approvable; @endphp
                                <tr>
                                    <td class="px-3 py-2 small text-dark">#{{ $approvalTask->id }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($approvableItem instanceof \App\Models\EmailApplication)
                                            <i class="bi bi-envelope-at me-1 text-info"></i>{{ __('Permohonan Emel') }}
                                            #{{ optional($approvableItem)->id }}
                                        @elseif ($approvableItem instanceof \App\Models\LoanApplication)
                                            <i class="bi bi-laptop me-1 text-success"></i>{{ __('Pinjaman ICT') }}
                                            #{{ optional($approvableItem)->id }}
                                        @else
                                            <i class="bi bi-question-circle me-1 text-secondary"></i>{{ __('Tidak Diketahui') }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ optional(optional($approvableItem)->user)->name ?? __('Tidak Diketahui') }}
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ \App\Models\Approval::getStageDisplayName($approvalTask->stage) }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ optional($approvalTask->created_at)->translatedFormat('d M Y, H:i A') }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @can('update', $approvalTask)
                                            <a href="{{ route('approvals.show', $approvalTask->id) }}"
                                                class="btn btn-sm btn-primary d-inline-flex align-items-center">
                                                <i class="bi bi-pencil-square me-1"></i> {{ __('Lihat & Ambil Tindakan') }}
                                            </a>
                                        @else
                                            <a href="{{ route('approvals.show', $approvalTask->id) }}"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                                                <i class="bi bi-eye-fill me-1"></i> {{ __('Lihat Sahaja') }}
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($approvals->hasPages())
                    <div class="card-footer bg-light border-top-0 py-3 d-flex justify-content-center">
                        {{ $approvals->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
