@extends('layouts.app') {{-- Assuming layouts.app is your main Bootstrap layout [cite: 9] --}}

@section('title', __('Dashboard Pengguna'))

@section('content')
    <div class="container-fluid"> {{-- Or 'container' for fixed width --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ __('Dashboard Pengguna') }}</h1> {{-- [cite: 9] --}}
        </div>

        <div class="row">
            {{-- Quick Links/Cards --}}
            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fas fa-envelope-open-text fa-3x text-primary mb-3"></i> {{-- Example FontAwesome icon [cite: 9] --}}
                        <h5 class="card-title">{{ __('Permohonan Emel') }}</h5> {{-- [cite: 9] --}}
                        <p class="card-text small">{{ __('Mohon akaun emel MOTAC atau semak status permohonan anda.') }}</p>
                        {{-- [cite: 9] --}}
                        <a href="{{ route('resource-management.my-applications.email.index') }}"
                            class="btn btn-primary mt-auto">{{ __('Lihat Permohonan Emel') }}</a> {{-- [cite: 9] --}}
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fas fa-laptop fa-3x text-success mb-3"></i> {{-- Example FontAwesome icon [cite: 9] --}}
                        <h5 class="card-title">{{ __('Pinjaman Peralatan ICT') }}</h5> {{-- [cite: 9] --}}
                        <p class="card-text small">
                            {{ __('Mohon pinjaman peralatan ICT atau jejaki status permohonan anda.') }}</p>
                        {{-- [cite: 9] --}}
                        <a href="{{ route('resource-management.my-applications.loan.index') }}"
                            class="btn btn-success mt-auto">{{ __('Lihat Permohonan Pinjaman') }}</a> {{-- [cite: 9] --}}
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                        <i class="fas fa-bell fa-3x text-warning mb-3"></i> {{-- Example FontAwesome icon [cite: 9] --}}
                        <h5 class="card-title">{{ __('Notifikasi') }}</h5> {{-- [cite: 9] --}}
                        <p class="card-text small">{{ __('Semak notifikasi terkini berkaitan permohonan dan sistem.') }}
                        </p> {{-- [cite: 9] --}}
                        <a href="{{ route('notifications.index') }}"
                            class="btn btn-warning mt-auto">{{ __('Lihat Notifikasi') }}</a> {{-- [cite: 9] --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Placeholder for User's Active Loans or Recent Applications Summary --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Ringkasan Aktiviti Anda') }}</h5>
                    </div>
                    <div class="card-body">
                        {{-- Here you could embed a Livewire component showing a summary for the user --}}
                        {{-- @livewire('my-activity-summary') --}}
                        <p class="text-muted">{{ __('Paparan aktiviti terkini akan muncul di sini.') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
