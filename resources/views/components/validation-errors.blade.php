{{--
    resources/views/components/validation-errors.blade.php

    Component to display validation errors in a standardized way.
    Props:
    - $errors: (optional) Illuminate\Support\MessageBag or array of errors. Defaults to $errors global.

    Usage:
    <x-validation-errors />
    <x-validation-errors :errors="$errors" />
--}}
@props(['errors' => $errors])

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
        <h6 class="alert-heading mb-1 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('Ralat Pengesahan') }}
        </h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('Tutup') }}"></button>
    </div>
@endif
