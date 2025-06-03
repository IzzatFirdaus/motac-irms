{{-- resources/views/livewire/bpm/outstanding-loans.blade.php --}}
<div>
    {{-- The title is set in your Livewire component's render method using ->title() --}}
    {{-- The layout is set by #[Layout('layouts.app')] in your Livewire component --}}

    <div class="container py-4"> {{-- Consider container-fluid for full width as per general MOTAC internal tool design --}}
        <h2 class="h2 fw-bold text-dark mb-4">{{ __('Senarai Pinjaman Menunggu Pengeluaran') }}</h2>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{-- Iconography: Design Language 2.4 (Bootstrap Icons are good) --}}
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{-- Iconography: Design Language 2.4 --}}
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Search Input --}}
        {{-- Apply .motac-card if it provides specific MOTAC theme styling over default .card shadow-sm --}}
        <div class="card shadow-sm mb-4 motac-card">
            <div class="card-body motac-card-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="searchOutstandingLoansBPM"
                            class="form-label visually-hidden">{{ __('Carian Permohonan') }}</label>
                        <input wire:model.live.debounce.300ms="searchTerm" id="searchOutstandingLoansBPM" type="text"
                            placeholder="{{ __('Cari ID Permohonan, Tujuan, Nama Pemohon...') }}" class="form-control"> {{-- Ensure form-control is MOTAC themed --}}
                    </div>
                </div>
            </div>
        </div>

        <div wire:loading.delay.long class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"> {{-- Ensure text-primary uses MOTAC primary color --}}
                <span class="visually-hidden">{{ __('Memuatkan...') }}</span>
            </div>
            <p class="mt-2">{{ __('Memuatkan senarai permohonan...') }}</p>
        </div>

        <div wire:loading.remove>
            @if ($applications->isEmpty())
                <div class="alert alert-info" role="alert"> {{-- Ensure alert-info is MOTAC themed --}}
                    <i class="bi bi-info-circle-fill me-2"></i>
                    {{ __('Tiada permohonan pinjaman menunggu pengeluaran pada masa ini atau sepadan dengan carian anda.') }}
                </div>
            @else
                {{-- Apply .motac-card if preferred over .card.shadow-sm --}}
                <div class="card shadow-sm motac-card">
                    <div class="card-body p-0 motac-card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light"> {{-- Ensure table-light uses MOTAC theme surface/bg color --}}
                                    <tr>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Permohonan #') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Pemohon') }}
                                        </th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Tujuan') }}
                                        </th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Tarikh Dijangka Pulang') }}</th>
                                        <th scope="col" class="small text-uppercase text-muted fw-medium px-3 py-2">
                                            {{ __('Item Diluluskan') }}</th>
                                        <th scope="col"
                                            class="small text-uppercase text-muted fw-medium text-end px-3 py-2">
                                            {{ __('Tindakan') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($applications as $application)
                                        <tr wire:key="bpm-outstanding-loan-{{ $application->id }}">
                                            <td class="align-middle px-3 py-2">
                                                <a href="{{ route('loan-applications.show', $application->id) }}">{{ $application->id }}</a>
                                            </td>
                                            <td class="align-middle px-3 py-2">
                                                @if ($application->user)
                                                    <a href="{{ route('settings.users.show', $application->user->id) }}">
                                                        {{ $application->user->name ?? __('N/A') }}
                                                    </a>
                                                @else
                                                    {{__('N/A')}}
                                                @endif
                                            </td>
                                            <td class="align-middle px-3 py-2"
                                                style="white-space: normal; min-width: 250px;">
                                                {{ Str::limit($application->purpose, 70) }}</td>
                                            <td class="align-middle px-3 py-2">
                                                {{ $application->loan_end_date?->translatedFormat(config('app.date_format_my_short', 'd M Y')) ?? __('N/A') }}
                                            </td>
                                            <td class="align-middle small px-3 py-2">
                                                @if ($application->applicationItems->where('quantity_approved', '>', 0)->isNotEmpty())
                                                    <ul class="list-unstyled mb-0 ps-0">
                                                        @foreach ($application->applicationItems->where('quantity_approved', '>', 0) as $item)
                                                            <li>
                                                                {{ $item->equipment_type ? \App\Models\Equipment::$ASSET_TYPES_LABELS[$item->equipment_type] ?? Str::title(str_replace('_', ' ', $item->equipment_type)) : __('N/A') }}
                                                                ({{ __('Diluluskan') }}:
                                                                {{ $item->quantity_approved ?? __('N/A') }})
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end align-middle px-3 py-2">
                                                @can('processIssuance', $application)
                                                    <a href="{{ route('resource-management.bpm.loan-transactions.issue.form', $application->id) }}"
                                                        class="btn btn-sm btn-primary d-inline-flex align-items-center"> {{-- btn-primary should be MOTAC themed --}}
                                                        <i class="bi bi-box-arrow-up-right me-1"></i> {{-- Already Bootstrap Icon --}}
                                                        {{ __('Keluarkan Peralatan') }}
                                                    </a>
                                                @else
                                                    <span class="text-muted fst-italic">{{ __('Tiada tindakan') }}</span>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if ($applications->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $applications->links() }} {{-- Ensure pagination view is Bootstrap 5 styled --}}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
