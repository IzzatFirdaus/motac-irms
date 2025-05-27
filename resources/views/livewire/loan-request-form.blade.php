<div>
    @section('title', $this->isEdit ? __('Kemaskini Permohonan Pinjaman Peralatan ICT') : __('Borang Permohonan Peminjaman Peralatan ICT'))

    <form wire:submit.prevent="{{ $isEdit && $loanApplication && $loanApplication->status === \App\Models\LoanApplication::STATUS_DRAFT ? 'saveAsDraft' : 'submitForApproval' }}" class="space-y-8">

        {{-- Section Title & Mandatory Note --}}
        <div class="flex justify-between items-center pb-3 mb-4 border-b dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                {{-- Main form title from lang file or direct --}}
                {{ $isEdit ? __('Kemaskini Borang Permohonan Peminjaman Peralatan ICT') : __('Borang Permohonan Peminjaman Peralatan ICT') }}
            </h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.instruction_mandatory_fields') }}</span>
        </div>

        <x-validation-errors class="mb-4" />
        @if (session()->has('success')) <x-alert type="success" :message="session('success')" /> @endif
        @if (session()->has('error')) <x-alert type="danger" :message="session('error')" /> @endif

        {{-- BAHAGIAN 1: MAKLUMAT PEMOHON --}}
        <div>
            <div class="flex justify-between items-center pb-3 mb-2">
                 <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">
                    {{ __('forms.section_applicant_info_ict') }}
                </h3>
            </div>
            {{-- Component to display read-only applicant details --}}
            {{-- Assuming Auth::user() is the applicant for a new form. For editing, it could be $loanApplication->user --}}
            <x-applicant-details-readonly :user="$isEdit && $loanApplication ? $loanApplication->user : Auth::user()" :title="null" />

            <x-card class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    {{-- No.Telefon Pemohon (Contact number for this specific loan) --}}
                    <div>
                        <label for="applicant_mobile_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('No. Telefon Pemohon (Untuk Dihubungi)') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="applicant_mobile_number" wire:model.defer="applicant_mobile_number" {{-- CORRECTED BINDING --}}
                               class="mt-1 block w-full input-field @error('applicant_mobile_number') input-error @enderror"
                               placeholder="{{ __('Cth: 012-3456789') }}">
                        @error('applicant_mobile_number') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div></div> {{-- Spacer --}}

                    <div class="md:col-span-2">
                        <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.label_application_purpose') }}<span class="text-red-500">*</span></label>
                        <textarea id="purpose" wire:model.defer="purpose" rows="3"
                                  class="mt-1 block w-full input-field @error('purpose') input-error @enderror"
                                  placeholder="{{ __('Nyatakan tujuan permohonan peralatan ICT...') }}"></textarea>
                        @error('purpose') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.label_location_ict') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="location" wire:model.defer="location"
                               class="mt-1 block w-full input-field @error('location') input-error @enderror"
                               placeholder="{{ __('Cth: Bilik Mesyuarat Utama, Aras 10') }}">
                        @error('location') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>

                     <div>
                        <label for="return_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lokasi Pemulangan Peralatan') }}</label>
                        <input type="text" id="return_location" wire:model.defer="return_location"
                               class="mt-1 block w-full input-field @error('return_location') input-error @enderror"
                               placeholder="{{ __('Cth: Kaunter BPM (Jika berbeza daripada lokasi guna)') }}">
                        @error('return_location') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="loan_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.label_loan_date') }}<span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="loan_start_date" wire:model.defer="loan_start_date"
                               class="mt-1 block w-full input-field @error('loan_start_date') input-error @enderror">
                        @error('loan_start_date') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="loan_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.label_expected_return_date') }}<span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="loan_end_date" wire:model.defer="loan_end_date"
                               class="mt-1 block w-full input-field @error('loan_end_date') input-error @enderror">
                        @error('loan_end_date') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-card>
        </div>

        {{-- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
        <x-card title="{{ __('forms.section_responsible_officer_info') }}">
            <div class="flex items-start mb-4">
                <div class="flex items-center h-5">
                    <input id="isApplicantResponsible" wire:model.live="isApplicantResponsible" type="checkbox" {{-- CORRECTED BINDING & ID --}}
                           class="h-4 w-4 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-700">
                </div>
                <div class="ml-3 text-sm">
                    <label for="isApplicantResponsible" class="font-medium text-gray-700 dark:text-gray-300">{{ __('forms.instruction_responsible_officer_is_applicant') }}</label>  {{-- CORRECTED BINDING --}}
                </div>
            </div>

            @if(!$isApplicantResponsible) {{-- CORRECTED VARIABLE --}}
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 italic">
                    {{ __('forms.instruction_responsible_officer_different') }}
                </p>
                {{-- Option to select from existing users --}}
                <div class="mb-4">
                    <label for="responsible_officer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Pilih Pegawai Bertanggungjawab (dari sistem)') }}</label>
                    <select id="responsible_officer_id" wire:model.live="responsible_officer_id"
                           class="mt-1 block w-full input-field @error('responsible_officer_id') input-error @enderror"
                           @if(!empty($manual_responsible_officer_name)) disabled @endif> {{-- Disable if manual entry is used --}}
                        @foreach($systemUsersForResponsibleOfficer as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('responsible_officer_id') <p class="input-error-msg">{{ $message }}</p> @enderror
                </div>
                 <p class="text-sm text-center my-2 text-gray-500 dark:text-gray-400">{{ __('ATAU masukkan butiran secara manual di bawah') }}</p>

                {{-- Manual entry fields --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                    <div>
                        <label for="manual_responsible_officer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.label_full_name') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="manual_responsible_officer_name" wire:model.defer="manual_responsible_officer_name" {{-- CORRECTED BINDING --}}
                               @if(!empty($responsible_officer_id)) readonly @endif {{-- Readonly if system user selected --}}
                               class="mt-1 block w-full input-field @error('manual_responsible_officer_name') input-error @enderror">
                        @error('manual_responsible_officer_name') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="manual_responsible_officer_jawatan_gred" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.label_position_grade') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="manual_responsible_officer_jawatan_gred" wire:model.defer="manual_responsible_officer_jawatan_gred" {{-- CORRECTED BINDING --}}
                               @if(!empty($responsible_officer_id)) readonly @endif
                               class="mt-1 block w-full input-field @error('manual_responsible_officer_jawatan_gred') input-error @enderror">
                        @error('manual_responsible_officer_jawatan_gred') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="manual_responsible_officer_mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.label_phone_number') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="manual_responsible_officer_mobile" wire:model.defer="manual_responsible_officer_mobile" {{-- CORRECTED BINDING --}}
                               @if(!empty($responsible_officer_id)) readonly @endif
                               class="mt-1 block w-full input-field @error('manual_responsible_officer_mobile') input-error @enderror">
                        @error('manual_responsible_officer_mobile') <p class="input-error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif
        </x-card>

        {{-- BAHAGIAN 3: MAKLUMAT PERALATAN --}}
        <x-card>
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <span>{{ __('forms.section_equipment_details_ict') }}</span>
                    <button type="button" wire:click="addItem" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-plus mr-1 -ml-0.5 h-4 w-4"></i> {{ __('Tambah Item Peralatan') }}
                    </button>
                </div>
            </x-slot>

            <div class="space-y-6">
                @forelse ($items as $index => $item) {{-- Changed from loan_application_items to items --}}
                    <div wire:key="loan_item_{{ $index }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md relative bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex justify-between items-center mb-3">
                             <h3 class="text-md font-medium text-gray-700 dark:text-gray-300">{{ __('Peralatan #') }}{{ $index + 1 }}</h3>
                            @if (count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})" title="{{__('Buang Item Ini')}}"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="ti ti-circle-x text-lg"></i>
                                </button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="item_{{ $index }}_equipment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.table_header_equipment_type') }} <span class="text-red-500">*</span></label>
                                <select id="item_{{ $index }}_equipment_type" wire:model.defer="items.{{ $index }}.equipment_type" {{-- Changed from loan_application_items to items --}}
                                       class="mt-1 block w-full input-field @error('items.'.$index.'.equipment_type') input-error @enderror">
                                    @foreach($equipmentTypeOptions as $key => $label)
                                        <option value="{{ $key }}">{{ __($label) }}</option>
                                    @endforeach
                                </select>
                                @error('items.'.$index.'.equipment_type') <p class="input-error-msg">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="item_{{ $index }}_quantity_requested" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.table_header_quantity') }} <span class="text-red-500">*</span></label>
                                <input type="number" id="item_{{ $index }}_quantity_requested" wire:model.defer="items.{{ $index }}.quantity_requested" min="1" {{-- Changed from loan_application_items to items --}}
                                       class="mt-1 block w-full input-field @error('items.'.$index.'.quantity_requested') input-error @enderror">
                                @error('items.'.$index.'.quantity_requested') <p class="input-error-msg">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="item_{{ $index }}_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('forms.table_header_remarks') }}</label>
                                <input type="text" id="item_{{ $index }}_notes" wire:model.defer="items.{{ $index }}.notes" {{-- Changed from loan_application_items to items --}}
                                       class="mt-1 block w-full input-field @error('items.'.$index.'.notes') input-error @enderror"
                                       placeholder="{{ __('Cth: Model spesifik, perisian khas, dll.') }}">
                                @error('items.'.$index.'.notes') <p class="input-error-msg">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @empty
                    <x-alert type="info" message="{{__('Sila tambah sekurang-kurangnya satu item peralatan dengan menekan butang "Tambah Item Peralatan".')}}" class="text-center"/>
                @endforelse
            </div>
             @error('items') <p class="input-error-msg mt-2">{{ $message }}</p> @enderror
        </x-card>

        {{-- BAHAGIAN 4: PENGESAHAN PEMOHON --}}
        <x-card title="{{ __('forms.section_applicant_confirmation_ict') }}">
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="applicant_confirmation" wire:model.defer="applicant_confirmation" type="checkbox" value="1"
                               class="h-4 w-4 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-700 @error('applicant_confirmation') border-red-500 dark:border-red-400 @enderror">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="applicant_confirmation" class="font-medium text-gray-700 dark:text-gray-300">
                            {{ __('forms.text_applicant_declaration_ict') }}
                        </label>
                    </div>
                </div>
                @error('applicant_confirmation') <p class="input-error-msg">{{ $message }}</p> @enderror
            </div>
            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                {{__('messages.instruction_ict_loan_check_equipment')}}
            </p>
        </x-card>

        {{-- Action Buttons --}}
        <div class="pt-8 flex items-center justify-between space-x-4">
            <button type="button" wire:click="resetForm" class="btn btn-outline-secondary">
                <i class="ti ti-refresh mr-1.5"></i> {{ __('app.button_reset') }}
            </button>
            <div class="space-x-2">
                <button type="button" wire:click="saveAsDraft" wire:loading.attr="disabled" wire:target="saveAsDraft,submitForApproval"
                        class="btn btn-outline-primary">
                    <span wire:loading.remove wire:target="saveAsDraft">
                         <i class="ti ti-device-floppy mr-1.5"></i> {{ __('app.button_save_draft') }}
                    </span>
                    <span wire:loading wire:target="saveAsDraft" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ __('Menyimpan...') }}
                    </span>
                </button>
                <button type="button" wire:click="submitForApproval" wire:loading.attr="disabled" wire:target="saveAsDraft,submitForApproval"
                        class="btn btn-primary">
                    <span wire:loading.remove wire:target="submitForApproval">
                        <i class="ti ti-send mr-1.5"></i> {{ $this->isEdit && $this->loanApplication && $this->loanApplication->status !== \App\Models\LoanApplication::STATUS_DRAFT ? __('Kemaskini & Hantar Semula') : __('app.button_submit_application') }}
                    </span>
                    <span wire:loading wire:target="submitForApproval" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        {{ __('Memproses...') }}
                    </span>
                </button>
            </div>
        </div>
    </form>
</div>
