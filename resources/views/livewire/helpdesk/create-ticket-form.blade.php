{{-- resources/views/livewire/helpdesk/create-ticket-form.blade.php --}}
<div>
    <h2 class="heading-medium fw-semibold text-black-900 mb-24">
        {{ __('Submit a Helpdesk Ticket') }}
    </h2

    <form wire:submit.prevent="createTicket" enctype="multipart/form-data" class="py-16">
        @if (session()->has('message'))
            <div class="alert alert-success-500 mb-24">{{ session('message') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger-500 mb-24">{{ session('error') }}</div>
        @endif

        <div class="mb-24">
            <label class="form-label fw-semibold text-black-900">{{ __('Title') }}</label>
            <input type="text" class="form-control input size-medium" wire:model.defer="title">
            @error('title') <span class="text-danger-500">{{ $message }}</span> @enderror
        </div>
        <div class="mb-24">
            <label class="form-label fw-semibold text-black-900">{{ __('Description') }}</label>
            <textarea class="form-control input size-medium" wire:model.defer="description" rows="4"></textarea>
            @error('description') <span class="text-danger-500">{{ $message }}</span> @enderror
        </div>
        <div class="row">
            <div class="col-md-6 mb-24">
                <label class="form-label fw-semibold text-black-900">{{ __('Category') }}</label>
                <select class="form-select input size-medium" wire:model.defer="category_id">
                    <option value="">{{ __('Choose...') }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('category_id') <span class="text-danger-500">{{ $message }}</span> @enderror
            </div>
            <div class="col-md-6 mb-24">
                <label class="form-label fw-semibold text-black-900">{{ __('Priority') }}</label>
                <select class="form-select input size-medium" wire:model.defer="priority_id">
                    <option value="">{{ __('Choose...') }}</option>
                    @foreach($priorities as $pri)
                        <option value="{{ $pri->id }}">{{ $pri->name }}</option>
                    @endforeach
                </select>
                @error('priority_id') <span class="text-danger-500">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="mb-24">
            <label class="form-label fw-semibold text-black-900">{{ __('Attachments (optional)') }}</label>
            <input type="file" class="form-control input size-medium" wire:model="attachments" multiple>
            @error('attachments.*') <span class="text-danger-500">{{ $message }}</span> @enderror
        </div>
    <button class="button variant-primary size-medium" type="submit">{{ __('Submit Ticket') }}</button>
    </form>
</div>
