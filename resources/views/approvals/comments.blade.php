{{-- resources/views/approvals/comments.blade.php --}}
@if (isset($approval) && $approval !== null)
    <div class="mb-3">
        <p class="h6 fw-semibold mb-2 text-dark">{{ __('Catatan Pegawai:') }}</p>
        @if (!empty($approval->comments))
            <div class="bg-light border p-3 rounded text-dark small motac-card">
                {{-- Display comments with line breaks preserved --}}
                {!! nl2br(e($approval->comments)) !!}
            </div>
        @else
            <p class="text-muted fst-italic small mb-0">{{ __('Tiada catatan disediakan.') }}</p>
        @endif
    </div>
@endif
