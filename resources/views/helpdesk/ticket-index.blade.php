{{-- resources/views/helpdesk/ticket-index.blade.php --}}
@extends('layouts.app')

@section('title', __('Senarai Tiket Bantuan'))

@section('content')
<div class="container my-4">
    <h1 class="mb-4">{{ __('Senarai Tiket Bantuan') }}</h1>
    {{-- Place to add a Livewire component for listing tickets --}}
    @livewire('helpdesk.ticket-index')

    <div class="mt-4">
        <a href="{{ route('helpdesk.tickets.create') }}" class="btn btn-primary">
            {{ __('Buat Tiket Baru') }}
        </a>
    </div>
</div>
@endsection
