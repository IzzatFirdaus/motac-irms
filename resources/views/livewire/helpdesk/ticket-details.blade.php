{{-- resources/views/livewire/helpdesk/ticket-details.blade.php --}}
<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Ticket Details') }} #{{ $ticket->id }}
    </h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5>{{ $ticket->title }}</h5>
            <p><strong>{{ __('Status:') }}</strong> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p>
            <p><strong>{{ __('Category:') }}</strong> {{ $ticket->category->name ?? '-' }}</p>
            <p><strong>{{ __('Priority:') }}</strong> {{ $ticket->priority->name ?? '-' }}</p>
            <p><strong>{{ __('Description:') }}</strong> {{ $ticket->description }}</p>
            <p><strong>{{ __('Submitted by:') }}</strong> {{ $ticket->user->name ?? '-' }}</p>
            <p><strong>{{ __('Created at:') }}</strong> {{ $ticket->created_at->format('d M Y H:i') }}</p>
        </div>
    </div>

    {{-- Comments Section --}}
    <div class="mb-3">
        <h5>{{ __('Comments') }}</h5>
        <ul class="list-group">
            @forelse($ticket->comments as $comment)
                <li class="list-group-item">
                    <strong>{{ $comment->user->name ?? 'N/A' }}</strong>:
                    {{ $comment->content }}
                    <span class="text-muted small float-end">{{ $comment->created_at->diffForHumans() }}</span>
                </li>
            @empty
                <li class="list-group-item text-muted">{{ __('No comments yet.') }}</li>
            @endforelse
        </ul>
    </div>

    {{-- Add Comment Form --}}
    <form wire:submit.prevent="addComment" class="mb-4">
        <div class="mb-3">
            <label class="form-label">{{ __('Add Comment') }}</label>
            <textarea class="form-control" wire:model.defer="newComment" rows="3"></textarea>
            @error('newComment') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Attachments (optional)') }}</label>
            <input type="file" class="form-control" wire:model="commentAttachments" multiple>
            @error('commentAttachments.*') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        @if(auth()->user()->hasRole('IT Admin'))
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" wire:model.defer="isInternalComment" id="isInternalComment">
                <label class="form-check-label" for="isInternalComment">
                    {{ __('Internal Note (Visible to IT Admin Only)') }}
                </label>
            </div>
        @endif
        <button class="btn btn-primary" type="submit">{{ __('Submit Comment') }}</button>
    </form>
</div>
