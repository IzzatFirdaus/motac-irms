<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Helpdesk Ticket Management') }}
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

                <div class="mb-4 flex items-center space-x-4">
                    <x-input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search tickets...') }}" class="w-full md:w-1/3"/>
                    <select wire:model.live="statusFilter" class="form-select rounded-md shadow-sm border-gray-300">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="open">{{ __('Open') }}</option>
                        <option value="in_progress">{{ __('In Progress') }}</option>
                        <option value="resolved">{{ __('Resolved') }}</option>
                        <option value="closed">{{ __('Closed') }}</option>
                    </select>
                    <select wire:model.live="priorityFilter" class="form-select rounded-md shadow-sm border-gray-300">
                        <option value="">{{ __('All Priorities') }}</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="categoryFilter" class="form-select rounded-md shadow-sm border-gray-300">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="assignedToFilter" class="form-select rounded-md shadow-sm border-gray-300">
                        <option value="">{{ __('All Agents') }}</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ID') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Title') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Applicant') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Priority') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Assigned To') }}</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Created At') }}</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($tickets as $ticket)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $ticket->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('helpdesk.view', $ticket->id) }}" class="text-indigo-600 hover:text-indigo-900">{{ $ticket->title }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ticket->applicant->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ticket->status_color }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                        @if ($ticket->is_overdue)
                                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                {{ __('OVERDUE') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $ticket->priority_color }}">
                                            {{ $ticket->priority->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ticket->assignedTo->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $ticket->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('helpdesk.view', $ticket->id) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                        @can('update', $ticket)
                                            <button wire:click="openEditModal({{ $ticket->id }})" class="ml-2 text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</button>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ __('No tickets found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $tickets->links() }}
                </div>

                {{-- Edit Ticket Modal --}}
                <x-dialog-modal wire:model="ticketIdToEdit">
                    <x-slot name="title">
                        {{ __('Edit Ticket') }} #{{ $ticketIdToEdit }}
                    </x-slot>

                    <x-slot name="content">
                        <form wire:submit.prevent="updateTicket">
                            <div class="mb-4">
                                <x-label for="editStatus" value="{{ __('Status') }}" />
                                <select id="editStatus" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="editStatus">
                                    <option value="open">{{ __('Open') }}</option>
                                    <option value="in_progress">{{ __('In Progress') }}</option>
                                    <option value="resolved">{{ __('Resolved') }}</option>
                                    <option value="closed">{{ __('Closed') }}</option>
                                </select>
                                <x-input-error for="editStatus" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-label for="editAssignedTo" value="{{ __('Assign To') }}" />
                                <select id="editAssignedTo" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="editAssignedTo">
                                    <option value="">{{ __('Unassigned') }}</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="editAssignedTo" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <x-label for="editResolutionNotes" value="{{ __('Resolution Notes (for Resolved/Closed)') }}" />
                                <textarea id="editResolutionNotes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="editResolutionNotes" rows="3"></textarea>
                                <x-input-error for="editResolutionNotes" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-secondary-button wire:click="$set('ticketIdToEdit', null)" wire:loading.attr="disabled">
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                                <x-button class="ml-2" type="submit" wire:loading.attr="disabled">
                                    {{ __('Update') }}
                                </x-button>
                            </div>
                        </form>
                    </x-slot>
                </x-dialog-modal>
            </div>
        </div>
    </div>
</div>
