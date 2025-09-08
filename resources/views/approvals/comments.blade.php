{{-- resources/views/approvals/comments.blade.php --}}
@if (isset($approval) && $approval !== null)
    <div class="mb-3"> {{-- Bootstrap margin --}}
        <p class="h6 fw-semibold mb-2 text-dark">{{ __('Catatan Pegawai:') }}</p> {{-- Bootstrap heading and text --}}

        @if (!empty($approval->comments))
            <div class="bg-light border p-3 rounded text-dark small"> {{-- Bootstrap styled block --}}
                {{-- Using nl2br to respect line breaks from textarea, and e() for security --}}
                {!! nl2br(e($approval->comments)) !!}
            </div>
        @else
<<<<<<< HEAD
            <p class="text-muted fst-italic small mb-0">{{ __('Tiada catatan disediakan.') }}</p> {{-- Bootstrap muted and italic --}}
        @endif
    </div>
@else
    {{-- Fallback if $approval object is not passed or is null (currently commented out) --}}
    {{-- <div class="mb-3 alert alert-warning small p-2">{{ __('Data kelulusan tidak tersedia untuk paparan catatan.') }}</div> --}}
=======
            <p class="text-muted fst-italic small mb-0">{{ __('Tiada catatan disediakan.') }}</p> {{-- Bootstrap muted and italic, added mb-0 for tighter spacing if no comments --}}
        @endif
    </div>
@else
    {{-- Optional: Error message or nothing if $approval is not set --}}
    {{-- <div class="mb-3 alert alert-warning small p-2">{{ __('Data kelulusan tidak tersedia untuk catatan.') }}</div> --}}
>>>>>>> d2dd0a5 (more edited codes and new files 29/5/25)
@endif
