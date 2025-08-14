@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Pengesahan Dua Faktor'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; } /* [cite: 297, 301, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 589, 590, 591, 592] */
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; } /* [cite: 297, 301, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 589, 590, 591, 592] */
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .form-control:focus { border-color: #0055A4; box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25); } /* [cite: 297, 301, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 589, 590, 591, 592] */
        .auth-cover-bg-color { background-color: #eef3f7; }
        /* Styling for Jetstream components to look like Bootstrap */
        .x-label { /* Corresponds to x-label */
            display: block;
            font-weight: 500; /* Bootstrap's form-label is often medium weight */
            margin-bottom: 0.5rem;
        }
        .x-input { /* Corresponds to x-input */
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem; /* Bootstrap form-control-sm size */
            font-weight: 400;
            line-height: 1.5;
            color: var(--bs-body-color);
            background-color: var(--bs-body-bg);
            background-clip: padding-box;
            border: 1px solid var(--bs-border-color);
            appearance: none;
            border-radius: 0.25rem; /* Bootstrap small radius */
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        .x-input.is-invalid {
            border-color: var(--bs-danger);
            padding-right: calc(1.5em + 0.75rem);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        .x-input:focus {
             border-color: #0055A4; /* MOTAC Blue */
            box-shadow: 0 0 0 0.25rem rgba(0, 85, 164, 0.25);
            outline: 0;
        }
        .x-input-error { /* Corresponds to x-input-error */
            display: block; /* To ensure it shows up below input */
            width: 100%;
            margin-top: 0.25rem;
            font-size: .75rem; /* Bootstrap .small */
            color: var(--bs-danger);
        }
        /* x-button styling will be applied via its class attribute */
    </style>
@endsection

@section('content')
    <div class="authentication-wrapper authentication-cover authentication-bg">
        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-7 p-0">
                <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                    <img src="{{ asset('assets/img/illustrations/motac-auth-professional-light.png') }}"
                        alt="{{ __('Ilustrasi Pengesahan Dua Faktor MOTAC') }}" class="img-fluid my-5 auth-illustration">
                    <img src="{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}"
                        alt="{{ __('Corak Latar Belakang Hiasan') }}" class="platform-bg">
                </div>
            </div>

            <div class="d-flex col-12 col-lg-5 align-items-center authentication-bg p-sm-5 p-4">
                <div class="w-px-400 mx-auto">
                    <div class="app-brand mb-4 d-flex justify-content-center">
                        <a href="{{ url('/') }}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                @include('_partials.macros', ['height' => 32, 'withbg' => 'fill: var(--bs-primary);'])
                            </span>
                            <span class="app-brand-text demo text-body fw-bold fs-4 ms-1">{{ __(config('app.name', 'MOTAC')) }}</span>
                        </a>
                    </div>

                    <h3 class="mb-1 fw-semibold text-center">{{ __('Pengesahan Dua Faktor') }} <i class="bi bi-shield-lock-fill ms-1"></i></h3>
                    <div x-data="{ recovery: false }">
                        <div class="mb-3 text-center text-muted small" x-show="! recovery">
                            {{ __('Sila sahkan akses ke akaun anda dengan memasukkan kod pengesahan dari aplikasi pengesah anda.') }}
                        </div>

                        <div class="mb-3 text-center text-muted small" x-show="recovery" x-cloak>
                            {{ __('Sila sahkan akses ke akaun anda dengan memasukkan salah satu kod pemulihan kecemasan anda.') }}
                        </div>

                        <x-validation-errors class="mb-3 alert alert-danger small" /> {{-- Added alert classes for styling --}}

                        <form method="POST" action="{{ route('two-factor.login') }}">
                            @csrf
                            <div class="mb-3" x-show="! recovery">
                                <x-label for="code" class="form-label" value="{{ __('Kod Pengesahan') }}" />
                                <x-input id="code" class="form-control form-control-sm {{ $errors->has('code') ? 'is-invalid' : '' }}"
                                    type="text" inputmode="numeric" name="code" autofocus x-ref="code"
                                    autocomplete="one-time-code" />
                                <x-input-error for="code" class="mt-2 small text-danger d-block" /> {{-- Added classes --}}
                            </div>

                            <div class="mb-3" x-show="recovery" x-cloak>
                                <x-label for="recovery_code" class="form-label" value="{{ __('Kod Pemulihan') }}" />
                                <x-input id="recovery_code" class="form-control form-control-sm {{ $errors->has('recovery_code') ? 'is-invalid' : '' }}"
                                    type="text" name="recovery_code" x-ref="recovery_code"
                                    autocomplete="one-time-code" />
                                <x-input-error for="recovery_code" class="mt-2 small text-danger d-block" /> {{-- Added classes --}}
                            </div>

                            <div class="d-flex justify-content-between my-3 flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" x-show="! recovery"
                                    x-on:click="recovery = true; $nextTick(() => { $refs.recovery_code.focus()})">
                                    {{ __('Guna kod pemulihan') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" x-cloak x-show="recovery"
                                    x-on:click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                                    {{ __('Guna kod pengesahan') }}
                                </button>
                                <x-button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>{{ __('Log Masuk') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
