@component('mail::message')
@include('emails._partials.email-header')

# Tiket Sokongan IT Baru Anda Telah Dicipta

Salam {{ $ticketCreatorName }},

Tiket sokongan IT anda **#{{ $ticket->id }} - "{{ $ticket->subject }}"** telah berjaya dicipta.

Kami akan menyemak permohonan anda dan memberikan maklum balas secepat mungkin.

**Butiran Tiket Anda:**
* **ID Tiket:** #{{ $ticket->id }}
* **Tajuk:** {{ $ticket->subject }}
* **Penerangan:**
@component('mail::panel')
{{ $ticket->description }}
@endcomponent
* **Kategori:** {{ $ticket->category->name ?? 'Tidak Diketahui' }}
* **Prioriti:** {{ $ticket->priority->name ?? 'Tidak Diketahui' }}
* **Status:** {{ $ticket->status }}
* **Tarikh Dicipta:** {{ $ticket->created_at->format('d M Y H:i A') }}

@if($ticket->assignedTo)
Tiket ini telah ditugaskan kepada {{ $ticket->assignedTo->name }} untuk tindakan lanjut.
@endif

Anda boleh melihat status dan sebarang kemas kini untuk tiket ini melalui pautan di bawah:

@component('mail::button', ['url' => $ticketUrl])
Lihat Tiket Anda
@endcomponent

Terima kasih kerana menghubungi kami.<br>
Sistem Pengurusan Sumber Bersepadu MOTAC
@endcomponent
