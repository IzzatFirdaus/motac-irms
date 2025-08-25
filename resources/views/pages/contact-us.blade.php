{{--
    Contact Us page wrapper view
    This view ensures the main application layout (navbar, sidebar, footer) is included.
    The Livewire component will render the full Contact Us form and content.
    Sets the browser tab title for proper SEO and user experience.
--}}
@extends('layouts.app')

{{-- Set the page title for the browser tab --}}
@section('title', __('contact-us.title'))

@section('content')
    @livewire('contact-us')
@endsection
