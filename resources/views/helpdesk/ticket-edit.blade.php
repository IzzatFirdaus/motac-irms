{{-- resources/views/helpdesk/ticket-edit.blade.php --}}
@extends('layouts.app')

@section('title', __('Kemaskini Tiket Bantuan'))

@section('content')
<div class="container my-4">
    <h1 class="mb-4">{{ __('Kemaskini Tiket Bantuan') }}</h1>
    {{-- Place for Livewire ticket edit form --}}
    @livewire('helpdesk.ticket-edit', ['ticket' => $ticket->id])

    <div class="mt-3">
        <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="motac-btn-secondary d-inline-flex align-items-center" aria-label="{{ __('Kembali ke Butiran Tiket') }}">
            {{ __('Kembali ke Butiran Tiket') }}
        </a>
    </div>
</div>
@endsection
