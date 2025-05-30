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
