@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Lupa Kata Laluan'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; }
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; }
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .form-control:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); }
        .auth-cover-bg-color { background-color: #eef3f7; }
    </style>
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <div class="authentication-inner row m-0">
    {{-- Left Text / Illustration Panel --}}
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        {{-- ACTION REQUIRED: Replace with MOTAC-appropriate visuals --}}
        <img src="{{ asset('assets/img/illustrations/motac-auth-professional-light.png') }}" {{-- Placeholder --}}
             alt="{{ __('Ilustrasi Lupa Kata Laluan MOTAC') }}" class="img-fluid my-5 auth-illustration">
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" {{-- Placeholder --}}
             alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
      </div>
    </div>
    {{-- /Left Text --}}

    {{-- Forgot Password Form --}}
    <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        {{-- Logo --}}
        <div class="app-brand mb-4 d-flex justify-content-center">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                @include('_partials.macros',["height"=>32, "withbg"=>'fill: var(--bs-primary);'])
            </span>
             <span class="app-brand-text demo text-body fw-bold fs-4 ms-1">{{ __(config('app.name', 'MOTAC')) }}</span>
          </a>
        </div>
        {{-- /Logo --}}

        <h3 class="mb-1 fw-semibold text-center">{{ __('Lupa Kata Laluan?') }} <i class="bi bi-lock-fill"></i></h3>
        <p class="mb-4 text-center text-muted">
            {{ __('Masukkan e-mel anda dan kami akan menghantar pautan untuk menetapkan semula kata laluan anda.') }}
        </p>

        @if (session('status'))
        <div class="alert alert-success mb-3 py-2 small" role="alert"> {{-- Bootstrap Alert --}}
          <i class="bi bi-check-circle-fill me-1"></i>{{ session('status') }}
        </div>
        @endif

        <form id="formAuthentication" class="mb-3" action="{{ route('password.email') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label">{{ __('Alamat E-mel') }}</label>
            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('cth: pengguna@motac.gov.my') }}" autofocus value="{{ old('email') }}" required>
            @error('email')
            <span class="invalid-feedback" role="alert">
              <span class="fw-medium">{{ $message }}</span>
            </span>
            @enderror
          </div>
          <button type="submit" class="btn btn-primary d-grid w-100">
            <i class="bi bi-envelope-fill me-1"></i>{{ __('Hantar Pautan Tetapan Semula') }}
          </button>
        </form>
        <div class="text-center">
          @if (Route::has('login'))
          <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center text-decoration-none">
            <i class="bi bi-chevron-left"></i> {{-- Bootstrap Icon --}}
            <small class="ms-1">{{ __('Kembali ke Log Masuk') }}</small>
          </a>
          @endif
        </div>
      </div>
    </div>
    {{-- /Forgot Password Form --}}
  </div>
</div>
@endsection
