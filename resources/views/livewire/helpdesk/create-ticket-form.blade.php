{{-- resources/views/livewire/helpdesk/create-ticket-form.blade.php --}}
<div>
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Submit a Helpdesk Ticket') }}
    </h2>

    <form wire:submit.prevent="createTicket" enctype="multipart/form-data" class="py-4">
        @if (session()->has('message'))
            <div class="alert alert-success mb-3">{{ session('message') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <label class="form-label">{{ __('Title') }}</label>
            <input type="text" class="form-control" wire:model.defer="title">
            @error('title') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Description') }}</label>
            <textarea class="form-control" wire:model.defer="description" rows="4"></textarea>
            @error('description') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('Category') }}</label>
                <select class="form-select" wire:model.defer="category_id">
                    <option value="">{{ __('Choose...') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">{{ __('Priority') }}</label>
                <select class="form-select" wire:model.defer="priority_id">
                    <option value="">{{ __('Choose...') }}</option>
                    @foreach($priorities as $pri)
                        <option value="{{ $pri->id }}">{{ $pri->name }}</option>
                    @endforeach
                </select>
                @error('priority_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('Attachments (optional)') }}</label>
            <input type="file" class="form-control" wire:model="attachments" multiple>
            @error('attachments.*') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button class="btn btn-primary" type="submit">{{ __('Submit Ticket') }}</button>
    </form>
</div>
