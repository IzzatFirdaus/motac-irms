{{-- resources/views/approvals/approvals-history.blade.php --}}
@extends('layouts.app')

@section('title', __('Sejarah Kelulusan'))

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center"><i class="bi bi-clock-history me-2"></i>{{ __('Sejarah Kelulusan') }}</h1>
            <a href="{{ route('approvals.dashboard') }}"
                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                <i class="bi bi-speedometer2 me-1"></i>
                {{ __('Papan Pemuka Kelulusan') }}
            </a>
        </div>

        {{-- Show general alert messages --}}
        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm motac-card">
            <div class="card-header bg-light py-3 motac-card-header">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('Rekod Kelulusan Lepas') }}</h3>
            </div>
            @if ($approvals->isEmpty())
                <div class="card-body text-center text-muted p-5">
                    <i class="bi bi-collection fs-1 text-secondary mb-2"></i>
                    <h5 class="mb-1">{{ __('Tiada Sejarah Ditemui') }}</h5>
                    <p class="small">{{ __('Anda belum mempunyai sejarah kelulusan.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="ps-3">{{ __('ID Kelulusan') }}</th>
                                <th scope="col">{{ __('Jenis Permohonan') }}</th>
                                <th scope="col">{{ __('Pemohon') }}</th>
                                <th scope="col">{{ __('Tarikh Permohonan') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col" class="text-center">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvals as $approvalTask)
                                <tr>
                                    <td class="ps-3">{{ $approvalTask->id }}</td>
                                    <td>
                                        @php
                                            $itemTypeDisplay = __('Tidak Diketahui');
                                            if ($approvalTask->approvable instanceof \App\Models\LoanApplication) {
                                                $itemTypeDisplay = __('Pinjaman Peralatan');
                                            } elseif ($approvalTask->approvable instanceof \App\Models\HelpdeskTicket) {
                                                $itemTypeDisplay = __('Tiket Meja Bantuan');
                                            }
                                        @endphp
                                        <span class="badge bg-secondary">{{ $itemTypeDisplay }}</span>
                                    </td>
                                    <td>{{ $approvalTask->approvable->user->name ?? '-' }}</td>
                                    <td>{{ $approvalTask->approvable->created_at->translatedFormat('d M Y') ?? '-' }}</td>
                                    <td>
                                        @include('approvals._partials.status-badge', ['status' => $approvalTask->status])
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('approvals.show', $approvalTask->id) }}"
                                                class="btn btn-sm btn-icon btn-outline-secondary motac-btn-icon"
                                                title="{{ __('Lihat Butiran Kelulusan') }}">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            @if ($approvalTask->approvable)
                                                @can('view', $approvalTask->approvable)
                                                    @php
                                                        $route = '';
                                                        if ($approvalTask->approvable instanceof \App\Models\LoanApplication) {
                                                            $route = 'loan-applications.show';
                                                        } elseif ($approvalTask->approvable instanceof \App\Models\HelpdeskTicket) {
                                                            $route = 'helpdesk.view';
                                                        }
                                                    @endphp
                                                    @if ($route)
                                                        <a href="{{ route($route, $approvalTask->approvable->id) }}" class="btn btn-sm btn-icon btn-outline-primary motac-btn-icon" title="{{ __('Lihat Permohonan Asal') }}">
                                                            <i class="bi bi-file-earmark-text-fill"></i>
                                                        </a>
                                                    @endif
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($approvals->hasPages())
                    <div class="card-footer bg-light border-top py-3 d-flex justify-content-center">
                        {{ $approvals->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
