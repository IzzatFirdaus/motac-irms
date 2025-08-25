{{-- resources/views/livewire/helpdesk/my-tickets-index.blade.php --}}
<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('My Helpdesk Tickets') }}
    </h2>

    {{-- Filter Bar --}}
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <input type="text" class="form-control"
                wire:model.debounce.300ms="search"
                placeholder="{{ __('Search your tickets...') }}">
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" wire:model="status">
                <option value="">{{ __('All Statuses') }}</option>
                <option value="open">{{ __('Open') }}</option>
                <option value="in_progress">{{ __('In Progress') }}</option>
                <option value="resolved">{{ __('Resolved') }}</option>
                <option value="closed">{{ __('Closed') }}</option>
                <option value="pending_user_feedback">{{ __('Pending User Feedback') }}</option>
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" wire:model="priority">
                <option value="">{{ __('All Priorities') }}</option>
                @foreach($priorities as $priority)
                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <select class="form-select" wire:model="category">
                <option value="">{{ __('All Categories') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Priority') }}</th>
                    <th>{{ __('Assigned To') }}</th>
                    <th>{{ __('Created') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->id }}</td>
                        <td>
                            <a href="{{ route('helpdesk.view', $ticket->id) }}" class="text-primary">{{ $ticket->title }}</a>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $ticket->priority->name ?? '-' }}</span>
                        </td>
                        <td>{{ $ticket->assignedTo->name ?? __('Unassigned') }}</td>
                        <td>{{ $ticket->created_at->format('d M Y') }}</td>
                        <td>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('helpdesk.view', $ticket->id) }}">{{ __('View') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">{{ __('No tickets found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $tickets->links() }}
    </div>
</div>
