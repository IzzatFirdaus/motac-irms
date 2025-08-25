{{-- resources/views/livewire/helpdesk/admin/ticket-management.blade.php --}}
<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Helpdesk Ticket Management') }}
    </h2>

    <div class="py-6">
        <div class="container mx-auto px-4">
            {{-- Session Alerts --}}
            @if (session()->has('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
            @endif

            {{-- Filter/Search Bar --}}
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <input type="text" class="form-control"
                        wire:model.debounce.300ms="search"
                        placeholder="{{ __('Search tickets...') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <select class="form-select" wire:model="status">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="open">{{ __('Open') }}</option>
                        <option value="in_progress">{{ __('In Progress') }}</option>
                        <option value="on_hold">{{ __('On Hold') }}</option>
                        <option value="resolved">{{ __('Resolved') }}</option>
                        <option value="closed">{{ __('Closed') }}</option>
                        <option value="reopened">{{ __('Reopened') }}</option>
                        <option value="pending_user_feedback">{{ __('Pending User Feedback') }}</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select class="form-select" wire:model="category_id">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select class="form-select" wire:model="priority_id">
                        <option value="">{{ __('All Priorities') }}</option>
                        @foreach($this->priorities as $priority)
                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select" wire:model="assigned_to_user_id">
                        <option value="">{{ __('All Staff') }}</option>
                        @foreach($this->staffUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tickets Table --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr class="table-light">
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Applicant') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Priority') }}</th>
                            <th>{{ __('Assigned To') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>
                                    <a href="javascript:void(0);" wire:click="viewTicketDetails({{ $ticket->id }})" class="text-primary">
                                        {{ $ticket->title }}
                                    </a>
                                </td>
                                <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $ticket->priority->name ?? '-' }}</span>
                                </td>
                                <td>{{ $ticket->assignedTo->name ?? __('Unassigned') }}</td>
                                <td>{{ $ticket->created_at->format('d M Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" wire:click="openAssignTicketModal({{ $ticket->id }})">{{ __('Assign') }}</button>
                                    <button class="btn btn-sm btn-outline-success" wire:click="openChangeStatusModal({{ $ticket->id }})">{{ __('Change Status') }}</button>
                                    <button class="btn btn-sm btn-outline-secondary" wire:click="openAddCommentModal({{ $ticket->id }})">{{ __('Add Comment') }}</button>
                                    <button class="btn btn-sm btn-outline-danger" wire:click="openCloseTicketModal({{ $ticket->id }})">{{ __('Close') }}</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">{{ __('No tickets found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $this->tickets->links() }}
            </div>

            {{-- Modals/Drawers for actions could be included here as partials/components --}}
            {{-- ... --}}
        </div>
    </div>
</div>
