@extends('layouts.app') {{-- Or your main layout --}}

@section('title', __('Return Equipment')) {{-- Or dynamically include transaction/equipment details --}}

@section('content')
    <div class="container">
        <h2>{{ __('Return Equipment') }}</h2>

        {{-- Display Loan Transaction Details --}}
        <div class="card mb-4">
            <div class="card-header">{{ __('Transaction Details') }}</div>
            <div class="card-body">
                <p><strong>{{ __('Equipment:') }}</strong> {{ $loanTransaction->equipment->tag_id }} -
                    {{ $loanTransaction->equipment->brand }} {{ $loanTransaction->equipment->model }}</p>
                <p><strong>{{ __('Loan Application:') }}</strong> <a
                        href="{{ route('resource-management.loan-applications.show', $loanTransaction->loanApplication) }}">{{ __('Application #:app_id', ['app_id' => $loanTransaction->loanApplication->id]) }}</a>
                </p>
                <p><strong>{{ __('Issued To:') }}</strong>
                    {{ $loanTransaction->loanApplication->user->full_name ?? ($loanTransaction->loanApplication->user->name ?? 'N/A') }}
                </p>
                @if ($loanTransaction->receivingOfficer)
                    <p><strong>{{ __('Received By (on issue):') }}</strong> {{-- Clarified label --}}
                        {{ $loanTransaction->receivingOfficer->full_name ?? ($loanTransaction->receivingOfficer->name ?? 'N/A') }}
                    </p>
                @endif
                <p><strong>{{ __('Issued By:') }}</strong>
                    {{ $loanTransaction->issuingOfficer->full_name ?? ($loanTransaction->issuingOfficer->name ?? 'N/A') }}
                </p>
                <p><strong>{{ __('Issue Timestamp:') }}</strong> {{ $loanTransaction->issue_timestamp }}</p>

                {{-- Display accessories issued checklist for reference --}}
                @if (
                    $loanTransaction->accessories_checklist_on_issue &&
                        count((array) $loanTransaction->accessories_checklist_on_issue) > 0)
                    <div class="mb-3">
                        <strong>{{ __('Accessories Issued Checklist:') }}</strong>
                        <ul>
                            @foreach ((array) $loanTransaction->accessories_checklist_on_issue as $accessory)
                                <li>{{ __($accessory) }}</li> {{-- Added translation helper --}}
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="mb-3">
                        <strong>{{ __('Accessories Issued Checklist:') }}</strong>
                        <p>{{ __('No accessories were recorded on issue.') }}</p>
                    </div>
                @endif
                {{-- Add other relevant details --}}
            </div>
        </div>

        {{-- Return Form --}}
        <div class="card mb-4">
            <div class="card-header">{{ __('Process Return') }}</div>
            <div class="card-body">
                <form action="{{ route('resource-management.loan-transactions.return', $loanTransaction) }}"
                    method="POST">
                    @csrf
                    {{-- POST is default, no need for @method('POST') if route is POST --}}

                    {{-- Returning Officer (Optional, defaults to Applicant or original receiver) --}}
                    <div class="mb-3">
                        <label for="returning_officer_id"
                            class="form-label">{{ __('Returning Officer (Optional - if different from original applicant/receiver)') }}</label>
                        <select class="form-select" id="returning_officer_id" name="returning_officer_id">
                            <option value="">{{ __('Original Applicant / Receiver') }}</option>
                            {{-- Changed placeholder text for clarity --}}
                            {{-- Example: Loop through potential returning officers passed from controller --}}
                            {{-- @foreach ($potentialReturningOfficers as $officer) --}}
                            {{-- <option value="{{ $officer->id }}" {{ old('returning_officer_id') == $officer->id ? 'selected' : '' }}>{{ $officer->full_name ?? $officer->name }}</option> --}}
                            {{-- @endforeach --}}
                            <option value="4" {{ old('returning_officer_id') == '4' ? 'selected' : '' }}>Another
                                Officer (Test)</option> {{-- Example value --}}
                        </select>
                        @error('returning_officer_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Return Accepting Officer (Defaults to logged-in BPM Staff/Admin) --}}
                    <div class="mb-3">
                        <label for="return_accepting_officer_id"
                            class="form-label">{{ __('Return Accepting Officer') }}</label>
                        <select class="form-select" id="return_accepting_officer_id" name="return_accepting_officer_id"
                            required>
                            {{-- Example: Loop through authorized accepting officers passed from controller --}}
                            {{-- @foreach ($acceptingOfficers as $officer) --}}
                            {{-- <option value="{{ $officer->id }}" {{ (old('return_accepting_officer_id', Auth::id()) == $officer->id) ? 'selected' : '' }}>{{ $officer->full_name ?? $officer->name }}</option> --}}
                            {{-- @endforeach --}}
                            {{-- Default to authenticated user if no other logic/list is provided --}}
                            <option value="{{ Auth::id() }}" selected>
                                {{ Auth::user()->full_name ?? Auth::user()->name }} (Current User)
                            </option>
                        </select>
                        @error('return_accepting_officer_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Return Timestamp (Defaults to current time, might be editable) --}}
                    <div class="mb-3">
                        <label for="return_timestamp" class="form-label">{{ __('Return Timestamp') }}</label>
                        <input type="datetime-local" class="form-control @error('return_timestamp') is-invalid @enderror"
                            id="return_timestamp" name="return_timestamp"
                            value="{{ old('return_timestamp', now()->format('Y-m-d\TH:i')) }}" required>
                        @error('return_timestamp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Accessories Checklist on Return --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('Accessories Returned Checklist') }}</label>
                        @php
                            // Define a fallback in case the config is not set or not accessible directly
                            $defaultAccessories = [
                                'Charger',
                                'Bag',
                                'Mouse',
                                'HDMI Cable',
                                'Power Cable',
                                'User Manual',
                            ];
                            // Get the list of all possible accessories from config, or use the fallback
                            // The system design refers to this as config('motac.loan_accessories_list') [cite: 64]
                            $allPossibleAccessories = config('motac.loan_accessories_list', $defaultAccessories);
                        @endphp

                        @if (count($allPossibleAccessories) > 0)
                            @foreach ($allPossibleAccessories as $accessoryName)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $accessoryName }}"
                                        id="return_accessory_{{ Str::slug($accessoryName) }}"
                                        name="accessories_checklist_on_return[]" {{-- Pre-selection will be handled by JavaScript --}}>
                                    <label class="form-check-label" for="return_accessory_{{ Str::slug($accessoryName) }}">
                                        {{ __($accessoryName) }} {{-- Use translation helper --}}
                                    </label>
                                </div>
                            @endforeach
                        @else
                            <p>{{ __('No standard accessories are defined in the system configuration.') }}</p>
                        @endif
                        @error('accessories_checklist_on_return')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                        @error('accessories_checklist_on_return.*')
                            {{-- For array validation errors --}}
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- Equipment Condition on Return --}}
                    <div class="mb-3">
                        <label for="equipment_condition_on_return"
                            class="form-label">{{ __('Equipment Condition on Return') }}</label>
                        <select class="form-select @error('equipment_condition_on_return') is-invalid @enderror"
                            id="equipment_condition_on_return" name="equipment_condition_on_return" required>
                            <option value="">{{ __('Select Condition') }}</option>
                            {{-- These values should ideally map to constants or enums defined in your Equipment model or configuration --}}
                            {{-- See equipment.condition_status or equipment.status from System Design pg. 8 [cite: 79, 80] --}}
                            <option value="Good" {{ old('equipment_condition_on_return') == 'Good' ? 'selected' : '' }}>
                                {{ __('Good') }}</option>
                            <option value="Damaged"
                                {{ old('equipment_condition_on_return') == 'Damaged' ? 'selected' : '' }}>
                                {{ __('Damaged') }}</option>
                            <option value="Lost" {{ old('equipment_condition_on_return') == 'Lost' ? 'selected' : '' }}>
                                {{ __('Lost') }}</option>
                            <option value="Under Maintenance"
                                {{ old('equipment_condition_on_return') == 'Under Maintenance' ? 'selected' : '' }}>
                                {{ __('Under Maintenance') }}</option>
                            {{-- Add other relevant conditions as defined in your system design --}}
                        </select>
                        @error('equipment_condition_on_return')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Return Notes --}}
                    <div class="mb-3">
                        <label for="return_notes" class="form-label">{{ __('Return Notes (Optional)') }}</label>
                        <textarea class="form-control @error('return_notes') is-invalid @enderror" id="return_notes" name="return_notes"
                            rows="3">{{ old('return_notes') }}</textarea>
                        @error('return_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <button type="submit" class="btn btn-primary">{{ __('Process Return') }}</button>
                    <a href="{{ route('resource-management.loan-transactions.show', $loanTransaction) }}"
                        class="btn btn-secondary">{{ __('Cancel') }}</a>
                    {{-- Or link back to the issued loans list --}}
                    {{-- <a href="{{ route('resource-management.admin.bpm.issued-loans') }}" class="btn btn-secondary">{{ __('Back to Issued List') }}</a> --}}
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the accessories that were actually issued.
            // Ensure this is an array, even if null or not set.
            const issuedAccessories = @json((array) ($loanTransaction->accessories_checklist_on_issue ?? []));

            // Get all possible accessories that are rendered as checkboxes
            // This relies on the $allPossibleAccessories variable used in the PHP loop above.
            const allPossibleAccessoriesOnForm = @json($allPossibleAccessories ?? $defaultAccessories);

            /**
             * Helper function to create a simple slug similar to Laravel's Str::slug()
             * (lowercase, hyphens for spaces/underscores, remove special chars except hyphen)
             * @param {string} str
             * @returns {string}
             */
            function simpleSlug(str) {
                if (typeof str !== 'string') return '';
                return str
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '') // Remove non-word, non-space, non-hyphen chars
                    .replace(/[\s_]+/g, '-') // Replace spaces and underscores with a single hyphen
                    .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
            }

            allPossibleAccessoriesOnForm.forEach(function(accessoryName) {
                // Construct the ID of the checkbox
                const checkboxId = 'return_accessory_' + simpleSlug(accessoryName);
                const checkbox = document.getElementById(checkboxId);

                if (checkbox && issuedAccessories.includes(accessoryName)) {
                    checkbox.checked = true;
                }
            });
        });
    </script>
@endpush
