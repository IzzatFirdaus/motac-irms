@php
    $customizerHidden = 'customizer-hide';
    $configData = App\Helpers\Helpers::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Sahkan Alamat E-mel'))

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}">
    <style>
        body { font-family: 'Noto Sans', sans-serif !important; line-height: 1.6; } /* [cite: 297, 301, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 589, 590, 591, 592] */
        .btn-primary { background-color: #0055A4 !important; border-color: #0055A4 !important; } /* [cite: 297, 301, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313, 314, 315, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325, 326, 327, 328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, 343, 344, 345, 346, 347, 348, 349, 350, 351, 352, 353, 354, 355, 356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 388, 389, 390, 391, 392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418, 419, 420, 421, 422, 423, 424, 425, 426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, 454, 455, 456, 457, 458, 459, 460, 461, 462, 463, 464, 465, 466, 467, 468, 469, 470, 471, 472, 473, 474, 475, 476, 477, 478, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499, 500, 501, 502, 503, 504, 505, 506, 507, 508, 509, 510, 511, 512, 513, 514, 515, 516, 517, 518, 519, 520, 521, 522, 523, 524, 525, 526, 527, 528, 529, 530, 531, 532, 533, 534, 535, 536, 537, 538, 539, 540, 541, 542, 543, 544, 545, 546, 547, 548, 549, 550, 551, 552, 553, 554, 555, 556, 557, 558, 559, 560, 561, 562, 563, 564, 565, 566, 567, 568, 569, 570, 571, 572, 573, 574, 575, 576, 577, 578, 579, 580, 581, 582, 583, 584, 585, 586, 587, 588, 589, 590, 591, 592] */
        .btn-primary:hover { background-color: #00417d !important; border-color: #00417d !important; }
        .auth-cover-bg-color { background-color: #eef3f7; }
        .card { border: 1px solid #dee2e6; } /* Added default border to card */
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
                            <button type="submit" class="btn btn-primary d-grid w-100"> {{-- Changed to btn-primary --}}
                                <i class="bi bi-send-arrow-up-fill me-1"></i>{{ __('Hantar Semula E-mel Pengesahan') }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger d-grid w-100"> {{-- Changed to btn-outline-danger --}}
                                <i class="bi bi-box-arrow-left me-1"></i>{{ __('Log Keluar') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
