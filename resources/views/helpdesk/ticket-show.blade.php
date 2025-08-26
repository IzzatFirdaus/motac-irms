{{-- resources/views/helpdesk/ticket-show.blade.php --}}
@extends('layouts.app')

@section('title', __('Butiran Tiket Bantuan'))

@section('content')
<div class="container my-4">
    <h1 class="mb-4">{{ __('Butiran Tiket Bantuan') }}</h1>

    {{-- Ticket summary --}}
    <div class="motac-card mb-3">
        <div class="motac-card-header">
            <strong>{{ $ticket->title }}</strong>
            <span class="motac-badge motac-badge-info float-end" role="status" aria-label="{{ __($ticket->status) }}">{{ __($ticket->status) }}</span>
        </div>
        <div class="motac-card-body">
            <p><strong>{{ __('Kategori') }}:</strong> {{ $ticket->category->name ?? '-' }}</p>
            <p><strong>{{ __('Keutamaan') }}:</strong> {{ $ticket->priority->name ?? '-' }}</p>
            <p><strong>{{ __('Dihantar oleh') }}:</strong> {{ $ticket->applicant->name ?? '-' }}</p>
            <p><strong>{{ __('Ditugaskan kepada') }}:</strong> {{ $ticket->assignedTo->name ?? '-' }}</p>
            <p><strong>{{ __('Tarikh Hantar') }}:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
            <p><strong>{{ __('Status') }}:</strong> {{ __($ticket->status) }}</p>
            <p><strong>{{ __('Deskripsi') }}:</strong><br>{{ $ticket->description }}</p>
            @if($ticket->resolution_notes)
            <p><strong>{{ __('Catatan Penyelesaian') }}:</strong><br>{{ $ticket->resolution_notes }}</p>
            @endif
            @if($ticket->attachments && $ticket->attachments->count())
                <div>
                    <strong>{{ __('Lampiran') }}:</strong>
                    <ul>
                        @foreach($ticket->attachments as $attachment)
                            <li>
                                <a href="{{ route('helpdesk.attachments.download', $attachment) }}" target="_blank">
                                    {{ $attachment->file_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- Comments Section (optional, can be replaced with Livewire) --}}
    <div class="mb-3">
        <h4>{{ __('Komen') }}</h4>
        @if($ticket->comments && $ticket->comments->count())
            <ul class="list-group mb-3">
                @foreach($ticket->comments as $comment)
                    <li class="list-group-item">
                        <strong>{{ $comment->user->name ?? '-' }}</strong>
                        <span class="text-muted float-end">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        <div>{{ $comment->content }}</div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-muted">{{ __('Tiada komen.') }}</p>
        @endif
    </div>

    {{-- Place for Livewire comment form or reply --}}
    @livewire('helpdesk.ticket-comment-form', ['ticket' => $ticket->id])

    <div class="mt-3">
        <a href="{{ route('helpdesk.tickets.index') }}" class="motac-btn-secondary d-inline-flex align-items-center" aria-label="{{ __('Kembali ke Senarai Tiket') }}">
            {{ __('Kembali ke Senarai Tiket') }}
        </a>
        @can('update', $ticket)
            <a href="{{ route('helpdesk.tickets.edit', $ticket) }}" class="motac-btn-primary d-inline-flex align-items-center" aria-label="{{ __('Kemaskini') }}">
                {{ __('Kemaskini') }}
            </a>
        @endcan
        @can('delete', $ticket)
            <form action="{{ route('helpdesk.tickets.destroy', $ticket) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Padam tiket ini?') }}')">
                @csrf
                @method('DELETE')
                <button class="motac-btn-danger d-inline-flex align-items-center" aria-label="{{ __('Padam') }}">{{ __('Padam') }}</button>
            </form>
        @endcan
    </div>
</div>
@endsection
