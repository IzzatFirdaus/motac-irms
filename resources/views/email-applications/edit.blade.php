{{-- resources/views/email-applications/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Edit Permohonan E-mel ICT'))

@section('content')
    <div class="container py-4">
        <div class="col-lg-8 mx-auto">
            <h1 class="fs-3 fw-bold mb-4">{{ __('Edit Permohonan E-mel ICT') }}</h1>

            @isset($emailApplication)
                @livewire('email-application-form', ['emailApplication' => $emailApplication, 'isEdit' => true, 'applicationId' => $emailApplication->id])
            @else
                <div class="alert alert-danger" role="alert">
                    {{ __('Error: Email application data not provided for editing.') }}
                </div>
            @endisset
        </div>
    </div>
@endsection

@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('css/email-applications-edit.css') }}"> --}}
@endpush

@push('scripts')
    {{-- <script src="{{ asset('js/email-applications-edit.js') }}"></script> --}}
@endpush
