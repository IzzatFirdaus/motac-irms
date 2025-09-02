@extends('layouts.app')

@section('title', __('Ticket Details'))

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">{{ __('Ticket Details') }}</h1>
    @isset($ticket)
    @livewire('helpdesk.ticket-details', ['ticket' => $ticket])
    @else
        <p class="text-muted">{{ __('Ticket not found.') }}</p>
    @endisset
</div>
@endsection
