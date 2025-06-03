{{-- resources/views/livewire/employee-info.blade.php --}}
<div>

@php
  // Standardized helper path
  $configData = \App\Helpers\Helpers::appClasses();
@endphp

@section('title', ($employee->fullName ?? $employee->name) . ' - ' . __('Maklumat Kakitangan')) {{-- Dynamic Title, Design Language 1.2: BM First --}}

@section('page-style')
  <style>
    /* Design Language 2.2: Noto Sans should be global. These are reinforcements or specific overrides. */
    .timeline-icon { cursor: pointer; opacity: 0; transition: opacity 0.2s ease-in-out; }
    .timeline-row:hover .timeline-icon { display: inline-block; opacity: 1; }
    .btn-tr { opacity: 0; transition: opacity 0.2s ease-in-out; } /* For transparent buttons appearing on row hover */
    tr:hover .btn-tr { display: inline-block; opacity: 1; }
    tr:hover .td-link a { color: var(--motac-primary) !important; } /* Ensure MOTAC primary for links on hover */

    .user-profile-img { width: 120px; height: 120px; object-fit: cover; border: 3px solid var(--motac-surface, #FFFFFF); box-shadow: 0 2px 4px rgba(0,0,0,0.1); } /* Use MOTAC surface var */
    .list-inline-item i.bi { vertical-align: middle; font-size: 0.95rem; {{-- Adjusted for Bootstrap Icons --}} }
  </style>
@endsection

{{-- Alerts --}}
@include('_partials._alerts/alert-general')

<div class="row">
  <div class="col-12">
    <div class="card mb-4 motac-card"> {{-- Added .motac-card --}}
      {{-- User Profile Header Banner (Optional) --}}
      {{-- <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div> --}}
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ $employee->profile_photo_path ? Storage::disk("public")->url($employee->profile_photo_path) : asset('assets/img/avatars/default-avatar.png') }}"
               alt="{{ __('Foto Profil') }} {{ $employee->name }}" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>{{ $employee->title ? $employee->title . ' ' : '' }}{{ $employee->name }}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2 small">
                {{-- Iconography: Design Language 2.4. Changed all ti-* to bi-*. --}}
                <li class="list-inline-item"><span class="badge rounded-pill bg-label-primary"><i class="bi bi-person-badge me-1"></i>{{ __('ID') }}: {{ $employee->id }}</span></li>
                @if($employee->department)
                  <li class="list-inline-item"><i class="bi bi-building me-1"></i>{{ $employee->department->name }}</li>
                @endif
                @if($employee->position)
                  <li class="list-inline-item"><i class="bi bi-briefcase me-1"></i>{{ $employee->position->name }}</li>
                @endif
                @if($employee->grade)
                  <li class="list-inline-item"><i class="bi bi-star me-1"></i>{{ __('Gred') }}: {{ $employee->grade->name }}</li>
                @endif
                @if($employee->user_id_assigned)
                    <li class="list-inline-item"><i class="bi bi-upc-scan me-1"></i>{{ __('ID Pengguna') }}: {{ $employee->user_id_assigned }}</li>
                @endif
                {{-- Fields like join_at_short_form, worked_years are accessors, likely from HRMS template. --}}
                {{-- If MOTAC needs them, ensure data source (e.g., service_start_date) and accessors are defined in User model. --}}
              </ul>
            </div>
            {{-- Ensure $employee->status uses constants from App\Models\User as per MOTAC design --}}
            <button wire:click='toggleActive' type="button" class="btn btn-sm @if ($employee->status == \App\Models\User::STATUS_ACTIVE) btn-success @else btn-danger @endif">
              {{-- Iconography: Design Language 2.4. --}}
              <span class="bi @if ($employee->status == \App\Models\User::STATUS_ACTIVE) bi-person-check-fill @else bi-person-x-fill @endif me-1"></span>
              @if ($employee->status == \App\Models\User::STATUS_ACTIVE) {{ __('Aktif') }} @else {{ __('Tidak Aktif') }} @endif
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
    <div class="card mb-4 motac-card">
      <div class="card-body motac-card-body">
        <small class="card-text text-uppercase text-muted">{{__('Perihal')}}</small>
        <ul class="list-unstyled mb-4 mt-3">
          {{-- Iconography: Design Language 2.4. Changed all ti-* to bi-*. --}}
          <li class="d-flex align-items-center mb-3"><i class="bi bi-person-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Nama Penuh') }}:</span> <span>{{ $employee->title ? $employee->title . ' ' : '' }}{{ $employee->name }}</span></li>
          @if($employee->identification_number)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-person-vcard ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('No. KP') }}:</span> <span>{{ $employee->identification_number }}</span></li>
          @elseif($employee->passport_number)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-globe-americas ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('No. Pasport') }}:</span> <span>{{ $employee->passport_number }}</span></li>
          @endif
          <li class="d-flex align-items-center mb-3"><i class="bi bi-check-circle-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Status') }}:</span> <span class="badge bg-label-{{ $employee->status == \App\Models\User::STATUS_ACTIVE ? 'success' : 'danger' }}">{{ __(Str::title($employee->status)) }}</span></li>
          @if($employee->department)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-building ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Jabatan') }}:</span> <span>{{ $employee->department->name }}</span></li>
          @endif
          @if($employee->position)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-briefcase-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Jawatan') }}:</span> <span>{{ $employee->position->name }}</span></li>
          @endif
          @if($employee->grade)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-star-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Gred') }}:</span> <span>{{ $employee->grade->name }}</span></li>
          @endif
          @if($employee->level)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-layers-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Aras') }}:</span> <span>{{ $employee->level }}</span></li>
          @endif
        </ul>
        <small class="card-text text-uppercase text-muted">{{__('Hubungi')}}</small>
        <ul class="list-unstyled mb-4 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="bi bi-telephone-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Tel. Bimbit') }}:</span> <span style="direction: ltr">{{ $employee->mobile_number ? '+60 ' . $employee->mobile_number : __('N/A') }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="bi bi-envelope-at-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('E-mel Peribadi') }}:</span> <span>{{ $employee->email }}</span></li>
          @if($employee->motac_email)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-envelope-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('E-mel MOTAC') }}:</span> <span>{{ $employee->motac_email }}</span></li>
          @endif
        </ul>
        <small class="card-text text-uppercase text-muted">{{__('Maklumat Perkhidmatan')}}</small>
        <ul class="list-unstyled mb-0 mt-3">
          @if($employee->service_status)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-tag-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Status Perkhidmatan') }}:</span> <span>{{ __(Str::title(str_replace('_', ' ', $employee->service_status))) }}</span></li>
          @endif
          @if($employee->appointment_type)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-arrow-down-up ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Jenis Lantikan') }}:</span> <span>{{ __(Str::title(str_replace('_', ' ', $employee->appointment_type))) }}</span></li>
          @endif
          @if($employee->service_start_date)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-calendar-event-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Mula Khidmat') }}:</span> <span>{{ \Carbon\Carbon::parse($employee->service_start_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) }}</span></li>
          @endif
          @if($employee->service_end_date)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-calendar-x-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Tamat Khidmat') }}:</span> <span>{{ \Carbon\Carbon::parse($employee->service_end_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) }}</span></li>
          @endif
           @if($employee->previous_department_name)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-building-dash ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Jabatan Terdahulu') }}:</span> <span>{{ $employee->previous_department_name }}</span></li>
          @endif
          @if($employee->previous_department_email)
            <li class="d-flex align-items-center mb-3"><i class="bi bi-envelope-paper-fill ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('E-mel Jab. Terdahulu') }}:</span> <span>{{ $employee->previous_department_email }}</span></li>
          @endif
        </ul>
      </div>
    </div>
  </div>
  <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
    {{-- Assets Section: This section is likely from HRMS template.
         For MOTAC, this could be adapted to show "Currently Loaned ICT Equipment"
         based on LoanApplication and LoanTransaction models.
         The current structure ($employeeAssets, $asset->getCategory, etc.) implies a different model.
         Confirm if this specific asset tracking is needed or if it should show ICT loans.
    --}}
    <div class="card mb-4 motac-card">
      <div class="card-header d-flex justify-content-between align-items-center motac-card-header">
        {{-- Iconography: Design Language 2.4. Changed ti-box. --}}
        <h5 class="card-title mb-0"><i class="bi bi-box-seam-fill me-2"></i>{{ __('Aset Ditetapkan / Pinjaman ICT') }}</h5>
        {{-- <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assetModal">{{ __('Tambah Baharu') }}</button> --}}
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>{{ __('ID Aset / Pinjaman') }}</th>
              <th>{{ __('Kategori / Jenis') }}</th>
              <th>{{ __('No. Siri') }}</th>
              <th>{{ __('Tarikh Terima / Pinjam')}}</th>
              <th>{{ __('Tindakan')}}</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($employeeAssetsOrLoans as $item) {{-- Renamed variable for clarity --}}
              <tr>
                {{-- Adapt this to the structure of your $employeeAssetsOrLoans data --}}
                <td class="td-link">
                  {{-- Iconography: Design Language 2.4. Changed ti-tag. --}}
                  <a href="#"> <i class="bi bi-tag-fill text-danger me-2"></i><strong>{{ $item->asset_id ?? $item->loan_id ?? $item->id }}</strong></a>
                </td>
                <td>{{ $item->category_name ?? $item->equipment_type ?? __('N/A') }}</td>
                <td>{{ $item->serial_number ?? __('N/A') }}</td>
                <td><span class="badge rounded-pill bg-label-secondary">{{ $item->handed_date ?? ($item->loan_start_date ? \Carbon\Carbon::parse($item->loan_start_date)->translatedFormat(config('app.date_format_my')) : __('N/A')) }}</span></td>
                <td>
                  {{-- Actions depend on whether these are general assets or ICT loans --}}
                  {{-- Example actions with Bootstrap Icons --}}
                  {{--
                  <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect me-1" title="{{__('View Details')}}">
                    <i class="bi bi-eye"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect" title="{{__('Edit')}}"
                    wire:click.prevent='showEditAssetModal({{ $item }})' data-bs-toggle="modal" data-bs-target="#assetModal">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  --}}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5">
                  <div class="text-center p-4">
                    {{-- Iconography: Design Language 2.4 --}}
                    <i class="bi bi-folder-x fs-1 text-muted mb-2 d-block"></i>
                    {{-- Design Language 1.4: Formal Tone --}}
                    <h5 class="mb-1 mx-2">{{ __('Tiada Aset atau Pinjaman Ditemui') }}</h5>
                    <p class="mb-0 mx-2 text-muted">
                    {{ __('Kakitangan ini tidak mempunyai aset yang ditetapkan atau pinjaman ICT aktif pada masa ini.') }}
                    </p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Timeline Section: This is an HRMS feature. --}}
    <div class="card card-action mb-4 motac-card">
      <div class="card-header align-items-center motac-card-header">
        {{-- Iconography: Design Language 2.4. Changed ti-list-details. --}}
        <h5 class="card-action-title mb-0"><i class="bi bi-list-ol me-2"></i>{{ __('Sejarah Jawatan / Garis Masa') }}</h5>
        <div class="card-action-element">
          <div class="dropdown">
            {{-- Iconography: Design Language 2.4. Changed ti-dots-vertical. --}}
            <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a wire:click='showStoreTimelineModal()' class="dropdown-item" data-bs-toggle="modal" data-bs-target="#timelineModal">
                  {{-- Iconography: Design Language 2.4 --}}
                  <i class="bi bi-plus-circle me-1"></i> {{ __('Tambah Entri Baharu') }}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card-body pb-0 motac-card-body">
        @if($employeeTimelines && $employeeTimelines->count() > 0)
        <ul class="timeline ms-1 mb-0">
          @foreach ($employeeTimelines as $timeline)
            <li class="timeline-item timeline-item-transparent @if ($loop->last) border-0 @endif">
              <span class="timeline-point @if ($loop->first) timeline-point-primary @else timeline-point-info @endif"></span>
              <div class="timeline-event">
                <div class="timeline-header">
                  <div class="timeline-row d-flex m-0 align-items-center">
                    <h6 class="mb-0 me-2">{{ $timeline->position->name ?? __('Posisi Tidak Dinyatakan') }}</h6>
                    <small class="text-muted me-auto">@ {{ $timeline->department->name ?? ($timeline->center->name ?? __('Unit/Pusat Tidak Dinyatakan')) }}</small>
                    <div>
                        {{-- Iconography: Design Language 2.4. --}}
                        {{-- <i wire:click='setPresentTimeline({{ $timeline }})' class="timeline-icon text-success bi bi-arrow-clockwise mx-1" title="{{__('Tetapkan Semasa')}}"></i> --}}
                        <i wire:click='showUpdateTimelineModal({{ $timeline }})' class="timeline-icon text-info bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#timelineModal" title="{{__('Sunting')}}"></i>
                        <i wire:click='confirmDeleteTimeline({{ $timeline->id }})' class="timeline-icon text-danger bi bi-trash3-fill mx-1" title="{{__('Padam')}}"></i>
                        @if ($confirmedTimelineDeleteId === $timeline->id) {{-- Assuming a distinct confirmed ID variable --}}
                          <button wire:click.prevent='deleteTimeline({{ $timeline }})' type="button"
                            class="btn btn-xs btn-danger ms-1">{{ __('Pasti?') }}
                          </button>
                        @endif
                    </div>
                  </div>
                  <small class="text-muted">
                    {{ \Carbon\Carbon::parse($timeline->start_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) }}
                    @if ($timeline->end_date == null) - {{ __('Kini') }} @else - {{ \Carbon\Carbon::parse($timeline->end_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) }} @endif
                  </small>
                </div>
                @if($timeline->description)
                    <p class="mb-2">{{ $timeline->description }}</p>
                @endif
              </div>
            </li>
          @endforeach
        </ul>
        @else
        <div class="text-center p-3">
            {{-- Iconography: Design Language 2.4 --}}
            <i class="bi bi-calendar-x fs-1 text-muted mb-2 d-block"></i>
            <p class="text-muted">{{__('Tiada sejarah jawatan ditemui.')}}</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Modal for Timeline (if used) --}}
{{-- Ensure _partials/_modals/modal-timeline.blade.php is for creating/editing position history (position_id, department_id/center_id, start_date, end_date, description) --}}
@include('_partials/_modals/modal-timeline')

{{-- Scripts --}}
@push('custom-scripts') {{-- Ensure stack name 'custom-scripts' is used in main layout --}}
  @if(session('openTimelineModal'))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
          var timelineModalEl = document.getElementById('timelineModal');
          if(timelineModalEl) { // Check if modal exists
            var timelineModal = new bootstrap.Modal(timelineModalEl);
            timelineModal.show();
          }
      });
    </script>
  @endif
@endpush
</div>
