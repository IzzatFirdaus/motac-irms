{{-- resources/views/email-applications/edit.blade.php --}}
@extends('layouts.app')

<<<<<<< HEAD
@section('title', __('Kemaskini Permohonan E-mel / ID Pengguna'))
=======
@section('title', __('Edit Permohonan E-mel ICT'))
>>>>>>> b3ca845 (code additions and edits)

@section('content')
    <div class="container py-4">
        <div class="col-lg-8 mx-auto">
<<<<<<< HEAD
            {{-- The title is already set by the Livewire component if it uses #[Title] or dispatches event --}}
            {{-- <h1 class="fs-3 fw-bold mb-4">{{ __('Kemaskini Permohonan E-mel / ID Pengguna') }}</h1> --}}

            @isset($emailApplication)
                {{-- Assuming 'resource-management.email-account.application-form' is the correct Livewire component alias --}}
                {{-- Or use the class: \App\Livewire\ResourceManagement\EmailAccount\ApplicationForm::class --}}
                @livewire('resource-management.email-account.application-form', ['emailApplicationId' => $emailApplication->id, 'isEdit' => true])
            @else
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ __('Ralat: Data permohonan e-mel tidak disediakan untuk pengemaskinian.') }}
                </div>
                 <div class="mt-3">
                    <a href="{{ route('email-applications.index') }}" class="btn btn-outline-secondary"> {{-- Use a valid fallback route --}}
                        <i class="bi bi-arrow-left"></i> {{__('Kembali ke Senarai Permohonan')}}
                    </a>
=======
            <h1 class="fs-3 fw-bold mb-4">{{ __('Edit Permohonan E-mel ICT') }}</h1>

            @isset($emailApplication)
                @livewire('email-application-form', ['emailApplication' => $emailApplication, 'isEdit' => true, 'applicationId' => $emailApplication->id])
            @else
                <div class="alert alert-danger" role="alert">
                    {{ __('Error: Email application data not provided for editing.') }}
>>>>>>> b3ca845 (code additions and edits)
                </div>
            @endisset
        </div>
    </div>
@endsection

<<<<<<< HEAD
{{-- Styles and scripts can be pushed if specific to this edit page wrapper --}}
{{--
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/email-applications-edit-wrapper.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/email-applications-edit-wrapper.js') }}"></script>
@endpush
--}}
=======
@push('styles')
    {{-- <link rel="stylesheet" href="{{ asset('css/email-applications-edit.css') }}"> --}}
@endpush

@push('scripts')
    {{-- <script src="{{ asset('js/email-applications-edit.js') }}"></script> --}}
@endpush
>>>>>>> b3ca845 (code additions and edits)
