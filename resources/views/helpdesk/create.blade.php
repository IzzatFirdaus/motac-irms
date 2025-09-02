@extends('layouts.app')

@section('title', __('Create Helpdesk Ticket'))

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">{{ __('Create Helpdesk Ticket') }}</h1>
    @livewire('helpdesk.create-ticket-form')
</div>
@endsection
