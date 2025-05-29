@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Dalam Selenggaraan'))

@section('page-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection

@section('content')
<div class="container-xxl container-p-y"> {{-- Added container-p-y for padding --}}
  <div class="misc-wrapper text-center">
    <h2 class="mb-2 display-5 fw-bold">{{ __('Dalam Selenggaraan!') }}</h2>
    <p class="mb-4 mx-2">
      {{ __('Perhatian! Halaman ini dijadualkan untuk kemaskini tidak lama lagi.') }}<br>
      {{ __('Sila cuba sebentar lagi.') }}
    </p>
    {{-- Timer can be kept if the maintenance duration is fixed and communicated
    <h3 id="timer" class="mb-4">00:15:00:0</h3>
    --}}
    @auth {{-- Show logout only if user is somehow authenticated on this page --}}
    <form method="POST" id="logout-form" action="{{ route('logout') }}" class="d-inline">
      @csrf
      <button class="btn btn-outline-secondary mt-3" type="submit">
        {{ __('Log Keluar') }}
      </button>
    </form>
    @endauth
    <a href="{{url('/')}}" class="btn btn-primary mt-3">{{ __('Pergi ke Laman Utama') }}</a>
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" alt="{{ __('Ilustrasi Dalam Selenggaraan') }}" width="350" class="img-fluid">
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper misc-under-maintenance-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="{{ __('Latar Belakang Selenggaraan') }}" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
@endsection

{{--
@push('custom-scripts')
  <script>
    // The countdown script is fine if needed, but often maintenance pages are static.
    // If used, ensure totalMilliseconds is set appropriately or passed from backend.
    // let totalMilliseconds = 900000; // Example: 15 minutes
    // const timerElement = document.getElementById('timer');
    // if (timerElement) {
    //   const countdown = setInterval(() => {
    //       // ... (countdown logic from original file) ...
    //       timerElement.textContent = `${hours}:${minutes}:${seconds}`; // Simplified display
    //       // ...
    //   }, 1000); // Update every second
    // }
</script>
@endpush
--}}
