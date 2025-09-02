{{-- Edit Helpdesk Ticket container view; renders Livewire edit form --}}
<div class="container mx-auto p-4">
    @if(isset($ticket))
        @livewire('helpdesk.edit-ticket-form', ['ticketId' => $ticket->id])
    @else
        <div class="text-red-600">Ticket not found.</div>
    @endif
    
    <div class="mt-4">
        <a href="{{ route('helpdesk.tickets.index') }}" class="text-blue-600 hover:underline">&larr; Kembali</a>
    </div>
</div>
