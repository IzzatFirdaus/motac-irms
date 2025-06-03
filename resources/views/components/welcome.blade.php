{{-- resources/views/welcome.blade.php --}}
{{--
    NOTE: This is a STRUCTURAL conversion to Bootstrap 5 and MOTAC theming.
    The ACTUAL CONTENT (text, links) of this page is from Laravel Jetstream
    and MUST BE REPLACED with MOTAC-specific information, dashboard links,
    or system overview relevant to your users.
--}}

@extends('layouts.app') {{-- Assuming layouts.app is your MOTAC-themed Bootstrap layout --}}

@section('title', __('Selamat Datang ke Sistem MOTAC'))

@section('content')
    <div class="container py-5">
        <div class="p-5 mb-4 bg-light rounded-3 shadow-sm"> {{-- Bootstrap Jumbotron-like styling --}}
            <div class="container-fluid py-4 text-center">
                <x-application-logo style="height: 60px; width: auto; margin-bottom: 1rem;" /> {{-- Ensure this renders MOTAC logo --}}

                <h1 class="display-5 fw-bold">{{ __('Selamat Datang ke Sistem Pengurusan Sumber Bersepadu MOTAC') }}</h1>
                <p class="fs-5 text-muted col-md-10 mx-auto">
                    {{-- ACTION REQUIRED: Replace this with MOTAC-specific welcome message or system purpose. --}}
                    {{ __('Platform berpusat anda untuk menguruskan permohonan emel/ID pengguna dan pinjaman peralatan ICT di Kementerian Pelancongan, Seni dan Budaya Malaysia.') }}
                </p>
                {{-- Example Button to Dashboard --}}
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-speedometer2 me-2"></i>{{ __('Pergi ke Papan Pemuka') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg mt-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Log Masuk') }}
                    </a>
                @endauth
            </div>
        </div>

        <div class="row align-items-md-stretch g-4">
            <div class="col-md-6">
                <div class="h-100 p-5 text-bg-dark rounded-3 shadow-sm"> {{-- Example dark card --}}
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-envelope-at-fill fs-2 text-white me-3"></i>
                        <h2 class="h4 text-white">{{ __('Permohonan Emel/ID') }}</h2>
                    </div>
                    <p class="small">
                        {{-- ACTION REQUIRED: MOTAC-specific content --}}
                        {{ __('Mohon akaun emel rasmi MOTAC atau ID pengguna untuk sistem dalaman dengan mudah melalui platform ini.') }}
                    </p>
                    @auth
                        <a href="{{-- route('email-applications.create') --}}" class="btn btn-outline-light btn-sm"> {{-- Placeholder route --}}
                            <i class="bi bi-plus-circle-fill me-1"></i>{{ __('Buat Permohonan Emel Baru') }}
                        </a>
                    @endauth
                </div>
            </div>
            <div class="col-md-6">
                <div class="h-100 p-5 bg-light border rounded-3 shadow-sm">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-laptop-fill fs-2 text-primary me-3"></i>
                        <h2 class="h4">{{ __('Pinjaman Peralatan ICT') }}</h2>
                    </div>
                    <p class="small">
                        {{-- ACTION REQUIRED: MOTAC-specific content --}}
                        {{ __('Perlukan peralatan ICT untuk tugasan rasmi? Semak ketersediaan dan buat permohonan pinjaman di sini.') }}
                    </p>
                    @auth
                        <a href="{{-- route('loan-applications.create') --}}" class="btn btn-outline-primary btn-sm"> {{-- Placeholder route --}}
                            <i class="bi bi-handbag-fill me-1"></i> {{ __('Mohon Pinjaman Peralatan') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Further MOTAC-specific sections can be added here: --}}
        {{-- e.g., Links to user guides, contact information for BPM, latest announcements related to the system. --}}
        {{--
    <div class="mt-5 pt-4 border-top">
        <h3 class="h5 fw-semibold mb-3">{{__('Sumber Berguna')}}</h3>
        <ul class="list-unstyled">
            <li class="mb-2"><a href="#" class="text-decoration-none"><i class="bi bi-book-half me-2"></i>{{__('Panduan Pengguna Sistem')}}</a></li>
            <li class="mb-2"><a href="#" class="text-decoration-none"><i class="bi bi-headset me-2"></i>{{__('Hubungi Meja Bantuan BPM')}}</a></li>
        </ul>
    </div>
    --}}
    </div>
@endsection
