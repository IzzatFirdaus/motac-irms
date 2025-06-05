@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Daftar Akaun Baru'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; } /* [cite: 297, 301, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 589, 590, 591, 592] */
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; } /* [cite: 297, 301, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 589, 590, 591, 592] */
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .form-control:focus, .form-check-input:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); } /* [cite: 297, 301, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 589, 590, 591, 592] */
        .form-check-input:checked { background-color: #0055A4; border-color: #0055A4; }
        .auth-cover-bg-color { background-color: #eef3f7; }
    </style>
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover authentication-bg">
  <div class="authentication-inner row m-0">
    <div class="d-none d-lg-flex col-lg-7 p-0">
      <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/motac-auth-professional-light.png') }}"
             alt="{{ __('Ilustrasi Pendaftaran MOTAC') }}" class="img-fluid my-5 auth-illustration">
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
             alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
      </div>
    </div>

    <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
      <div class="w-px-400 mx-auto">
        <div class="app-brand mb-4 d-flex justify-content-center">
          <a href="{{url('/')}}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                @include('_partials.macros',["height"=>32, "withbg"=>'fill: var(--bs-primary);'])
            </span>
            <span class="app-brand-text demo text-body fw-bold fs-4 ms-1">{{ __(config('app.name', 'MOTAC')) }}</span>
          </a>
        </div>

        <h3 class="mb-1 fw-semibold text-center">{{ __('Sertai Sistem Kami') }}</h3>
        <p class="mb-4 text-center text-muted small">{{ __('Sila lengkapkan maklumat di bawah untuk mendaftar.') }}</p> {{-- Made text small --}}

        <x-validation-errors class="mb-3" /> {{-- Added Jetstream validation errors --}}

        <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
          @csrf
          <div class="mb-3">
            <label for="name" class="form-label">{{ __('Nama Penuh') }} <span class="text-danger">*</span></label> {{-- Clarified label --}}
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="{{ __('Masukkan nama penuh anda') }}" autofocus value="{{ old('name') }}" required />
            @error('name')
            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></span> {{-- Ensure d-block --}}
            @enderror
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">{{ __('Alamat E-mel') }} <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('cth: pengguna@example.com') }}" value="{{ old('email') }}" required/>
            @error('email')
            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></span> {{-- Ensure d-block --}}
            @enderror
          </div>
          <div class="mb-3 form-password-toggle">
            <label class="form-label" for="password">{{ __('Kata Laluan') }} <span class="text-danger">*</span></label>
            <div class="input-group input-group-merge @error('password') is-invalid @enderror">
              <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="passwordHelpBlock" required autocomplete="new-password"/>
              <span class="input-group-text cursor-pointer toggle-password"><i class="bi bi-eye-slash-fill"></i></span>
            </div>
            <div id="passwordHelpBlock" class="form-text small">
                {{ __('Kata laluan mesti sekurang-kurangnya 8 aksara.') }} {{-- Example help text --}}
            </div>
            @error('password')
            <span class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></span> {{-- Ensure d-block --}}
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
                <label class="form-check-label small" for="terms"> {{-- Made label small --}}
                  {{ __('Saya bersetuju dengan') }}
                  <a href="{{ route('terms.show') }}" target="_blank" class="text-decoration-none">{{ __('terma perkhidmatan') }}</a> &amp; {{-- Changed to &amp; --}}
                  <a href="{{ route('policy.show') }}" target="_blank" class="text-decoration-none">{{ __('dasar privasi') }}</a> MOTAC. <span class="text-danger">*</span>
                </label>
              </div>
              @error('terms')
                <div class="invalid-feedback d-block" role="alert"><span class="fw-medium">{{ $message }}</span></div> {{-- Ensure d-block --}}
              @enderror
            </div>
          @endif
          <button type="submit" class="btn btn-primary d-grid w-100">
            <i class="bi bi-person-plus-fill me-1"></i>{{ __('Daftar Akaun') }}
          </button>
        </form>

        <p class="text-center mt-3 small"> {{-- Made text small --}}
          <span>{{ __('Sudah mempunyai akaun?') }}</span>
          @if (Route::has('login'))
          <a href="{{ route('login') }}" class="text-decoration-none ms-1 fw-medium"> {{-- Added fw-medium --}}
            <span>{{ __('Log masuk di sini') }}</span>
          </a>
          @endif
        </p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('custom-scripts')
<script>
    // Vanilla JS for password toggle
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function () {
                const input = this.closest('.input-group').querySelector('input');
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
