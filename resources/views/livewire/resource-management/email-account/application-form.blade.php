<div>
    @section('title', __('Permohonan Akaun Emel / ID Pengguna MOTAC'))

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 py-6">
        {{-- Overall form title --}}
        <div class="flex justify-between items-center pb-3 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                {{ __('Permohonan Akaun Emel / ID Pengguna MOTAC') }}
            </h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('* WAJIB diisi') }}</span>
        </div>

        @if (session()->has('success'))
            <x-alert type="success" :message="session('success')" class="mb-4" />
        @endif
        @if (session()->has('error'))
            <x-alert type="danger" :message="session('error')" class="mb-4" />
        @endif
        @if ($errors->any())
            <x-alert type="danger" class="mb-4">
                <p class="font-semibold">Sila perbetulkan ralat berikut:</p>
                <ul class="mt-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form wire:submit.prevent="submitApplication" class="space-y-6">
            {{-- Use the applicant details component --}}
            <x-applicant-details-readonly :user="$this->user"
                title="{{ __('MAKLUMAT PEMOHON (Akan diambil dari data pengguna jika log masuk)') }}" />

            <x-card title="{{ __('BUTIRAN PERMOHONAN') }}">
                {{-- Taraf Perkhidmatan --}}
                <div class="mb-4">
                    <label for="service_status"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Taraf Perkhidmatan') }}
                        <span class="text-red-500">*</span></label>
                    <select wire:model.live="service_status" id="service_status"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('service_status') border-red-500 dark:border-red-400 ring-red-500 dark:ring-red-400 @enderror">
                        <option value="">- {{ __('Pilih Taraf Perkhidmatan') }} -</option>
                        @foreach (\App\Models\User::$SERVICE_STATUS_LABELS as $key => $label)
                            {{-- Assuming User model has this static array based on MyMail supplementary doc --}}
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('service_status')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tujuan Permohonan / Catatan (application_reason_notes) --}}
                <div class="mb-4">
                    <label for="application_reason_notes"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tujuan Permohonan / Catatan') }}
                        <span class="text-red-500">*</span></label>
                    <textarea wire:model.defer="application_reason_notes" id="application_reason_notes" rows="3"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('application_reason_notes') border-red-500 dark:border-red-400 ring-red-500 dark:ring-red-400 @enderror"
                        placeholder="{{ __('Nyatakan tujuan dan cadangan emel jika ada...') }}"></textarea>
                    @error('application_reason_notes')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cadangan Emel --}}
                <div class="mb-4">
                    <label for="proposed_email"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Cadangan E-mel (Jika Ada)') }}</label>
                    <input type="email" wire:model.defer="proposed_email" id="proposed_email"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 @error('proposed_email') border-red-500 dark:border-red-400 ring-red-500 dark:ring-red-400 @enderror"
                        placeholder="cth: nama.anda@motac.gov.my">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Biarkan kosong jika tiada cadangan spesifik. Format: nama@motac.gov.my') }}
                    </p>
                    @error('proposed_email')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Other Email application specific fields like group_email, contact_person_name, contact_person_email etc. as per design. --}}
                {{-- Example for Group Email (conditionally shown based on service_status or a dedicated checkbox) --}}
                @if ($this->showGroupEmailFields())
                    {{-- Implement this method in your Livewire component --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                        <h5 class="text-md font-semibold mb-3 text-gray-600 dark:text-gray-300">
                            {{ __('Maklumat Group E-mel / E-mel Agensi Luar') }}
                        </h5>
                        <div class="space-y-4">
                            <div>
                                <label for="group_email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama Group E-mel / E-mel Rasmi Agensi') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="group_email" id="group_email"
                                    class="input-field @error('group_email') input-error @enderror">
                                @error('group_email')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="contact_person_name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama Pegawai Dihubungi (Admin/EO/CC)') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="contact_person_name" id="contact_person_name"
                                    class="input-field @error('contact_person_name') input-error @enderror">
                                @error('contact_person_name')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="contact_person_email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('E-mel Pegawai Dihubungi (Rasmi MOTAC)') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="email" wire:model.defer="contact_person_email" id="contact_person_email"
                                    class="input-field @error('contact_person_email') input-error @enderror">
                                <p class="field-hint">{{ __('Sila pastikan e-mel adalah e-mel rasmi MOTAC.') }}</p>
                                @error('contact_person_email')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Add fields for service_start_date and service_end_date if service_status requires it --}}
                @if ($this->shouldShowServiceDates())
                    {{-- Implement this method in your Livewire component --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="service_start_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tarikh Mula Berkhidmat (Jika Berkaitan)') }}
                                <span class="text-red-500">*</span></label>
                            <input type="date" wire:model.defer="service_start_date" id="service_start_date"
                                class="input-field @error('service_start_date') input-error @enderror">
                            @error('service_start_date')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="service_end_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tarikh Akhir Berkhidmat (Jika Berkaitan)') }}
                                <span class="text-red-500">*</span></label>
                            <input type="date" wire:model.defer="service_end_date" id="service_end_date"
                                class="input-field @error('service_end_date') input-error @enderror">
                            @error('service_end_date')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif
            </x-card>


            {{-- PERAKUAN PEMOHON Section --}}
            <x-card title="{{ __('PERAKUAN PEMOHON') }}">
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Saya dengan ini mengesahkan bahawa:') }}</p>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <input type="checkbox" wire:model.defer="cert_info_is_true" id="cert_info_is_true"
                            value="1" class="form-checkbox @error('cert_info_is_true') border-red-500 @enderror">
                        <label for="cert_info_is_true" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            {{ __('Semua maklumat yang diberikan di dalam borang ini adalah BENAR.') }} <span
                                class="text-red-500">*</span>
                        </label>
                    </div>
                    @error('cert_info_is_true')
                        <p class="error-message">{{ $message }}</p>
                    @enderror

                    <div class="flex items-start">
                        <input type="checkbox" wire:model.defer="cert_data_usage_agreed" id="cert_data_usage_agreed"
                            value="1"
                            class="form-checkbox @error('cert_data_usage_agreed') border-red-500 @enderror">
                        <label for="cert_data_usage_agreed"
                            class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            {{ __('Saya BERSETUJU Bahagian Pengurusan Maklumat (BPM) menggunakan maklumat yang diberikan untuk memproses permohonan ini.') }}
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    @error('cert_data_usage_agreed')
                        <p class="error-message">{{ $message }}</p>
                    @enderror

                    <div class="flex items-start">
                        <input type="checkbox" wire:model.defer="cert_email_responsibility_agreed"
                            id="cert_email_responsibility_agreed" value="1"
                            class="form-checkbox @error('cert_email_responsibility_agreed') border-red-500 @enderror">
                        <label for="cert_email_responsibility_agreed"
                            class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            {{ __('Saya BERSETUJU untuk bertanggungjawab ke atas setiap e-mel yang dihantar dan diterima melalui akaun e-mel ini.') }}
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    @error('cert_email_responsibility_agreed')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                <p class="mt-4 text-xs text-red-600 dark:text-red-400 italic">
                    {{ __('Sila tandakan semua kotak perakuan untuk meneruskan permohonan.') }}</p>
            </x-card>

            <div class="flex justify-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" wire:loading.attr="disabled" wire:target="submitApplication"
                    class="btn btn-success"> {{-- Using Bootstrap-like class from your example --}}
                    <span wire:loading.remove wire:target="submitApplication">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $this->applicationToEdit ? __('Kemaskini Permohonan') : __('Hantar Permohonan') }}
                    </span>
                    <span wire:loading wire:target="submitApplication" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('Memproses...') }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
{{-- Add shared CSS for input-field, error-message, field-hint, form-checkbox if not globally available --}}
{{-- <style>
.input-field { /* Corresponds to .form-control in other examples */
    margin-top: 0.25rem; display: block; width: 100%;
    padding-left: 0.75rem; padding-right: 0.75rem; padding-top: 0.5rem; padding-bottom: 0.5rem;
    border-width: 1px; border-color: #D1D5DB; /* dark:border-gray-600 */
    border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    /* placeholder-gray-400 dark:placeholder-gray-500 */
    /* focus:outline-none focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-indigo-500 dark:focus:border-indigo-400 */
    font-size: 0.875rem; /* sm:text-sm */
    background-color: white; /* dark:bg-gray-700 */
    color: #111827; /* text-gray-900 dark:text-gray-100 */
}
.input-error { border-color: #F87171; /* dark:border-red-400 */ /* ring-red-500 dark:ring-red-400 */ }
.error-message { margin-top: 0.25rem; font-size: 0.75rem; color: #DC2626; /* dark:text-red-400 */ }
.field-hint { margin-top: 0.25rem; font-size: 0.75rem; color: #6B7280; /* dark:text-gray-400 */ }
.form-checkbox {
    height: 1rem; width: 1rem; color: #4F46E5; /* text-indigo-600 dark:text-indigo-500 */
    /* focus:ring-indigo-500 dark:focus:ring-indigo-400 */
    border-color: #D1D5DB; /* border-gray-300 dark:border-gray-600 */
    border-radius: 0.25rem;
    background-color: #F9FAFB; /* bg-gray-50 dark:bg-gray-700 */
}
.form-checkbox:checked { border-color: transparent; background-color: currentColor; }
@media (prefers-color-scheme: dark) {
    .input-field { border-color: #4B5563; background-color: #374151; color: #F3F4F6; }
    .input-error { border-color: #FCA5A5; }
    .error-message { color: #FCA5A5; }
    .field-hint { color: #9CA3AF; }
    .form-checkbox { border-color: #4B5563; background-color: #374151; }
}
</style> --}}
