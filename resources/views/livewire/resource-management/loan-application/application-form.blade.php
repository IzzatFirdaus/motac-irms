{{-- resources/views/livewire/resource-management/loan-application/application-form.blade.php (Assumed Path) --}}
<div>
    @section('title', __('Borang Permohonan Peminjaman Peralatan ICT'))

    @include('_partials._alerts.alert-general') {{-- Ensure this uses MOTAC themed alerts --}}

    <form wire:submit.prevent="submitLoanApplication">

        {{-- BAHAGIAN 1: MAKLUMAT PEMOHON --}}
        <div class="card motac-card mb-4"> {{-- Using .motac-card --}}
            <div class="card-header motac-card-header p-3"> {{-- Using .motac-card-header --}}
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        {{-- Iconography: Design Language 2.4 --}}
                        <i class="bi bi-person-lines-fill me-2 fs-5"></i>
                        {{ __('BAHAGIAN 1: MAKLUMAT PEMOHON') }}
                    </h2>
                    <small class="text-muted">{{ __('* WAJIB diisi') }}</small>
                </div>
            </div>
            <div class="card-body p-4 motac-card-body"> {{-- Using .motac-card-body --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Nama Penuh') }}<span class="text-danger">*</span></label>
                        {{-- Consider bg-light-subtle for readonly fields for better theme integration --}}
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">{{ $this->applicantName ?? Auth::user()->name }}</p>
                    </div>

                    <div class="col-md-6">
                        <label for="applicant_phone" class="form-label fw-medium">{{ __('No.Telefon') }}<span class="text-danger">*</span></label>
                        <input type="text" id="applicant_phone" wire:model.defer="applicant_phone"
                               class="form-control @error('applicant_phone') is-invalid @enderror" {{-- Ensure form-control is MOTAC themed --}}
                               placeholder="{{ __('Cth: 012-3456789') }}">
                        @error('applicant_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Jawatan & Gred') }}<span class="text-danger">*</span></label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">{{ $this->applicantPositionAndGrade ?? (optional(Auth::user()->position)->name . ' (' . optional(Auth::user()->grade)->name . ')') }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">{{ __('Bahagian/Unit') }}<span class="text-danger">*</span></label>
                        <p class="form-control-plaintext p-2 border rounded bg-light-subtle mb-0">{{ $this->applicantDepartment ?? optional(Auth::user()->department)->name }}</p>
                    </div>

                    <div class="col-md-12">
                        <label for="purpose" class="form-label fw-medium">{{ __('Tujuan Permohonan') }}<span class="text-danger">*</span></label>
                        <textarea id="purpose" wire:model.defer="purpose" rows="3"
                                  class="form-control @error('purpose') is-invalid @enderror"
                                  placeholder="{{ __('Nyatakan tujuan permohonan peralatan ICT...') }}"></textarea>
                        @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="location" class="form-label fw-medium">{{ __('Lokasi Penggunaan Peralatan') }}<span class="text-danger">*</span></label>
                        <input type="text" id="location" wire:model.defer="location"
                               class="form-control @error('location') is-invalid @enderror"
                               placeholder="{{ __('Cth: Bilik Mesyuarat Utama, Aras 10') }}">
                        @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="return_location" class="form-label fw-medium">{{ __('Lokasi Dijangka Pulang') }}</label>
                        <input type="text" id="return_location" wire:model.defer="return_location"
                               class="form-control @error('return_location') is-invalid @enderror"
                               placeholder="{{ __('Cth: Kaunter BPM (Jika berbeza)') }}">
                        @error('return_location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="loan_start_date" class="form-label fw-medium">{{ __('Tarikh Pinjaman') }}<span class="text-danger">*</span></label>
                        <input type="datetime-local" id="loan_start_date" wire:model.defer="loan_start_date"
                               class="form-control @error('loan_start_date') is-invalid @enderror">
                        @error('loan_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="loan_end_date" class="form-label fw-medium">{{ __('Tarikh Dijangka Pulang') }}<span class="text-danger">*</span></label>
                        <input type="datetime-local" id="loan_end_date" wire:model.defer="loan_end_date"
                               class="form-control @error('loan_end_date') is-invalid @enderror">
                        @error('loan_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                     {{-- Iconography: Design Language 2.4 --}}
                    <i class="bi bi-person-check-fill me-2 fs-5"></i>
                    {{ __('BAHAGIAN 2: MAKLUMAT PEGAWAI BERTANGGUNGJAWAB') }}
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="form-check mb-3">
                    <input id="applicant_is_responsible_officer" wire:model.live="applicant_is_responsible_officer" type="checkbox" class="form-check-input"> {{-- Ensure form-check-input is MOTAC themed --}}
                    <label for="applicant_is_responsible_officer" class="form-check-label fw-medium">{{ __('Sila tandakan jika Pemohon adalah Pegawai Bertanggungjawab.') }}</label>
                </div>

                @if(!$applicant_is_responsible_officer)
                    <p class="text-muted mb-3 fst-italic">
                        {{ __('Bahagian ini hanya perlu diisi jika Pegawai Bertanggungjawab bukan Pemohon.') }}
                    </p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="responsible_officer_name" class="form-label fw-medium">{{ __('Nama Penuh') }}<span class="text-danger">*</span></label>
                            <input type="text" id="responsible_officer_name" wire:model.defer="responsible_officer_name"
                                   class="form-control @error('responsible_officer_name') is-invalid @enderror">
                            @error('responsible_officer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="responsible_officer_position_grade" class="form-label fw-medium">{{ __('Jawatan & Gred') }}<span class="text-danger">*</span></label>
                            <input type="text" id="responsible_officer_position_grade" wire:model.defer="responsible_officer_position_grade"
                                   class="form-control @error('responsible_officer_position_grade') is-invalid @enderror">
                            @error('responsible_officer_position_grade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="responsible_officer_phone" class="form-label fw-medium">{{ __('No.Telefon') }}<span class="text-danger">*</span></label>
                            <input type="text" id="responsible_officer_phone" wire:model.defer="responsible_officer_phone"
                                   class="form-control @error('responsible_officer_phone') is-invalid @enderror">
                            @error('responsible_officer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- BAHAGIAN 3: MAKLUMAT PERALATAN --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                        {{-- Iconography: Design Language 2.4 --}}
                        <i class="bi bi-tools me-2 fs-5"></i>
                        {{ __('BAHAGIAN 3: MAKLUMAT PERALATAN') }}
                    </h2>
                    <button type="button" wire:click="addLoanItem"
                            class="btn btn-sm btn-outline-primary"> {{-- Ensure btn-outline-primary is MOTAC themed --}}
                        {{-- Iconography: Design Language 2.4. Changed from ti-plus --}}
                        <i class="bi bi-plus-lg me-1"></i> {{ __('Tambah Peralatan') }}
                    </button>
                </div>
            </div>
            <div class="card-body p-4 motac-card-body">
                <div class="list-group">
                    @foreach ($loan_application_items as $index => $item)
                        <div wire:key="loan_item_{{ $index }}" class="list-group-item mb-3 p-3 border rounded bg-light-subtle"> {{-- Using bg-light-subtle for item differentiation --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                 <h3 class="h6 mb-0 fw-medium text-primary">{{ __('Peralatan #') }}{{ $index + 1 }}</h3>
                                @if (count($loan_application_items) > 1)
                                    <button type="button" wire:click="removeLoanItem({{ $index }})" title="{{__('Buang Peralatan')}}"
                                            class="btn btn-sm btn-icon btn-text-danger p-0"> {{-- Using btn-icon for consistent icon button styling --}}
                                        {{-- Iconography: Design Language 2.4. Changed from ti-circle-x --}}
                                        <i class="bi bi-x-circle-fill fs-5"></i>
                                    </button>
                                @endif
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="item_{{ $index }}_equipment_type" class="form-label fw-medium">{{ __('Jenis Peralatan') }} <span class="text-danger">*</span></label>
                                    <input type="text" id="item_{{ $index }}_equipment_type" wire:model.defer="loan_application_items.{{ $index }}.equipment_type"
                                           class="form-control @error('loan_application_items.'.$index.'.equipment_type') is-invalid @enderror"
                                           placeholder="{{ __('Cth: Komputer Riba, Projektor LCD') }}">
                                    @error('loan_application_items.'.$index.'.equipment_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="item_{{ $index }}_quantity_requested" class="form-label fw-medium">{{ __('Kuantiti') }} <span class="text-danger">*</span></label>
                                    <input type="number" id="item_{{ $index }}_quantity_requested" wire:model.defer="loan_application_items.{{ $index }}.quantity_requested" min="1"
                                           class="form-control @error('loan_application_items.'.$index.'.quantity_requested') is-invalid @enderror">
                                    @error('loan_application_items.'.$index.'.quantity_requested') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label for="item_{{ $index }}_notes" class="form-label fw-medium">{{ __('Catatan') }}</label>
                                    <input type="text" id="item_{{ $index }}_notes" wire:model.defer="loan_application_items.{{ $index }}.notes"
                                           class="form-control @error('loan_application_items.'.$index.'.notes') is-invalid @enderror"
                                           placeholder="{{ __('Cth: Perisian khas diperlukan, Keperluan segera, dll.') }}">
                                    @error('loan_application_items.'.$index.'.notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if (empty($loan_application_items))
                        <div class="text-center py-3 border rounded bg-light-subtle">
                             {{-- Iconography: Design Language 2.4 --}}
                            <i class="bi bi-info-circle fs-2 text-muted mb-2 d-block"></i>
                            <p class="text-muted mb-0">{{ __('Sila tambah sekurang-kurangnya satu item peralatan.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- BAHAGIAN 4: PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB) --}}
        <div class="card motac-card mb-4">
            <div class="card-header motac-card-header p-3">
                <h2 class="h5 mb-0 fw-semibold d-flex align-items-center">
                    {{-- Iconography: Design Language 2.4 --}}
                    <i class="bi bi-patch-check-fill me-2 fs-5"></i>
                    {{ __('BAHAGIAN 4: PENGESAHAN PEMOHON (PEGAWAI BERTANGGUNGJAWAB)') }}
                </h2>
            </div>
            <div class="card-body p-4 motac-card-body">
                {{-- Design Language 4.2 style certification block --}}
                <div class="certification-block bg-light-subtle p-3 rounded border mb-3">
                    <div class="form-check mb-2">
                        <input id="applicant_confirmation" wire:model.defer="applicant_confirmation" type="checkbox" value="1"
                               class="form-check-input @error('applicant_confirmation') is-invalid @enderror">
                        <label for="applicant_confirmation" class="form-check-label fw-medium">
                            {{ __('Saya dengan ini mengesahkan dan memperakukan bahawa semua peralatan yang dipinjam adalah untuk kegunaan rasmi dan berada di bawah tanggungjawab dan penyeliaan saya sepanjang tempoh tersebut.') }} <span class="text-danger">*</span>
                        </label>
                        @error('applicant_confirmation') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
                <p class="form-text small"> {{-- Use small for consistency if form-text is not styled as 12px by default --}}
                    {{__('Peringatan: Sila semak dan periksa kesempurnaan peralatan semasa mengambil dan sebelum memulangkan peralatan yang dipinjam. Kehilangan dan kekurangan pada peralatan semasa pemulangan adalah dibawah tanggungjawab pemohon.')}}
                </p>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="text-end pt-3">
            <button type="button" wire:click="resetForm"
                    class="btn btn-outline-secondary text-uppercase me-2"> {{-- Ensure btn-outline-secondary is MOTAC themed --}}
                {{-- Iconography: Design Language 2.4. Changed from ti-refresh --}}
                <i class="bi bi-arrow-counterclockwise me-1"></i> {{ __('Reset Borang') }}
            </button>
            <button type="submit" wire:loading.attr="disabled" wire:target="submitLoanApplication"
                    class="btn btn-primary text-uppercase"> {{-- Ensure btn-primary is MOTAC themed --}}
                <span wire:loading.remove wire:target="submitLoanApplication">
                    {{-- Iconography: Design Language 2.4. Changed from ti-send --}}
                    <i class="bi bi-send-check-fill me-1"></i> {{ __('Hantar Permohonan') }}
                </span>
                <span wire:loading wire:target="submitLoanApplication" class="d-inline-flex align-items-center">
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {{ __('Memproses...') }}
                </span>
            </button>
        </div>
    </form>
</div>
