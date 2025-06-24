{{-- resources/views/loan-applications/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Permohonan Pinjaman ICT Saya'))

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <h1 class="h2 fw-bold text-body mb-0 d-flex align-items-center">
                <i class="bi bi-card-list me-2"></i>{{ __('Senarai Permohonan Pinjaman Saya') }}
            </h1>
            @can('create', App\Models\LoanApplication::class)
                <a href="{{ route('loan-applications.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-plus-circle-fill me-2"></i>
                    {{ __('Buat Permohonan Baru') }}
                </a>
            @endcan
        </div>

        @include('_partials._alerts.alert-general')

        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h3 class="h5 card-title fw-semibold mb-0">{{ __('Sejarah Permohonan Pinjaman Anda') }}</h3>
            </div>
            @if ($applications->isEmpty())
                <div class="card-body text-center text-muted p-5">
                    <i class="bi bi-folder-x fs-1 text-secondary mb-2"></i>
                    <h5 class="mb-1">{{ __('Tiada Permohonan Ditemui') }}</h5>
                    <p class="small">{{ __('Anda belum membuat sebarang permohonan pinjaman peralatan ICT.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-dark mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">ID</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tujuan') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Tarikh Pinjaman') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2">{{ __('Status') }}</th>
                                <th class="small text-uppercase text-muted fw-medium px-3 py-2 text-center">{{ __('Tindakan') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $app)
                                <tr>
                                    <td class="px-3 py-2 small text-body fw-medium">#{{ $app->id }}</td>
                                    <td class="px-3 py-2 small text-body">{{ Str::limit($app->purpose, 50) }}</td>
                                    <td class="px-3 py-2 small text-muted">{{ optional($app->loan_start_date)->translatedFormat('d M Y') ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 small"><x-resource-status-panel :resource="$app" statusAttribute="status" type="loan_application" /></td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a href="{{ route('loan-applications.show', $app) }}" class="btn btn-sm btn-icon btn-outline-primary border-0" title="{{ __('Lihat Butiran') }}">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            @can('update', $app)
                                                <a href="{{ route('loan-applications.edit', $app) }}" class="btn btn-sm btn-icon btn-outline-secondary border-0" title="{{ __('Kemaskini Draf') }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                            @endcan
                                            @can('delete', $app)
                                                {{-- This button now triggers the modal --}}
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger border-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteLoanModal"
                                                        data-delete-url="{{ route('loan-applications.destroy', $app) }}"
                                                        title="{{ __('Padam Draf') }}">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
             @if ($applications->hasPages())
                <div class="card-footer border-top py-3 d-flex justify-content-center">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL for Delete Confirmation --}}
    <div class="modal fade" id="deleteLoanModal" tabindex="-1" aria-labelledby="deleteLoanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteLoanModalLabel">{{ __('Sahkan Padam Permohonan') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Adakah anda pasti untuk memadam draf permohonan ini? Tindakan ini tidak boleh dibatalkan.') }}
                </div>
                <div class="modal-footer">
                    {{-- The form is now inside the modal and its action will be set by JavaScript --}}
                    <form id="deleteLoanForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Ya, Padam') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-scripts')
{{-- JavaScript to dynamically set the form action in the modal --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteLoanModal = document.getElementById('deleteLoanModal');
        if (deleteLoanModal) {
            deleteLoanModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;
                // Extract URL from data-delete-url attribute
                const deleteUrl = button.getAttribute('data-delete-url');
                // Get the form inside the modal
                const deleteForm = document.getElementById('deleteLoanForm');
                // Update the form's action attribute
                deleteForm.setAttribute('action', deleteUrl);
            });
        }
    });
</script>
@endpush
