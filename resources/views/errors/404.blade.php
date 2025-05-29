@php
  $customizerHidden = 'customizer-hide';
  $configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', __('Halaman Tidak Ditemui'))

@section('page-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-misc.css')}}">
@endsection

@section('content')
<div class="container-xxl container-p-y">
  <div class="misc-wrapper text-center">
    <h2 class="mb-1 mt-4 display-5 fw-bold">{{ __('Halaman Tidak Ditemui!') }}</h2>
    <p class="mb-4 mx-2">{{ __('Oops! 😖 URL yang diminta tidak ditemui di pelayan ini.') }}</p>
    <a href="{{url('/')}}" class="btn btn-primary mb-4">{{ __('Kembali ke Laman Utama') }}</a>
    <div class="mt-4">
      <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" alt="{{ __('Ilustrasi Halaman Tidak Ditemui') }}" width="200" class="img-fluid"> {{-- Adjusted width --}}
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-'.$configData['style'].'.png') }}" alt="{{ __('Latar Belakang Ralat') }}" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png">
</div>
@endsection
