@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Daftar Akaun Baru'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; }
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; }
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .form-control:focus, .form-check-input:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); }
        .form-check-input:checked { background-color: #0055A4; border-color: #0055A4; }
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
             alt="{{ __('Ilustrasi Pendaftaran MOTAC') }}" class="img-fluid my-5 auth-illustration">
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}" {{-- Placeholder --}}
             alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
      </div>
    </div>
    {{-- /Left Text --}}

    {{-- Register Form --}}
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

        <h3 class="mb-1 fw-semibold text-center">{{ __('Sertai Sistem Kami') }}</h3>
        <p class="mb-4 text-center text-muted">{{ __('Sila lengkapkan maklumat di bawah untuk mendaftar.') }}</p>

        <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label">{{ __('Nama Pengguna') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="{{ __('Cth: ahmad_motac') }}" autofocus value="{{ old('name') }}" required />
            @error('name')
            <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">{{ __('Alamat E-mel') }} <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('cth: pengguna@example.com') }}" value="{{ old('email') }}" required/>
            @error('email')
            <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>
          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">{{ __('Kata Laluan') }} <span class="text-danger">*</span></label>
            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
              <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required autocomplete="new-password"/>
              <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></span>
            @enderror
          </div>

          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password_confirmation">{{ __('Sahkan Kata Laluan') }} <span class="text-danger">*</span></label>
            <div class="input-group input-group-merge">
              <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password_confirmation" required autocomplete="new-password"/>
              <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
            </div>
          </div>

          @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <div class="mb-3">
              <div class="form-check @error('terms') is-invalid @enderror">
                <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" required />
                <label class="form-check-label" for="terms">
                  {{ __('Saya bersetuju dengan') }}
                  <a href="{{ route('policy.show') }}" target="_blank" class="text-decoration-none">{{ __('dasar privasi') }}</a> &
                  <a href="{{ route('terms.show') }}" target="_blank" class="text-decoration-none">{{ __('terma perkhidmatan') }}</a>
                </label>
              </div>
              @error('terms')
                <div class="invalid-feedback" role="alert"><span class="fw-medium">{{ $message }}</span></div>
              @enderror
            </div>
          @endif
          <button type="submit" class="btn btn-primary d-grid w-100">
            <i class="bi bi-person-plus-fill me-1"></i>{{ __('Daftar Akaun') }}
          </button>
        </form>

        <p class="text-center mt-3"> {{-- Added mt-3 --}}
          <span>{{ __('Sudah mempunyai akaun?') }}</span>
          @if (Route::has('login'))
          <a href="{{ route('login') }}" class="text-decoration-none ms-1">
            <span>{{ __('Log masuk di sini') }}</span>
          </a>
          @endif
        </p>

        {{-- Social logins are usually not applicable for internal government systems.
             Consider removing if not part of MOTAC's requirements.
        <div class="divider my-4">
          <div class="divider-text">{{ __('atau') }}</div>
        </div>

        <div class="d-flex justify-content-center">
          <a href="javascript:;" class="btn btn-icon btn-label-facebook me-3">
            <i class="tf-icons fa-brands fa-facebook-f fs-5"></i>
          </a>

          <a href="javascript:;" class="btn btn-icon btn-label-google-plus me-3">
            <i class="tf-icons fa-brands fa-google fs-5"></i>
          </a>

          <a href="javascript:;" class="btn btn-icon btn-label-twitter">
            <i class="tf-icons fa-brands fa-twitter fs-5"></i>
          </a>
        </div>
        --}}
      </div>
    </div>
    {{-- /Register Form --}}
  </div>
</div>
@endsection

@push('custom-scripts')
<script>
    // Vanilla JS for password toggle
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function () {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');
                if (input.type === "password") {
                    input.type = "text";
                    icon.classList.remove('bi-eye-slash-fill');
                    icon.classList.add('bi-eye-fill');
                } else {
                    input.type = "password";
                    icon.classList.remove('bi-eye-fill');
                    icon.classList.add('bi-eye-slash-fill');
                }
            });
        });
    });
</script>
@endpush
