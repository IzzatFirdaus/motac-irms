{{-- resources/views/helpdesk/ticket-create.blade.php --}}
@extends('layouts.app')

@section('title', __('Buat Tiket Bantuan'))

@section('content')
<div class="container my-4">
    <h1 class="mb-4">{{ __('Buat Tiket Bantuan') }}</h1>
    {{-- Place to add a Livewire component for the ticket creation form --}}
    @livewire('helpdesk.ticket-create')
    <div class="mt-3">
        <a href="{{ route('helpdesk.tickets.index') }}" class="btn btn-secondary">
            {{ __('Kembali ke Senarai Tiket') }}
        </a>
    </div>
</div>
@endsection
