{{-- resources/views/livewire/helpdesk/ticket-form.blade.php --}}
<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Helpdesk Ticket') }}
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

                <form wire:submit.prevent="createTicket">
                    <div class="mb-4">
                        <x-label for="title" value="{{ __('Title') }}" />
                        <x-input id="title" class="block mt-1 w-full" type="text" wire:model.defer="title" required autofocus />
                        <x-input-error for="title" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="description" value="{{ __('Description') }}" />
                        <textarea id="description" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" wire:model.defer="description" rows="5" required></textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="category_id" value="{{ __('Category') }}" />
                        <select id="category_id" class="form-select rounded-md shadow-sm border-gray-300 mt-1 block w-full" wire:model.defer="category_id" required>
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="category_id" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="priority_id" value="{{ __('Priority') }}" />
                        <select id="priority_id" class="form-select rounded-md shadow-sm border-gray-300 mt-1 block w-full" wire:model.defer="priority_id" required>
                            <option value="">{{ __('Select Priority') }}</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="priority_id" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-label for="attachments" value="{{ __('Attachments (Max 2MB per file, JPG, PNG, PDF, DOCX, TXT, XLSX)') }}" />
                        <input type="file" id="attachments" wire:model="attachments" multiple class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"/>
                        <x-input-error for="attachments.*" class="mt-2" />
                        <div wire:loading wire:target="attachments">Uploading...</div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-button class="ml-4" wire:loading.attr="disabled">
                            {{ __('Submit Ticket') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
