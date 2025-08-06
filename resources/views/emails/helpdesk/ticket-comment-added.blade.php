@component('mail::message')
@include('emails._partials.email-header')

# Komen Baru Ditambah pada Tiket Sokongan IT

Salam,

Komen baru telah ditambah pada tiket sokongan IT **#{{ $ticket->id }} - "{{ $ticket->subject }}"**.

**Komen Ditambah Oleh:** {{ $commenterName }}

---

**Komen:**
@component('mail::panel')
{{ $comment->comment }}
@endcomponent

---

**Butiran Tiket:**
* **ID Tiket:** #{{ $ticket->id }}
* **Tajuk:** {{ $ticket->subject }}
* **Status Semasa:** {{ $ticket->status }}
* **Dihantar Oleh:** {{ $ticket->user->name ?? 'Tidak Diketahui' }}
@if($ticket->assignedTo)
* **Ditugaskan Kepada:** {{ $ticket->assignedTo->name }}
@endif
* **Tarikh Dicipta:** {{ $ticket->created_at->format('d M Y H:i A') }}

@component('mail::button', ['url' => $ticketUrl])
Lihat Tiket & Balas Komen
@endcomponent

Terima kasih,<br>
Sistem Pengurusan Sumber Berpadu MOTAC
@endcomponent
