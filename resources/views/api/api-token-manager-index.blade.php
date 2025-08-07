{{-- resources/views/api/api-token-manager-index.blade.php --}}
{{-- Renamed from index.blade.php for clarity and consistency with naming conventions --}}

@extends('layouts.app') {{-- MOTAC themed Bootstrap 5 layout --}}

@section('title', __('Pengurusan Token API'))

@section('page-style')
    <style>
        /* Minimal custom styles to ensure Jetstream section components fit the Bootstrap theme */
        .x-form-section,
        .x-action-section {
            background-color: var(--bs-card-bg, #fff);
            border: 1px solid var(--bs-card-border-color, rgba(0, 0, 0, .175));
            border-radius: var(--bs-card-border-radius, .375rem);
            box-shadow: var(--bs-box-shadow-sm, 0 .125rem .25rem rgba(0, 0, 0, .075));
            margin-bottom: 1.5rem;
        }
    </style>
@endsection

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold text-dark mb-0">{{ __('Pengurusan Token API') }}</h1>
                </div>
                {{-- Livewire component for API token management. This should reference the renamed Blade component if applicable. --}}
                @livewire('api.api-token-manager-page')
            </div>
        </div>
    </div>
@endsection
