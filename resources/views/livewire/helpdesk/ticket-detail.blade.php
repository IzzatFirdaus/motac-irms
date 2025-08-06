{{-- resources/views/livewire/helpdesk/ticket-detail.blade.php --}}
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Helpdesk Ticket #') }}{{ $ticket->id }} - {{ $ticket->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session()->has('message'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('message') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Ticket Details') }}</h3>
                        <p><strong>{{ __('Title:') }}</strong> {{ $ticket->title }}</p>
                        <p><strong>{{ __('Description:') }}</strong> {{ $ticket->description }}</p>
                        <p><strong>{{ __('Category:') }}</strong> {{ $ticket->category->name ?? 'N/A' }}</p>
                        <p><strong>{{ __('Status:') }}</strong>
                            <span class="badge @if($ticket->status == 'open') bg-success @elseif($ticket->status == 'in_progress') bg-info @elseif($ticket->status == 'pending_user_feedback') bg-warning @else bg-secondary @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </p>
                        <p><strong>{{ __('Priority:') }}</strong>
                            @if($ticket->priority)
                                <span style="color: {{ $ticket->priority->color_code ?? 'inherit' }}; font-weight: bold;">
                                    {{ $ticket->priority->name }}
                                </span>
                            @else
                                N/A
                            @endif
                        </p>
                        <p><strong>{{ __('Requested By:') }}</strong> {{ $ticket->user->name ?? 'N/A' }}</p>
                        <p><strong>{{ __('Assigned To:') }}</strong> {{ $ticket->assignedTo->name ?? 'Unassigned' }}</p>
                        <p><strong>{{ __('Created At:') }}</strong> {{ $ticket->created_at->format('d M Y H:i') }}</p>
                        @if ($ticket->due_date)
                            <p><strong>{{ __('Due Date:') }}</strong> {{ \Carbon\Carbon::parse($ticket->due_date)->format('d M Y H:i') }}</p>
                        @endif
                        @if ($ticket->resolution_notes)
                            <p><strong>{{ __('Resolution Notes:') }}</strong> {{ $ticket->resolution_notes }}</p>
                        @endif
                        @if ($ticket->closed_at)
                            <p><strong>{{ __('Closed At:') }}</strong> {{ \Carbon\Carbon::parse($ticket->closed_at)->format('d M Y H:i') }}</p>
                        @endif

                        <h4 class="text-md font-semibold mt-4 mb-2">{{ __('Attachments') }}</h4>
                        @forelse ($ticket->attachments as $attachment)
                            <p class="text-sm">
                                <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                    <i class="bi bi-file-earmark-fill"></i> {{ $attachment->file_name }}
                                </a>
                            </p>
                        @empty
                            <p class="text-sm text-gray-500">{{ __('No attachments.') }}</p>
                        @endforelse
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Comments') }}</h3>
                        @forelse ($ticket->comments->sortBy('created_at') as $comment)
                            <div class="border-b border-gray-200 pb-4 mb-4 {{ $comment->is_internal ? 'bg-gray-100 p-3 rounded' : '' }}">
                                <p class="text-sm text-gray-800">
                                    <strong>{{ $comment->user->name ?? 'System' }}</strong>
                                    <span class="text-gray-500 text-xs ml-2">{{ $comment->created_at->format('d M Y H:i') }}</span>
                                    @if ($comment->is_internal)
                                        <span class="text-red-500 text-xs ml-2">({{ __('Internal Note') }})</span>
                                    @endif
                                </p>
                                <p class="text-sm mt-1">{{ $comment->comment }}</p>
                                @if ($comment->attachments->count())
                                    <h5 class="text-xs font-semibold mt-2">{{ __('Comment Attachments:') }}</h5>
                                    @foreach ($comment->attachments as $attachment)
                                        <p class="text-xs">
                                            <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-blue-500 hover:underline">
                                                <i class="bi bi-paperclip"></i> {{ $attachment->file_name }}
                                            </a>
                                        </p>
                                    @endforeach
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500">{{ __('No comments yet.') }}</p>
                        @endforelse

                        @can('addComment', $ticket)
                            <div class="mt-6">
                                <h4 class="text-md font-semibold mb-2">{{ __('Add a Comment') }}</h4>
                                <form wire:submit.prevent="addComment">
                                    <div class="mb-4">
                                        <textarea wire:model.defer="newComment" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="{{ __('Type your comment here...') }}" required></textarea>
                                        <x-input-error for="newComment" class="mt-2" />
                                    </div>

                                    <div class="mb-4">
                                        <x-label for="comment_attachments" value="{{ __('Attachments (Max 2MB per file)') }}" />
                                        <input type="file" id="comment_attachments" wire:model="commentAttachments" multiple class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"/>
                                        <x-input-error for="commentAttachments.*" class="mt-2" />
                                        <div wire:loading wire:target="commentAttachments">Uploading...</div>
                                    </div>

                                    @if (auth()->user()->hasRole('IT Admin'))
                                        <div class="mb-4 flex items-center">
                                            <x-checkbox id="is_internal_comment" wire:model.defer="isInternalComment" />
                                            <x-label for="is_internal_comment" class="ml-2">{{ __('Internal Note (Only visible to IT Staff)') }}</x-label>
                                        </div>
                                    @endif

                                    <x-button wire:loading.attr="disabled">
                                        {{ __('Add Comment') }}
                                    </x-button>
                                </form>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
