@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Akses Dihalang'))

@section('page-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection

@section('content')
<div class="container-xxl container-p-y">
  <div class="misc-wrapper text-center">
    <h2 class="mb-1 mt-4 display-5 fw-bold">{{ __('Akses Dihalang!') }} 🚫</h2>
    <p class="mb-4 mx-2">{{ __('Anda tidak mempunyai kebenaran yang mencukupi untuk mengakses sumber ini. Sila kembali ke Laman Utama.') }}</p>
    <a href="{{url('/')}}" class="btn btn-primary mb-4">{{ __('Kembali ke Laman Utama') }}</a>
    <div class="mt-4">
      {{-- Using the same illustration as 401, can be changed if a specific 403 illustration exists --}}
      <img src="{{ asset('assets/img/illustrations/not-authorized.png') }}" alt="{{ __('Ilustrasi Akses Dihalang') }}" width="200" class="img-fluid">
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="{{ __('Latar Belakang Ralat') }}" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
@endsection
