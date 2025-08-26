{{-- resources/views/auth/verify-email-page.blade.php --}}
{{-- Renamed from verify-email.blade.php for clarity and consistency --}}
@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Sahkan Alamat E-mel'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; }
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; }
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .auth-cover-bg-color { background-color: #eef3f7; }
        .card { border: 1px solid #dee2e6; }
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-basic px-4 d-flex align-items-center min-vh-100">
        <div class="authentication-inner py-4 w-100" style="max-width: 400px;">
            <div class="app-brand mb-4 d-flex justify-content-center">
                <a href="{{ url('/') }}" class="app-brand-link gap-2">
                    <span class="app-brand-logo demo">
                        @include('_partials.macros', ['height' => 32, 'withbg' => 'fill: var(--bs-primary);'])
                    </span>
                    <span class="app-brand-text demo text-body fw-bold fs-4 ms-1">{{ __(config('app.name', 'MOTAC')) }}</span>
                </a>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="mb-1 fw-semibold">{{ __('Sahkan Alamat E-mel Anda') }} <i class="bi bi-envelope-check-fill text-primary ms-1"></i></h3>
                    </div>
                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success mt-3 py-2 small d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ __('Pautan pengesahan baharu telah dihantar ke alamat e-mel yang anda berikan semasa pendaftaran.') }}
                        </div>
                    @endif
                    <p class="text-center mt-3 text-muted small">
                        {{ __('Terima kasih kerana mendaftar! Sebelum bermula, bolehkah anda mengesahkan alamat e-mel anda dengan mengklik pautan yang baru kami hantarkan kepada anda? Jika anda tidak menerima e-mel tersebut, kami dengan senang hati akan menghantar yang lain.') }}
                        <br><br>
                        {{ __('Alamat e-mel anda:') }} <strong class="text-dark">{{ Auth::user()->email }}</strong>
                    </p>
                    <div class="mt-4 d-flex flex-column justify-content-center gap-2">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary d-grid w-100">
                                <i class="bi bi-send-arrow-up-fill me-1"></i>{{ __('Hantar Semula E-mel Pengesahan') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger d-grid w-100">
                                <i class="bi bi-box-arrow-left me-1"></i>{{ __('Log Keluar') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
