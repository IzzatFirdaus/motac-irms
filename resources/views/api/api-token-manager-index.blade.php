{{-- resources/views/api/api-token-manager-index.blade.php --}}
{{-- Renamed from index.blade.php for clarity and consistency with naming conventions --}}

@extends('layouts.app') {{-- MOTAC themed Bootstrap 5 layout --}}

@section('title', __('Pengurusan Token API'))

@section('page-style')
    {{-- Page-level styles kept minimal; prefer token-driven classes (motac-card) in markup --}}
@endsection

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold text-dark mb-0">{{ __('Pengurusan Token API') }}</h1>
                </div>
                {{-- Livewire component for API token management. Uses token-driven classes via wrapper. --}}
                <div class="motac-card p-3">
                    @livewire('api.api-token-manager-page')
                </div>
            </div>
        </div>
    </div>
@endsection
