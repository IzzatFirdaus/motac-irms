{{-- MOTAC Welcome Page --}}
{{-- This page welcomes users to the MOTAC Integrated Resource Management System. --}}
{{-- Structure is Bootstrap 5, MOTAC theme. Replace text/links as needed for MOTAC context. --}}

@extends('layouts.app') {{-- Uses the MOTAC-themed layout --}}

@section('title', __('Selamat Datang ke Sistem MOTAC'))

@section('content')
    <div class="container py-5">
        <div class="p-5 mb-4 bg-light rounded-3 shadow-sm"> {{-- Bootstrap Jumbotron-like styling --}}
            <div class="container-fluid py-4 text-center">
                {{-- Display MOTAC/system logo --}}
                <x-application-logo style="height: 60px; width: auto; margin-bottom: 1rem;" />

                <h1 class="display-5 fw-bold">{{ __('Selamat Datang ke Sistem Pengurusan Sumber Bersepadu MOTAC') }}</h1>
                <p class="fs-5 text-muted col-md-10 mx-auto">
                    {{ __('Platform berpusat anda untuk menguruskan pinjaman peralatan ICT dan permintaan bantuan IT di Kementerian Pelancongan, Seni dan Budaya Malaysia.') }}
                </p>
                {{-- Button to Dashboard (only for authenticated users) --}}
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-speedometer2 me-2"></i> {{ __('Dashboard Saya') }}
                    </a>
                @endauth
            </div>
        </div>

        <div class="row g-4 mb-5">
            {{-- ICT Equipment Loan Feature --}}
            <div class="col-md-6">
                <div class="card h-100 shadow-sm motac-card">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3 text-primary"><i class="bi bi-laptop me-2"></i>{{ __('Pinjaman Peralatan ICT') }}</h2>
                        <p class="small">
                            {{ __('Perlukan peralatan ICT untuk tugasan rasmi? Semak ketersediaan dan buat permohonan pinjaman di sini.') }}
                        </p>
                        @auth
                            <a href="{{ route('loan-applications.create') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-handbag-fill me-1"></i> {{ __('Mohon Pinjaman Peralatan') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- Helpdesk Support/Ticketing Feature --}}
            <div class="col-md-6">
                <div class="card h-100 shadow-sm motac-card">
                    <div class="card-body p-4">
                        <h2 class="h5 fw-bold mb-3 text-success"><i class="bi bi-headset me-2"></i>{{ __('Sistem Meja Bantuan IT') }}</h2>
                        <p class="small">
                            {{ __('Mengalami masalah IT atau memerlukan bantuan teknikal? Hantar tiket sokongan di sini.') }}
                        </p>
                        @auth
                            <a href="{{ route('helpdesk.create') }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-ticket-detailed me-1"></i> {{ __('Buka Tiket Baru') }}
                            </a>
                            <a href="{{ route('helpdesk.index') }}" class="btn btn-outline-info btn-sm ms-2">
                                <i class="bi bi-card-checklist me-1"></i> {{ __('Lihat Tiket Saya') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        {{-- Section for guides, contacts, announcements --}}
        <div class="mt-5 pt-4 border-top">
            <h3 class="h5 fw-semibold mb-3">{{__('Sumber Berguna')}}</h3>
            <ul class="list-unstyled">
                <li class="mb-2">
                    <a href="#" class="text-decoration-none">
                        <i class="bi bi-book-half me-2"></i>{{__('Panduan Pengguna Sistem')}}
                    </a>
                </li>
                <li class="mb-2">
                    <a href="#" class="text-decoration-none">
                        <i class="bi bi-headset me-2"></i>{{__('Hubungi Meja Bantuan BPM')}}
                    </a>
                </li>
            </ul>
        </div>
    </div>
@endsection
