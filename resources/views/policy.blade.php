@php
// $configData = \App\Helpers\Helpers::appClasses(); // If needed by blankLayout
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', __('Dasar Privasi')) {{-- Translatable title --}}

@section('page-style')
  {{-- Page Css files --}}
  {{-- Ensure this path is correct or uses a MOTAC-specific auth page style if 'page-auth.css' is from a different theme. --}}
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('content')
<div class="authentication-wrapper authentication-basic px-4">
  <div class="authentication-inner py-4" style="max-width: 800px;"> {{-- Added max-width for readability --}}
    <div class="app-brand justify-content-center mb-4"> {{-- Bootstrap justify-content-center --}}
      <a href="{{url('/')}}" class="app-brand-link gap-2">
        {{-- Replace with your MOTAC application logo component or direct image --}}
        <x-application-logo style="height: 40px; width: auto;" />
      </a>
    </div>
    <div class="card">
      <div class="card-body">
        {!! $policy !!} {{-- $policy variable is expected to contain the HTML of the privacy policy --}}
      </div>
    </div>
     <div class="text-center mt-3">
        <a href="{{ url('/') }}" class="text-secondary">{{ __('&larr; Kembali ke Laman Utama') }}</a>
    </div>
  </div>
</div>
@endsection
