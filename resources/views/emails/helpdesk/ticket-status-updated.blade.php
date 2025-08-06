@component('mail::message')
@include('emails._partials.email-header')

# Kemas Kini Status Tiket Sokongan IT

Salam {{ $ticket->user->name ?? 'Pengguna' }},

Status tiket sokongan IT anda **#{{ $ticket->id }} - "{{ $ticket->subject }}"** telah dikemas kini.

**Perubahan Status:**
* **Dari:** {{ $oldStatus }}
* **Kepada:** **{{ $newStatus }}**

@if($comment)
**Komen Terkini:**
@component('mail::panel')
{{ $comment }}
@endcomponent
@endif

**Butiran Tiket:**
* **ID Tiket:** #{{ $ticket->id }}
* **Tajuk:** {{ $ticket->subject }}
* **Kategori:** {{ $ticket->category->name ?? 'Tidak Diketahui' }}
* **Prioriti:** {{ $ticket->priority->name ?? 'Tidak Diketahui' }}
* **Dihantar Oleh:** {{ $ticket->user->name ?? 'Tidak Diketahui' }}
@if($ticket->assignedTo)
* **Ditugaskan Kepada:** {{ $ticket->assignedTo->name }}
@endif
* **Tarikh Dicipta:** {{ $ticket->created_at->format('d M Y H:i A') }}

Anda boleh melihat butiran penuh tiket dan sejarah komen melalui pautan di bawah:

@component('mail::button', ['url' => $ticketUrl])
Lihat Butiran Tiket
@endcomponent

Terima kasih,<br>
Sistem Pengurusan Sumber Bersepadu MOTAC
@endcomponent
