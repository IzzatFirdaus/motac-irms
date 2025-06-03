{{-- resources/views/approvals/history.blade.php --}}
@extends('layouts.app')

@section('title', __('Sejarah Kelulusan'))

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('Sejarah Kelulusan') }}</h1>
            <a href="{{ route('approvals.dashboard') }}"
                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                <i class="bi bi-speedometer2 me-1"></i>
                {{ __('Papan Pemuka Kelulusan') }}
            </a>
        </div>

        {{-- Assuming global alert handling --}}

        <div class="card shadow-sm">
            <div class="card-header bg-light py-3">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('Rekod Kelulusan Lepas') }}</h3>
            </div>
            @if ($approvals->isEmpty())
                <div class="card-body">
                    <div class="alert alert-info text-center mb-0" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i>{{ __('Tiada kelulusan dalam sejarah pada masa ini.') }}
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">ID</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Jenis Permohonan') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Pemohon') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Tarikh Kelulusan Dibuat') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Status') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Pegawai Pelulus') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                    {{ __('Tarikh Keputusan Sebenar') }}</th>
                                <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2 text-end">
                                    {{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvals as $approval)
                                @php $approvableItem = $approval->approvable; @endphp
                                <tr>
                                    <td class="px-3 py-2 small text-dark">#{{ $approval->id ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small text-muted">
                                        @if ($approvableItem instanceof \App\Models\EmailApplication)
                                            <i class="bi bi-envelope-at me-1 text-info"></i>{{ __('Permohonan Emel') }}
                                        @elseif ($approvableItem instanceof \App\Models\LoanApplication)
                                            <i class="bi bi-laptop me-1 text-success"></i>{{ __('Pinjaman ICT') }}
                                        @else
                                            <i class="bi bi-question-circle me-1 text-secondary"></i>{{ __('Jenis Tidak Diketahui') }}
                                        @endif
                                        (#{{ optional($approvableItem)->id ?? 'N/A' }})
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ optional(optional($approvableItem)->user)->name ?? optional(optional($approvableItem)->user)->full_name ?? __('Tidak Diketahui') }}
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ optional($approval->created_at)->translatedFormat('d M Y, H:i A') ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small">
                                        <x-approval-status-badge :status="$approval->status" />
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ optional($approval->officer)->name ?? optional($approval->officer)->full_name ?? __('Tidak Diketahui') }}
                                    </td>
                                    <td class="px-3 py-2 small text-muted">
                                        {{ optional($approval->approval_timestamp)->translatedFormat('d M Y, H:i A') ?? 'N/A' }}
                                    </td>
                                    <td class="px-3 py-2 text-end">
                                        <div class="d-inline-flex align-items-center gap-1"> {{-- Reduced gap to 1 if buttons become too wide --}}
                                            @can('view', $approval)
                                                {{-- Made button larger and solid secondary --}}
                                                <a href="{{ route('approvals.show', $approval) }}"
                                                    class="btn btn-secondary p-2" {{-- Removed btn-sm, changed to p-2 --}}
                                                    title="{{ __('Lihat Butiran Kelulusan') }}">
                                                    <i class="bi bi-search"></i>
                                                </a>
                                            @endcan
                                            @if ($approvableItem)
                                                @php
                                                    $applicationDetailRouteName = null;
                                                    $applicationDetailRouteParams = null;
                                                    if ($approvableItem instanceof \App\Models\EmailApplication) {
                                                        $applicationDetailRouteName = 'email-applications.show';
                                                        $applicationDetailRouteParams = $approvableItem->id;
                                                    } elseif ($approvableItem instanceof \App\Models\LoanApplication) {
                                                        $applicationDetailRouteName = 'loan-applications.show';
                                                        $applicationDetailRouteParams = $approvableItem->id;
                                                    }
                                                @endphp
                                                @if ($applicationDetailRouteName && Route::has($applicationDetailRouteName))
                                                    {{-- Made button larger and solid primary --}}
                                                    <a href="{{ route($applicationDetailRouteName, $applicationDetailRouteParams) }}"
                                                        class="btn btn-primary p-2" {{-- Removed btn-sm, changed to p-2 --}}
                                                        title="{{ __('Lihat Permohonan Asal') }}">
                                                        <i class="bi bi-file-earmark-text-fill"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
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
