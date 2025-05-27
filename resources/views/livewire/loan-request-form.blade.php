<div>
    @section('title', __('Borang Permohonan Peminjaman Peralatan ICT'))

    <form wire:submit.prevent="submitLoanApplication" class="space-y-8">

        {{-- BAHAGIAN 1: MAKLUMAT PEMOHON --}}
        <div> {{-- Removed the outer card for this section as the component has its own --}}
            <div class="flex justify-between items-center pb-3 mb-0"> {{-- Adjusted mb from mb-6 --}}
                {{-- Title provided by component, but if you need an overall section title, add it outside the component call --}}
                 <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                    {{ __('BAHAGIAN 1: MAKLUMAT PEMOHON') }}
                </h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('* WAJIB diisi') }}</span>
            </div>

            {{-- Use the new component for read-only details --}}
            <x-applicant-details-readonly :user="Auth::user()" title="" /> {{-- Pass title as empty if the outer title is sufficient --}}

            {{-- Fields that are specific to this form or editable --}}
            <x-card> {{-- Wrap editable fields in their own card or style directly --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    {{-- No.Telefon (Editable for Loan Form, was part of applicantName previously, so separating) --}}
                    <div>
                        <label for="applicant_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('No.Telefon Pemohon (Untuk Dihubungi)') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="applicant_phone" wire:model.defer="applicant_phone"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('applicant_phone') border-red-500 dark:border-red-400 @enderror"
                               placeholder="{{ __('Cth: 012-3456789') }}">
                        @error('applicant_phone') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                     {{-- Spacer to maintain grid --}}
                    <div></div>

                    {{-- Tujuan Permohonan --}}
                    <div class="md:col-span-2">
                        <label for="purpose" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tujuan Permohonan') }}<span class="text-red-500">*</span></label>
                        <textarea id="purpose" wire:model.defer="purpose" rows="3"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('purpose') border-red-500 dark:border-red-400 @enderror"
                                  placeholder="{{ __('Nyatakan tujuan permohonan peralatan ICT...') }}"></textarea>
                        @error('purpose') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lokasi Penggunaan Peralatan --}}
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lokasi Penggunaan Peralatan') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="location" wire:model.defer="location"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('location') border-red-500 dark:border-red-400 @enderror"
                               placeholder="{{ __('Cth: Bilik Mesyuarat Utama, Aras 10') }}">
                        @error('location') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lokasi Pemulangan Peralatan --}}
                     <div>
                        <label for="return_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lokasi Dijangka Pulang') }}</label>
                        <input type="text" id="return_location" wire:model.defer="return_location"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('return_location') border-red-500 dark:border-red-400 @enderror"
                               placeholder="{{ __('Cth: Kaunter BPM (Jika berbeza daripada lokasi guna)') }}">
                        @error('return_location') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tarikh Pinjaman --}}
                    <div>
                        <label for="loan_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tarikh Pinjaman') }}<span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="loan_start_date" wire:model.defer="loan_start_date"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('loan_start_date') border-red-500 dark:border-red-400 @enderror">
                        @error('loan_start_date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tarikh Dijangka Pulang --}}
                    <div>
                        <label for="loan_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tarikh Dijangka Pulang') }}<span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="loan_end_date" wire:model.defer="loan_end_date"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('loan_end_date') border-red-500 dark:border-red-400 @enderror">
                        @error('loan_end_date') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            </x-card>
        </div>

        {{-- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
        <x-card title="{{ __('BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}">
            <div class="flex items-start mb-4">
                <div class="flex items-center h-5">
                    <input id="applicant_is_responsible_officer" wire:model.live="applicant_is_responsible_officer" type="checkbox"
                           class="h-4 w-4 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-700">
                </div>
                <div class="ml-3 text-sm">
                    <label for="applicant_is_responsible_officer" class="font-medium text-gray-700 dark:text-gray-300">{{ __('Sila tandakan jika Pemohon adalah Pegawai Bertanggungjawab.') }}</label>
                </div>
            </div>

            @if(!$applicant_is_responsible_officer)
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 italic">
                    {{ __('Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.') }}
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                    <div>
                        <label for="responsible_officer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama Penuh') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="responsible_officer_name" wire:model.defer="responsible_officer_name"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('responsible_officer_name') border-red-500 dark:border-red-400 @enderror">
                        @error('responsible_officer_name') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="responsible_officer_position_grade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jawatan & Gred') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="responsible_officer_position_grade" wire:model.defer="responsible_officer_position_grade"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('responsible_officer_position_grade') border-red-500 dark:border-red-400 @enderror">
                        @error('responsible_officer_position_grade') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="responsible_officer_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('No.Telefon') }}<span class="text-red-500">*</span></label>
                        <input type="text" id="responsible_officer_phone" wire:model.defer="responsible_officer_phone"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('responsible_officer_phone') border-red-500 dark:border-red-400 @enderror">
                        @error('responsible_officer_phone') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endif
        </x-card>

        {{-- BAHAGIAN 3: MAKLUMAT PERALATAN --}}
        <x-card>
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <span>{{ __('BAHAGIAN 3: MAKLUMAT PERALATAN') }}</span>
                    <button type="button" wire:click="addLoanItem"
                            class="btn btn-outline-secondary btn-sm"> {{-- Use your defined button classes --}}
                        <i class="ti ti-plus mr-1 -ml-0.5 h-4 w-4"></i> {{ __('Tambah Item') }}
                    </button>
                </div>
            </x-slot>

            <div class="space-y-6">
                @forelse ($loan_application_items as $index => $item)
                    <div wire:key="loan_item_{{ $index }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-md relative bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex justify-between items-center mb-3">
                             <h3 class="text-md font-medium text-gray-700 dark:text-gray-300">{{ __('Peralatan #') }}{{ $index + 1 }}</h3>
                            @if (count($loan_application_items) > 1)
                                <button type="button" wire:click="removeLoanItem({{ $index }})" title="{{__('Buang Peralatan')}}"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="ti ti-circle-x text-lg"></i>
                                </button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label for="item_{{ $index }}_equipment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jenis Peralatan') }} <span class="text-red-500">*</span></label>
                                {{-- Use select for equipment type based on Equipment model constants --}}
                                <select id="item_{{ $index }}_equipment_type" wire:model.defer="loan_application_items.{{ $index }}.equipment_type"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('loan_application_items.'.$index.'.equipment_type') border-red-500 dark:border-red-400 @enderror">
                                    <option value="">- Pilih Jenis -</option>
                                    @foreach(App\Models\Equipment::$ASSET_TYPES_LABELS as $key => $label) {{-- --}}
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('loan_application_items.'.$index.'.equipment_type') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="item_{{ $index }}_quantity_requested" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kuantiti') }} <span class="text-red-500">*</span></label>
                                <input type="number" id="item_{{ $index }}_quantity_requested" wire:model.defer="loan_application_items.{{ $index }}.quantity_requested" min="1"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('loan_application_items.'.$index.'.quantity_requested') border-red-500 dark:border-red-400 @enderror">
                                @error('loan_application_items.'.$index.'.quantity_requested') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="item_{{ $index }}_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Catatan (Cth: Model spesifik jika perlu)') }}</label>
                                <input type="text" id="item_{{ $index }}_notes" wire:model.defer="loan_application_items.{{ $index }}.notes"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 @error('loan_application_items.'.$index.'.notes') border-red-500 dark:border-red-400 @enderror"
                                       placeholder="{{ __('Cth: Perisian khas diperlukan, Keperluan segera, dll.') }}">
                                @error('loan_application_items.'.$index.'.notes') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                @empty
                    <x-alert type="info" message="Sila tambah sekurang-kurangnya satu item peralatan." class="text-center"/>
                @endforelse
            </div>
        </x-card>

        {{-- BAHAGIAN 4: PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB) --}}
        <x-card title="{{ __('BAHAGIAN 4: PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB)') }}">
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="applicant_confirmation" wire:model.defer="applicant_confirmation" type="checkbox" value="1"
                               class="h-4 w-4 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 border-gray-300 dark:border-gray-600 rounded bg-gray-50 dark:bg-gray-700 @error('applicant_confirmation') border-red-500 dark:border-red-400 @enderror">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="applicant_confirmation" class="font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut.') }}
                        </label>
                    </div>
                </div>
                @error('applicant_confirmation') <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                {{__('Peringatan: Sila semak dan periksa kesempurnaan peralatan semasa mengambil dan sebelum memulangkan peralatan yang dipinjam. Kehilangan dan kekurangan pada peralatan semasa pemulangan adalah dibawah tanggungjawab pemohon.')}}
            </p>
        </x-card>

        {{-- Action Buttons --}}
        <div class="pt-8 flex items-center justify-end space-x-4">
            <button type="button" wire:click="resetForm"
                    class="btn btn-outline-secondary"> {{-- Use your defined button classes --}}
                <i class="ti ti-refresh mr-1.5"></i> {{ __('Reset Borang') }}
            </button>
            <button type="submit" wire:loading.attr="disabled" wire:target="submitLoanApplication"
                    class="btn btn-primary"> {{-- Use your defined button classes --}}
                <span wire:loading.remove wire:target="submitLoanApplication">
                    <i class="ti ti-send mr-1.5"></i> {{ $this->applicationToEdit ? __('Kemaskini Permohonan') : __('Hantar Permohonan') }}
                </span>
                <span wire:loading wire:target="submitLoanApplication" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Memproses...') }}
                </span>
            </button>
        </div>
    </form>
</div>
