@extends('layouts.app') {{-- Changed to layouts.app assuming this is your MOTAC themed Bootstrap 5 master layout --}}

@php
    // Assuming breadcrumbs are handled by a component or section in layouts.app
    // If not, you might define them in the Livewire component or here.
    // For example:
    // $breadcrumbs = [
    //     ['link' => route('dashboard'), 'name' => __('Papan Pemuka')], // Assuming 'dashboard' is your home route
    //     ['name' => __('Pengurusan Token API')]
    // ];
@endphp

@section('title', __('Pengurusan Token API'))


@section('page-style')
    {{-- Page CSS files --}}
    {{-- <link rel="stylesheet" href="{{ asset(mix('assets/vendor/css/pages/page-auth.css')) }}"> --}}
    {{-- The above page-auth.css might not be needed if your global MOTAC theme handles form/card styling.
       If Jetstream specific UI elements are kept and need it, then it's fine.
       However, the goal is to make it look like the rest of your MOTAC Bootstrap system.
   --}}
    <style>
        /* Minimal styles to ensure Jetstream components fit within a Bootstrap page if not fully themed */
        /* This is a fallback. Ideally, Jetstream components are fully themed or replaced. */
        .x-form-section,
        .x-action-section {
            /* Mimic Bootstrap card */
            background-color: var(--bs-card-bg, #fff);
            border: 1px solid var(--bs-card-border-color, rgba(0, 0, 0, .175));
            border-radius: var(--bs-card-border-radius, .375rem);
            box-shadow: var(--bs-box-shadow-sm, 0 .125rem .25rem rgba(0, 0, 0, .075));
            margin-bottom: 1.5rem;
        }

        /* Further styling would be needed for title, description, content, actions slots */
    </style>
@endsection

@section('content')
    <div class="container py-4"> {{-- Added Bootstrap container for consistent padding --}}
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9"> {{-- Content width control --}}
                {{-- Breadcrumbs could be rendered here if defined --}}
                {{-- @include('partials.breadcrumbs', ['breadcrumbs' => $breadcrumbs]) --}}

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold text-dark mb-0">{{ __('Pengurusan Token API') }}</h1>
                </div>

                @livewire('api.api-token-manager')
            </div>
        </div>
    </div>
@endsection
