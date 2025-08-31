{{-- resources/views/layouts/layout-blank.blade.php --}}
{{-- Minimal layout for authentication or full-width pages (no menu/sidebar).
    Filename updated from blankLayout.blade.php to layout-blank.blade.php as per new convention.
--}}

@isset($pageConfigs)
    {!! \App\Helpers\Helpers::updatePageConfig($pageConfigs) !!}
@endisset

@php
    $configData = \App\Helpers\Helpers::appClasses();
    $customizerHidden = $customizerHidden ?? ($configData['customizerHidden'] ?? true);
@endphp

@extends('layouts.commonMaster')

@section('layoutContent')
    {{-- The ID here is changed to "main-content" to match the skip link in commonMaster.blade.php --}}
    <div class="authentication-wrapper authentication-basic px-4" id="main-content">
        <div class="authentication-inner py-4">
            @yield('content')
        </div>
    </div>
@endsection
