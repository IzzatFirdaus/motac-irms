{{-- Remove <!DOCTYPE html>, <html>, <head>, <script for Tailwind>, <style> block --}}
@extends('layouts.app') {{-- Assuming layouts.app has Bootstrap 5 linked --}}

@section('title', 'Senarai Peralatan ICT') {{-- Added title section --}}

@section('content')
<div class="container py-4"> {{-- Bootstrap container --}}

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fs-3 fw-bold mb-0">Senarai Peralatan ICT</h2>
        {{-- @can('create', App\Models\Equipment::class) --}}
        <a href="{{ route('equipment.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill me-2" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
            </svg>
            Tambah Peralatan Baru
        </a>
        {{-- @endcan --}}
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($equipment->isEmpty())
        <div class="alert alert-info" role="alert">
            Tiada peralatan ICT ditemui dalam inventori.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0"> {{-- p-0 to make table flush with card edges --}}
                <div class="table-responsive">
                    <table class="table table-hover mb-0"> {{-- Added table-hover --}}
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-uppercase small text-muted">Jenis Aset</th>
                                <th scope="col" class="text-uppercase small text-muted">Jenama & Model</th>
                                <th scope="col" class="text-uppercase small text-muted">Tag ID MOTAC</th>
                                <th scope="col" class="text-uppercase small text-muted">Nombor Siri</th>
                                <th scope="col" class="text-uppercase small text-muted">Status</th>
                                <th scope="col" class="text-uppercase small text-muted">Lokasi Semasa</th>
                                <th scope="col" class="text-uppercase small text-muted">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($equipment as $item)
                                <tr>
                                    <td>{{ $item->asset_type ?? 'N/A' }}</td>
                                    <td>{{ $item->brand ?? 'N/A' }} {{ $item->model ?? 'N/A' }}</td>
                                    <td>{{ $item->tag_id ?? 'N/A' }}</td>
                                    <td>{{ $item->serial_number ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            switch ($item->status) {
                                                case 'available': $statusClass = 'bg-success'; break;
                                                case 'on_loan': $statusClass = 'text-dark bg-warning'; break;
                                                case 'under_maintenance': $statusClass = 'bg-info'; break;
                                                case 'disposed': case 'lost': case 'damaged': $statusClass = 'bg-danger'; break;
                                                default: $statusClass = 'bg-secondary'; break;
                                            }
                                        @endphp
                                        <span class="badge rounded-pill {{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $item->current_location ?? 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('equipment.show', $item) }}" class="btn btn-sm btn-outline-primary me-1">Lihat</a>
                                        {{-- @can('update', $item) --}}
                                        <a href="{{ route('equipment.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        {{-- @endcan --}}
                                        {{-- Delete button would need a form --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if ($equipment->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $equipment->links() }} {{-- Laravel pagination should be Bootstrap 5 compatible by default --}}
            </div>
        @endif
    @endif
</div>
@endsection
