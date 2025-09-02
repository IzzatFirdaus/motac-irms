@extends('layouts.app')

@section('title', __('Helpdesk Tickets'))

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">{{ __('Helpdesk Tickets') }}</h1>
    @livewire('helpdesk.my-tickets-index')
    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
</div>
@endsection
