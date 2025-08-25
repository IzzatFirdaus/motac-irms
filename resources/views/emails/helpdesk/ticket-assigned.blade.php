@component('mail::message')
@include('emails._partials.email-header')

# Tiket Sokongan IT Ditugaskan Kepada Anda

Salam {{ $assignedToName }},

Tiket sokongan IT **#{{ $ticket->id }} - "{{ $ticket->subject }}"** telah ditugaskan kepada anda.

**Butiran Tiket:**
* **ID Tiket:** #{{ $ticket->id }}
* **Tajuk:** {{ $ticket->subject }}
* **Kategori:** {{ $ticket->category->name ?? 'Tidak Diketahui' }}
* **Prioriti:** {{ $ticket->priority->name ?? 'Tidak Diketahui' }}
* **Status Semasa:** {{ $ticket->status }}
* **Dihantar Oleh:** {{ $ticket->user->name ?? 'Tidak Diketahui' }} ({{ $ticket->user->email ?? 'N/A' }})
* **Tarikh Dicipta:** {{ $ticket->created_at->format('d M Y H:i A') }}

Sila semak tiket ini secepat mungkin dan berikan maklum balas kepada pemohon.

@component('mail::button', ['url' => $ticketUrl])
Lihat Butiran Tiket
@endcomponent

Terima kasih,<br>
Sistem Pengurusan Sumber Bersepadu MOTAC
@endcomponent
