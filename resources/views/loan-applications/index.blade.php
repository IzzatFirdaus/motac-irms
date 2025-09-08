<<<<<<< HEAD
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
=======
@extends('layouts.app')

@section('title', __('Senarai Permohonan Pinjaman Peralatan ICT Anda'))

@section('content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 fw-bold text-dark mb-0">{{ __('Senarai Permohonan Pinjaman Peralatan ICT Anda') }}</h2>
            {{-- Assuming 'loan-applications.create' is the route for the form page --}}
            <a href="{{ route('loan-applications.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-circle-fill me-2"></i>
                {{ __('Buat Permohonan Baru') }}
            </a>
        </div>

        @include('partials.alert-messages') {{-- Assuming you have a partial for session messages --}}

        @if ($applications->isEmpty())
            <div class="alert alert-info text-center shadow-sm" role="alert">
                <i class="bi bi-info-circle-fill fs-3 me-2 align-middle"></i>
                <span class="align-middle">{{ __('Tiada permohonan pinjaman peralatan ICT ditemui.') }}</span>
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-semibold px-3 py-2">ID</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-semibold px-3 py-2">
                                        {{ __('Tujuan Permohonan') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-semibold px-3 py-2">
                                        {{ __('Tarikh Pinjaman') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-semibold px-3 py-2">
                                        {{ __('Tarikh Dijangka Pulang') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-semibold px-3 py-2">
                                        {{ __('Status') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-semibold px-3 py-2">
                                        {{ __('Tarikh Hantar/Kemas Kini') }}</th>
                                    <th scope="col"
                                        class="small text-uppercase text-muted fw-semibold px-3 py-2 text-center">
                                        {{ __('Tindakan') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $app)
                                    <tr>
                                        <td class="px-3 py-2 small text-dark align-middle fw-medium">#{{ $app->id }}
                                        </td>
                                        <td class="px-3 py-2 small text-dark align-middle">
                                            {{ Str::limit($app->purpose, 45) }}</td>
                                        <td class="px-3 py-2 small text-muted align-middle">
                                            {{ optional($app->loan_start_date)->format('d M Y, H:i') ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small text-muted align-middle">
                                            {{ optional($app->loan_end_date)->format('d M Y, H:i') ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small align-middle">
                                            {{-- System Design Ref: 6.3 Reusable Blade Components [cite: 158, 172] --}}
                                            {{-- Assuming AppHelper exists and is configured, or replace with direct class --}}
                                            <span class="badge rounded-pill {{ \App\Helpers\Helpers::getStatusColorClass($app->status) }}">

                                        </td>
                                        <td class="px-3 py-2 small text-muted align-middle">
                                            {{ $app->updated_at->format('d M Y, H:i') }}</td>
                                        <td class="px-3 py-2 text-center align-middle">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('loan-applications.show', $app) }}"
                                                    class="btn btn-outline-primary border-0"
                                                    title="{{ __('Lihat Butiran') }}">
                                                    <i class="bi bi-eye-fill"></i>
                                                </a>
                                                @can('update', $app)
                                                    {{-- Policy check [cite: 225] --}}
                                                    {{-- Assuming 'loan-applications.edit' route exists and points to an edit form/Livewire component --}}
                                                    <a href="{{ route('loan-applications.edit', $app) }}"
                                                        class="btn btn-outline-secondary border-0"
                                                        title="{{ __('Kemaskini Draf') }}">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                @endcan
                                                @can('delete', $app)
                                                    {{-- Policy check [cite: 225] --}}
                                                    <form action="{{ route('loan-applications.destroy', $app) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('{{ __('Adakah anda pasti untuk memadam draf permohonan ini?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger border-0"
                                                            title="{{ __('Padam Draf') }}">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($applications->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $applications->links() }} {{-- Ensure Laravel pagination is configured for Bootstrap (usually default in AppServiceProvider) --}}
                </div>
            @endif
        @endif
    </div>
@endsection
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
