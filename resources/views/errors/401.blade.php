@php
  $customizerHidden = 'customizer-hide'; // Variable from the original template
  $configData = Helper::appClasses();   // Variable from the original template
@endphp

@extends('layouts/blankLayout') {{-- Assuming layouts/blankLayout.blade.php is your blank layout for error pages --}}

@section('title', __('Tidak Dibenarkan'))

@section('page-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection

@section('content')
<div class="container-xxl container-p-y">
  <div class="misc-wrapper text-center">
    <h2 class="mb-1 mt-4 display-5 fw-bold">{{ __('Anda Tidak Dibenarkan!') }} 🔐</h2>
    <p class="mb-4 mx-2">{{ __('Anda tidak mempunyai kebenaran untuk mengakses halaman ini. Sila kembali ke Laman Utama!') }}</p>
    <a href="{{url('/')}}" class="btn btn-primary mb-4">{{ __('Kembali ke Laman Utama') }}</a>
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/not-authorized.png') }}" alt="{{ __('Ilustrasi Tidak Dibenarkan') }}" width="200" class="img-fluid"> {{-- Adjusted width for consistency --}}
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="{{ __('Latar Belakang Ralat') }}" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
@endsection
