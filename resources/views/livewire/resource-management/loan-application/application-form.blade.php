{{-- resources/views/livewire/resource-management/loan-application/application-form.blade.php --}}
<div>
    @php
        // Construct the full title including the reference number if available and editing
        $pageTitle = $this->generatePageTitle();
        if (
            $this->editing_application_id &&
            $this->loanApplicationInstance &&
            $this->loanApplicationInstance->reference_number
        ) {
            $pageTitle .= ' (' . $this->loanApplicationInstance->reference_number . ')';
        }
        $pageTitle .= ' - ' . __(config('variables.templateName', 'Sistem Pengurusan Sumber Bersepadu MOTAC'));
    @endphp
    @section('title', $pageTitle)

    <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
        <div class="d-flex align-items-center">
            <div class="d-flex flex-column align-items-center me-3"> {{-- Flex column to stack logo and text, align items center --}}
                <img src="{{ asset('assets/img/logo/logo_bpm.png') }}" alt="BPM Logo"
                    style="height: 60px; width: 60px; object-fit: contain;"> {{-- Increased size, removed border/padding/radius --}}
                <small class="text-muted text-center mt-1">Bahagian<br>Pengurusan Maklumat</small> {{-- Added text, centered --}}
            </div>
            <h1 class="h4 fw-bold text-dark mb-0">{{ $this->generatePageTitle() }}</h1>
        </div>
        <small class="text-muted">{{ __('No. Rujukan Borang: PK.(S).MOTAC.07.(L3)') }}</small>
    </div>

    @if ($isEditMode && $completedSubmissionDate)
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="p-2 border rounded bg-light-subtle">
                    <span class="fw-medium">{{ __('Tarikh Permohonan Lengkap Diterima') }}:</span>
                    <span>{{ $completedSubmissionDate }}</span>
                </div>
            </div>
        </div>
    @elseif(
        $isEditMode &&
            !$completedSubmissionDate &&
            $this->loanApplicationInstance &&
            $this->loanApplicationInstance->status !== \App\Models\LoanApplication::STATUS_DRAFT)
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="p-2 border rounded bg-light-subtle">
                    <span class="fw-medium">{{ __('Tarikh Permohonan Lengkap Diterima') }}:</span>
                    <span class="fst-italic">{{ __('Akan dikemaskini oleh BPM') }}</span>
                </div>
            </div>
        </div>
    @endif


    @include('_partials._alerts.alert-general')

    <form wire:submit.prevent="submitLoanApplication" x-data="{
        termsScrolled: @entangle('termsScrolled').live,
        applicantConfirmation: @entangle('applicant_confirmation').live
    }">

        {{-- BAHAGIAN 1: MAKLUMAT PEMOHON --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-person-lines-fill me-2 fs-5"></i>
                        {{ __('BAHAGIAN 1: MAKLUMAT PEMOHON') }}
                    </h2>
                    <small class="text-muted">{{ __('* WAJIB diisi') }}</small>
                </div>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Nama Penuh') }}</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                            {{ $this->applicantName }}</p>
                    </div>
                    <div class="col-md-6">
                        <label for="applicant_phone" class="form-label fw-medium">{{ __('No.Telefon') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" id="applicant_phone" wire:model.defer="applicant_phone"
                            class="form-control @error('applicant_phone') is-invalid @enderror"
                            placeholder="{{ __('Cth: 012-3456789') }}">
                        @error('applicant_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Jawatan & Gred') }}</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                            {{ $this->applicantPositionAndGrade }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Bahagian/Unit') }}</label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                            {{ $this->applicantDepartment }}</p>
                    </div>
                    <div class="col-md-12">
                        <label for="purpose" class="form-label fw-medium">{{ __('Tujuan Permohonan') }}<span
                                class="text-danger">*</span></label>
                        <textarea id="purpose" wire:model.defer="purpose" rows="3"
                            class="form-control @error('purpose') is-invalid @enderror"
                            placeholder="{{ __('Nyatakan tujuan permohonan peralatan ICT...') }}"></textarea>
                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="location" class="form-label fw-medium">{{ __('Lokasi Penggunaan Peralatan') }}<span
                                class="text-danger">*</span></label>
                        <input type="text" id="location" wire:model.defer="location"
                            class="form-control @error('location') is-invalid @enderror"
                            placeholder="{{ __('Cth: Bilik Mesyuarat Utama, Aras 10') }}">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="return_location"
                            class="form-label fw-medium">{{ __('Lokasi Dijangka Pulang') }}</label>
                        <input type="text" id="return_location" wire:model.defer="return_location"
                            class="form-control @error('return_location') is-invalid @enderror"
                            placeholder="{{ __('Cth: Kaunter BPM (Jika berbeza)') }}">
                        @error('return_location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="loan_start_date" class="form-label fw-medium">{{ __('Tarikh Pinjaman') }}<span
                                class="text-danger">*</span></label>
                        <input type="datetime-local" id="loan_start_date" wire:model.defer="loan_start_date"
                            class="form-control @error('loan_start_date') is-invalid @enderror"
                            min="{{ now()->toDateTimeLocalString('minute') }}">
                        @error('loan_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="loan_end_date" class="form-label fw-medium">{{ __('Tarikh Dijangka Pulang') }}<span
                                class="text-danger">*</span></label>
                        <input type="datetime-local" id="loan_end_date" wire:model.defer="loan_end_date"
                            class="form-control @error('loan_end_date') is-invalid @enderror">
                        @error('loan_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-person-check-fill me-2 fs-5"></i>
                    {{ __('BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="form-check mb-3">
                    <input id="applicant_is_responsible_officer" wire:model.live="applicant_is_responsible_officer"
                        type="checkbox" class="form-check-input">
                    <label for="applicant_is_responsible_officer"
                        class="form-check-label fw-medium">{{ __('Pemohon adalah Pegawai Bertanggungjawab.') }}</label>
                </div>

                {{-- Show responsible officer details IF applicant IS the responsible officer --}}
                @if ($applicant_is_responsible_officer)
                    <p class="text-muted mb-3 fst-italic">
                        {{ __('Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon. Jika Pemohon adalah Pegawai Bertanggungjawab, maklumat pemohon akan digunakan.') }}
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label
                                class="form-label fw-medium">{{ __('Nama Penuh Pegawai Bertanggungjawab') }}</label>
                            <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                                {{ $this->responsibleOfficerName }}</p>
                        </div>
                        <div class="col-md-6">
                            <label
                                class="form-label fw-medium">{{ __('No.Telefon Pegawai Bertanggungjawab') }}</label>
                            <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                                {{ $this->responsibleOfficerPhone }}</p>
                        </div>
                        <div class="col-md-12">
                            <label
                                class="form-label fw-medium">{{ __('Jawatan & Gred Pegawai Bertanggungjawab') }}</label>
                            <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">
                                {{ $this->responsibleOfficerPositionAndGrade }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- MAKLUMAT PEGAWAI PENYOKONG --}}
        {{--
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-person-badge-fill me-2 fs-5"></i>
                    {{ __('MAKLUMAT PEGAWAI PENYOKONG') }} <span class="text-danger">*</span>
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="mb-3">
                    <label for="supporting_officer_id" class="form-label fw-medium">{{ __('Nama Penuh Pegawai Penyokong') }}</label>
                    <select id="supporting_officer_id" wire:model.defer="supporting_officer_id" class="form-select @error('supporting_officer_id') is-invalid @enderror">
                        <option value="">-- {{ __('Pilih Pegawai Penyokong') }} --</option>
                        @foreach ($supportingOfficerOptions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">{{__('Pegawai Penyokong mestilah sekurang-kurangnya Gred :grade atau setara.', ['grade' => config('motac.approval.min_loan_support_grade_level', 41)])}}</small>
                    @error('supporting_officer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        --}}

        {{-- BAHAGIAN 3: MAKLUMAT PERALATAN --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        <i class="bi bi-tools me-2 fs-5"></i>
                        {{ __('BAHAGIAN 3: MAKLUMAT PERALATAN') }} <span class="text-danger">*</span>
                    </h2>
                    <button type="button" wire:click="addLoanItem" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Peralatan') }}
                    </button>
                </div>
            </div>
            <div class="card-body p-4 motac-card-body">
                <p class="text-muted mb-2 fst-italic small">{{ __('Sila senaraikan peralatan ICT yang diperlukan.') }}
                </p>
                <div class="list-group">
                    @forelse ($loan_application_items as $index => $item)
                        <div wire:key="loan_item_{{ $index }}"
                            class="list-group-item mb-3 p-3 border rounded bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h3 class="h6 mb-0 fw-medium text-primary">{{ __('Peralatan #') }}{{ $index + 1 }}
                                </h3>
                                @if (count($loan_application_items) > 1)
                                    <button type="button" wire:click="removeLoanItem({{ $index }})"
                                        title="{{ __('Buang Peralatan') }}"
                                        class="btn btn-sm btn-icon btn-text-danger p-0">
                                        <i class="bi bi-x-circle-fill fs-5"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="item_{{ $index }}_equipment_type"
                                        class="form-label fw-medium">{{ __('Jenis Peralatan') }} <span
                                            class="text-danger">*</span></label>
                                    <select id="item_{{ $index }}_equipment_type"
                                        wire:model.defer="loan_application_items.{{ $index }}.equipment_type"
                                        class="form-select @error('loan_application_items.' . $index . '.equipment_type') is-invalid @enderror">
                                        <option value="">-- {{ __('Pilih Jenis') }} --</option>
                                        @foreach ($this->equipmentTypeOptions as $key => $label)
                                            <option value="{{ $key }}">{{ __($label) }}</option>
                                        @endforeach
                                    </select>
                                    @error('loan_application_items.' . $index . '.equipment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="item_{{ $index }}_quantity_requested"
                                        class="form-label fw-medium">{{ __('Kuantiti') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="number" id="item_{{ $index }}_quantity_requested"
                                        wire:model.defer="loan_application_items.{{ $index }}.quantity_requested"
                                        min="1" value="1"
                                        class="form-control @error('loan_application_items.' . $index . '.quantity_requested') is-invalid @enderror">
                                    @error('loan_application_items.' . $index . '.quantity_requested')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="item_{{ $index }}_notes"
                                        class="form-label fw-medium">{{ __('Catatan') }}</label>
                                    <input type="text" id="item_{{ $index }}_notes"
                                        wire:model.defer="loan_application_items.{{ $index }}.notes"
                                        class="form-control @error('loan_application_items.' . $index . '.notes') is-invalid @enderror"
                                        placeholder="{{ __('Cth: Model spesifik, perisian khas diperlukan, dll.') }}">
                                    @error('loan_application_items.' . $index . '.notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 border rounded bg-light-subtle">
                            <i class="bi bi-info-circle fs-2 text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">{{ __('Sila tambah sekurang-kurangnya satu item peralatan.') }}
                            </p>
                        </div>
                    @endforelse
                    @error('loan_application_items')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Syarat-Syarat Permohonan --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-card-checklist me-2 fs-5"></i>
                    {{ __('Syarat-Syarat Permohonan Pinjaman Peralatan ICT untuk Kegunaan Rasmi Kementerian Pelancongan, Seni dan Budaya') }}
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div id="termsBox"
                    style="height: 250px; overflow-y: scroll; border: 1px solid #dee2e6; padding: 15px; background-color: #f8f9fa; border-radius: 0.375rem;"
                    class="small text-muted motac-terms-box" x-ref="termsBox"
                    @scroll.debounce.150ms="
                        if ($refs.termsBox.scrollTop + $refs.termsBox.clientHeight >= $refs.termsBox.scrollHeight - 20) {
                            termsScrolled = true;
                        }
                     ">
                    <p class="fw-bold mb-2">{{ __('Peringatan:') }}</p>
                    <ol class="ps-3 mb-0">
                        <li class="mb-2">
                            {{ __('Sila pastikan semua medan yang diperlukan dalam permohonan ini diisi dengan lengkap. Medan bertanda * adalah wajib diisi.') }}
                        </li>
                        <li class="mb-2">
                            {{ __("Permohonan adalah tertakluk kepada ketersediaan peralatan berdasarkan konsep 'Siapa Cepat, Dia Dapat'.") }}
                        </li>
                        <li class="mb-2">
                            {{ __('Permohonan anda akan disemak dan diproses dalam tempoh tiga (3) hari bekerja dari tarikh penyerahan lengkap. Bahagian Pengurusan Maklumat (BPM) tidak bertanggungjawab atas ketersediaan peralatan jika permohonan dikemukakan kurang daripada tempoh pemprosesan yang ditetapkan dari tarikh diperlukan.') }}
                        </li>
                        <li class="mb-2">
                            {{ __('Setelah diluluskan, pemohon mungkin dikehendaki untuk mengemukakan salinan digital atau bercetak ringkasan permohonan ini semasa mengambil peralatan, menurut prosedur BPM.') }}
                        </li>
                        <li class="mb-2">
                            {{ __('Pemohon diingatkan untuk menyemak dan memeriksa kesempurnaan dan keadaan peralatan semasa mengambil dan sebelum memulangkan peralatan yang dipinjam.') }}
                        </li>
                        <li class="mb-2">
                            {{ __('Kehilangan atau kerosakan pada peralatan semasa tempoh pinjaman adalah tanggungjawab pemohon, dan tindakan boleh diambil mengikut peraturan-peraturan yang berkuatkuasa.') }}
                        </li>
                        <li class="mb-2">
                            {{ __("'Pemohon' merujuk kepada kakitangan yang mengemukakan permohonan pinjaman peralatan ICT ini.") }}
                        </li>
                        <li class="mb-2">
                            {{ __("'Pegawai Bertanggungjawab' merujuk kepada kakitangan yang bertanggungjawab ke atas penggunaan, keselamatan, dan keadaan peralatan yang dipinjam.") }}
                        </li>
                        <li class="mb-2">
                            {{ __("'Pegawai Pengeluar' merujuk kepada kakitangan BPM yang mengeluarkan peralatan untuk diberikan kepada 'Pegawai Penerima'.") }}
                        </li>
                        <li class="mb-2">
                            {{ __("'Pegawai Penerima' merujuk kepada kakitangan yang menerima peralatan daripada 'Pegawai Pengeluar'.") }}
                        </li>
                        <li class="mb-2">
                            {{ __("'Pegawai Yang Memulangkan' merujuk kepada kakitangan yang memulangkan peralatan yang dipinjam.") }}
                        </li>
                        <li class="mb-2">
                            {{ __("'Pegawai Terima Pulangan' merujuk kepada kakitangan BPM yang menerima peralatan yang dipulangkan oleh 'Pegawai Yang Memulangkan'.") }}
                        </li>
                        <li class="mb-0">
                            {{ __('Permohonan lengkap yang dihantar melalui sistem ini diproses oleh Bahagian Pengurusan Maklumat, Kementerian Pelancongan, Seni dan Budaya.') }}
                        </li>
                    </ol>
                </div>
                @error('termsScrolled')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- BAHAGIAN 4: PENGESAHAN PEMOHON --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    <i class="bi bi-patch-check-fill me-2 fs-5"></i>
                    {{ __('BAHAGIAN 4: PENGESAHAN PEMOHON') }}
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="certification-block bg-light-subtle p-3 rounded border mb-3">
                    <div class="form-check mb-2">
                        <input id="applicant_confirmation" wire:model.defer="applicant_confirmation" type="checkbox"
                            value="1"
                            class="form-check-input @error('applicant_confirmation') is-invalid @enderror"
                            x-model="applicantConfirmation">
                        <label for="applicant_confirmation" class="form-check-label fw-medium">
                            {{ __('Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut.') }}
                            <span class="text-danger">*</span>
                        </label>
                        @error('applicant_confirmation')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <p class="form-text small">
                    {{ __('Peringatan: Sila semak dan periksa kesempurnaan peralatan semasa mengambil dan sebelum memulangkan peralatan yang dipinjam. Kehilangan dan kekurangan pada peralatan semasa pemulangan adalah dibawah tanggungjawab pemohon.') }}
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex justify-content-end gap-2 pt-3">
            <button type="button" wire:click="resetFormForCreate" class="btn btn-outline-secondary text-uppercase">
                <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Reset Borang') }}
            </button>
            <button type="button" wire:click="saveAsDraft" wire:loading.attr="disabled" wire:target="saveAsDraft"
                class="btn btn-secondary text-uppercase">
                <span wire:loading.remove wire:target="saveAsDraft">
                    <i class="bi bi-save-fill me-1"></i> {{ __('Simpan Draf') }}
                </span>
                <span wire:loading wire:target="saveAsDraft" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {{ __('Menyimpan...') }}
                </span>
            </button>
            <button type="submit" wire:loading.attr="disabled" wire:target="submitLoanApplication"
                class="btn btn-primary text-uppercase" x-bind:disabled="!termsScrolled || !applicantConfirmation">
                <span wire:loading.remove wire:target="submitLoanApplication">
                    <i class="bi bi-send-check-fill me-1"></i> {{ __('Hantar Permohonan') }}
                </span>
                <span wire:loading wire:target="submitLoanApplication" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {{ __('Memproses...') }}
                </span>
            </button>
        </div>
        <div class="text-end mt-2">
            <template x-if="!termsScrolled">
                <small class="text-warning fst-italic"><i class="bi bi-exclamation-circle"></i>
                    {{ __('Sila skrol Syarat-syarat Permohonan untuk mengaktifkan butang Hantar.') }}</small>
            </template>
            <template x-if="termsScrolled && !applicantConfirmation">
                <small class="text-warning fst-italic"><i class="bi bi-exclamation-circle"></i>
                    {{ __('Sila buat Pengesahan Pemohon untuk mengaktifkan butang Hantar.') }}</small>
            </template>
        </div>
    </form>

    {{-- Footer for Document Number and Effective Date --}}
    <div class="text-center text-muted small mt-5 pt-3 border-top">
        <p class="mb-0">{{ __('No. Dokumen: PK.(S).KPK.08.(L3) Pin. 1') }}</p>
        <p class="mb-0">{{ __('Tarikh Kuatkuasa: 1/1/2024') }}</p>
    </div>

</div>
