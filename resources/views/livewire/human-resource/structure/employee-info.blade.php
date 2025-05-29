<div>

@php
  // Standardized helper path
  $configData = \App\Helpers\Helpers::appClasses();
@endphp

@section('title', ($employee->fullName ?? $employee->name) . ' - Employee Info') {{-- Dynamic Title --}}

@section('page-style')
  <style>
    .timeline-icon { cursor: pointer; opacity: 0; transition: opacity 0.2s ease-in-out; }
    .timeline-row:hover .timeline-icon { display: inline-block; opacity: 1; }
    .btn-tr { opacity: 0; transition: opacity 0.2s ease-in-out; }
    tr:hover .btn-tr { display: inline-block; opacity: 1; }
    tr:hover .td-link a { color: var(--bs-primary) !important; } /* Ensure specificity */
    .user-profile-img { width: 120px; height: 120px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .list-inline-item i { vertical-align: middle; }
  </style>
@endsection

{{-- Alerts --}}
@include('_partials/_alerts/alert-general')

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      {{-- User Profile Header Banner (Optional, from HRMS template) --}}
      {{-- <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top">
      </div> --}}
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
          <img src="{{ $employee->profile_photo_path ? Storage::disk("public")->url($employee->profile_photo_path) : asset('assets/img/avatars/default-avatar.png') }}"
               alt="{{ $employee->name }} Profile Photo" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img">
        </div>
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>{{ $employee->title ? $employee->title . ' ' : '' }}{{ $employee->name }}</h4>
              <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2 small">
                <li class="list-inline-item"><span class="badge rounded-pill bg-label-primary"><i class="ti ti-id-badge-2 ti-xs me-1"></i>{{ __('ID') }}: {{ $employee->id }}</span></li>
                @if($employee->department)
                  <li class="list-inline-item"><i class="ti ti-building ti-xs me-1"></i>{{ $employee->department->name }}</li>
                @endif
                @if($employee->position)
                  <li class="list-inline-item"><i class="ti ti-briefcase ti-xs me-1"></i>{{ $employee->position->name }}</li>
                @endif
                @if($employee->grade)
                  <li class="list-inline-item"><i class="ti ti-star ti-xs me-1"></i>{{ __('Gred') }}: {{ $employee->grade->name }}</li>
                @endif
                @if($employee->user_id_assigned)
                    <li class="list-inline-item"><i class="ti ti-scan ti-xs me-1"></i>{{ __('User ID') }}: {{ $employee->user_id_assigned }}</li>
                @endif
                {{-- Fields like join_at_short_form, worked_years are accessors, likely from HRMS template. --}}
                {{-- If MOTAC needs them, ensure data source (e.g., service_start_date) and accessors are defined. --}}
                {{-- <li class="list-inline-item"><i class="ti ti-rocket ti-xs me-1"></i> {{ $employee->join_at_short_form }}</li> --}}
                {{-- <li class="list-inline-item"><i class="ti ti-player-track-next ti-xs me-1"></i> {{ __('Continuity') . ": " . $employee->worked_years . " " . __('years') }}</li> --}}
              </ul>
            </div>
            {{-- Ensure $employee->status uses constants from App\Models\User as per MOTAC design --}}
            <button wire:click='toggleActive' type="button" class="btn btn-sm @if ($employee->status == \App\Models\User::STATUS_ACTIVE) btn-success @else btn-danger @endif waves-effect waves-light">
              <span class="ti @if ($employee->status == \App\Models\User::STATUS_ACTIVE) ti-user-check @else ti-user-x @endif me-1"></span>
              @if ($employee->status == \App\Models\User::STATUS_ACTIVE) {{ __('Active') }} @else {{ __('Inactive') }} @endif
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
    <div class="card mb-4">
      <div class="card-body">
        <small class="card-text text-uppercase text-muted">{{__('About')}}</small>
        <ul class="list-unstyled mb-4 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-user ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Full Name') }}:</span> <span>{{ $employee->title ? $employee->title . ' ' : '' }}{{ $employee->name }}</span></li>
          @if($employee->identification_number)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-id ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('NRIC') }}:</span> <span>{{ $employee->identification_number }}</span></li>
          @elseif($employee->passport_number)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-world ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Passport') }}:</span> <span>{{ $employee->passport_number }}</span></li>
          @endif
          <li class="d-flex align-items-center mb-3"><i class="ti ti-check ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Status') }}:</span> <span class="badge bg-label-{{ $employee->status == \App\Models\User::STATUS_ACTIVE ? 'success' : 'danger' }}">{{ __(Str::title($employee->status)) }}</span></li>
          @if($employee->department)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-building ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Department') }}:</span> <span>{{ $employee->department->name }}</span></li>
          @endif
          @if($employee->position)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-briefcase ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Position') }}:</span> <span>{{ $employee->position->name }}</span></li>
          @endif
          @if($employee->grade)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-star ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Grade') }}:</span> <span>{{ $employee->grade->name }}</span></li>
          @endif
          @if($employee->level)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-layers-intersect ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Level (Aras)') }}:</span> <span>{{ $employee->level }}</span></li>
          @endif
        </ul>
        <small class="card-text text-uppercase text-muted">{{__('Contact')}}</small>
        <ul class="list-unstyled mb-4 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-phone-call ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Mobile') }}:</span> <span style="direction: ltr">{{ $employee->mobile_number ? '+60 ' . $employee->mobile_number : __('N/A') }}</span></li> {{-- Assuming Malaysian context for +60 --}}
          <li class="d-flex align-items-center mb-3"><i class="ti ti-mail ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Personal Email') }}:</span> <span>{{ $employee->email }}</span></li>
          @if($employee->motac_email)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-mail-forward ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('MOTAC Email') }}:</span> <span>{{ $employee->motac_email }}</span></li>
          @endif
        </ul>
        <small class="card-text text-uppercase text-muted">{{__('Service Information')}}</small>
        <ul class="list-unstyled mb-0 mt-3">
          @if($employee->service_status)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-tag ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Service Status') }}:</span> <span>{{ __(Str::title(str_replace('_', ' ', $employee->service_status))) }}</span></li>
          @endif
          @if($employee->appointment_type)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-arrows-transfer-down ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Appointment') }}:</span> <span>{{ __(Str::title(str_replace('_', ' ', $employee->appointment_type))) }}</span></li>
          @endif
          @if($employee->service_start_date)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-calendar-event ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Service Start') }}:</span> <span>{{ \Carbon\Carbon::parse($employee->service_start_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) }}</span></li>
          @endif
          @if($employee->service_end_date)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-calendar-off ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Service End') }}:</span> <span>{{ \Carbon\Carbon::parse($employee->service_end_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) }}</span></li>
          @endif
           @if($employee->previous_department_name)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-building-arch ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Prev. Dept Name') }}:</span> <span>{{ $employee->previous_department_name }}</span></li>
          @endif
          @if($employee->previous_department_email)
            <li class="d-flex align-items-center mb-3"><i class="ti ti-mail-minus ti-sm me-2"></i><span class="fw-semibold mx-1">{{ __('Prev. Dept Email') }}:</span> <span>{{ $employee->previous_department_email }}</span></li>
          @endif
        </ul>
        {{-- User Address: Not in MOTAC User Design. If needed, add to User model/table. --}}
        {{-- <h5 class="card-action-title mb-0 mt-4">{{ __('Address') }}</h5>
        <ul class="list-unstyled mb-0 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-home"></i><span class="fw-bold mx-2">{{ __('Address') }}:</span> <span>{{ $employee->address ?? 'N/A' }}</span></li>
        </ul> --}}

        {{-- Counters (Leaves, Hourly, Delay): These are HRMS specific features. --}}
        {{-- Confirm if these are required for MOTAC. If so, add to User model/table and system design. --}}
        {{-- <h5 class="card-action-title mb-0 mt-4">{{ __('HRMS Counters') }}</h5>
        <ul class="list-unstyled mb-0 mt-3">
          <li class="d-flex align-items-center mb-3"><i class="ti ti-calendar-time"></i><span class="fw-bold mx-2">{{ __('Leaves Balance') }}:</span> <span class="badge bg-label-secondary">{{ $employee->max_leave_allowed ?? 0 }} {{__('Day') }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-alarm"></i><span class="fw-bold mx-2">{{ __('Hourly') }}:</span> <span class="badge bg-label-secondary">{{ $employee->hourly_counter ?? 0 }}</span></li>
          <li class="d-flex align-items-center mb-3"><i class="ti ti-hourglass"></i><span class="fw-bold mx-2">{{ __('Delay') }}:</span> <span class="badge bg-label-secondary">{{ $employee->delay_counter ?? 0 }}</span></li>
        </ul> --}}
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
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0"><i class="ti ti-box ti-sm me-2"></i>{{ __('Assigned Assets / ICT Loans') }}</h5>
        {{-- Add button if there's a modal to assign/manage these assets/loans for the user --}}
        {{-- <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assetModal">{{ __('Add New') }}</button> --}}
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table table-hover">
          <thead>
            <tr>
              {{-- Adjust columns if this shows ICT Loans: e.g., Equipment ID, Type, Serial, Loan Date, Return Date --}}
              <th>{{ __('Asset ID / Loan ID') }}</th>
              <th>{{ __('Category / Type') }}</th>
              <th>{{ __('Serial Number') }}</th>
              <th>{{ __('Handed Date / Loan Date')}}</th>
              <th>{{ __('Actions')}}</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse ($employeeAssetsOrLoans as $item) {{-- Renamed variable for clarity --}}
              <tr>
                {{-- Adapt this to the structure of your $employeeAssetsOrLoans data --}}
                <td class="td-link"><a href="#"> <i class="ti ti-tag ti-sm text-danger me-3"></i><strong>{{ $item->asset_id ?? $item->loan_id ?? $item->id }}</strong></a></td>
                <td>{{ $item->category_name ?? $item->equipment_type ?? 'N/A' }}</td>
                <td>{{ $item->serial_number ?? 'N/A' }}</td>
                <td><span class="badge rounded-pill bg-label-secondary">{{ $item->handed_date ?? $item->loan_start_date ?? 'N/A' }}</span></td>
                <td>
                  {{-- Actions depend on whether these are general assets or ICT loans --}}
                  {{-- <button type="button" class="btn btn-sm btn-tr rounded-pill btn-icon btn-outline-secondary waves-effect">
                    <span class="ti ti-arrow-guide"></span>
                  </button>
                  <button type="button" class="btn btn-sm btn-tr rounded-pill btn-icon btn-outline-secondary waves-effect">
                    <span wire:click.prevent='showEditAssetModal({{ $item }})' data-bs-toggle="modal" data-bs-target="#assetModal" class="ti ti-pencil"></span>
                  </button> --}}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5">
                  <div class="mt-2 mb-2" style="text-align: center">
                    <h3 class="mb-1 mx-2">{{ __('No Assets or Loans Found') }}</h3>
                    <p class="mb-4 mx-2">
                    {{ __('This employee currently has no assigned assets or active ICT loans.') }}
                    </p>
                    {{-- <button class="btn btn-label-primary mb-4" data-bs-toggle="modal" data-bs-target="#assetModal">
                      {{ __('Assign New Asset') }}
                    </button> --}}
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Timeline Section: This is an HRMS feature.
         The concept of "Center" needs clarification for MOTAC.
         If it means Department, the timeline would track changes in Department/Position.
         The structure ($employeeTimelines, $timeline->position, $timeline->center) implies specific models.
    --}}
    <div class="card card-action mb-4">
      <div class="card-header align-items-center">
        <h5 class="card-action-title mb-0"><i class="ti ti-list-details ti-sm me-2"></i>{{ __('Position History / Timeline') }}</h5>
        <div class="card-action-element">
          <div class="dropdown">
            <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical text-muted"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                {{-- This modal should allow adding a new entry to the user's position history --}}
                <a wire:click='showStoreTimelineModal()' class="dropdown-item" data-bs-toggle="modal" data-bs-target="#timelineModal">{{ __('Add New Timeline Entry') }}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="card-body pb-0">
        @if($employeeTimelines && $employeeTimelines->count() > 0)
        <ul class="timeline ms-1 mb-0">
          @foreach ($employeeTimelines as $timeline)
            <li class="timeline-item timeline-item-transparent @if ($loop->last) border-0 @endif">
              <span class="timeline-point @if ($loop->first) timeline-point-primary @else timeline-point-info @endif"></span>
              <div class="timeline-event">
                <div class="timeline-header">
                  <div class="timeline-row d-flex m-0 align-items-center">
                    <h6 class="mb-0 me-2">{{ $timeline->position->name ?? 'N/A Position' }}</h6>
                    {{-- If $timeline->center refers to a Department for MOTAC: --}}
                    <small class="text-muted me-auto">@ {{ $timeline->department->name ?? ($timeline->center->name ?? 'N/A Unit/Center') }}</small>
                    <div>
                        {{-- <i wire:click='setPresentTimeline({{ $timeline }})' class="timeline-icon text-success ti ti-refresh mx-1" title="{{__('Set as Present')}}"></i> --}}
                        <i wire:click='showUpdateTimelineModal({{ $timeline }})' class="timeline-icon text-info ti ti-edit" data-bs-toggle="modal" data-bs-target="#timelineModal" title="{{__('Edit')}}"></i>
                        <i wire:click='confirmDeleteTimeline({{ $timeline->id }})' class="timeline-icon text-danger ti ti-trash mx-1" title="{{__('Delete')}}"></i>
                        @if ($confirmedTimelineDeleteId === $timeline->id) {{-- Assuming a distinct confirmed ID variable --}}
                          <button wire:click.prevent='deleteTimeline({{ $timeline }})' type="button"
                            class="btn btn-xs btn-danger waves-effect waves-light mx-1">{{ __('Sure?') }}
                          </button>
                        @endif
                    </div>
                  </div>
                  <small class="text-muted">
                    {{ \Carbon\Carbon::parse($timeline->start_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) }}
                    @if ($timeline->end_date == null) - {{ __('Present') }} @else - {{ \Carbon\Carbon::parse($timeline->end_date)->translatedFormat(config('app.date_format_my', 'd/m/Y')) }} @endif
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
        <div class="text-center py-3">
            <p class="text-muted">{{__('No position history found.')}}</p>
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
@push('custom-scripts') {{-- Changed from page-script to custom-scripts if that's your convention --}}
  @if(session('openTimelineModal'))
    <script>
      document.addEventListener('DOMContentLoaded', function () {
          var timelineModal = new bootstrap.Modal(document.getElementById('timelineModal'));
          timelineModal.show();
      });
    </script>
  @endif
@endpush
</div>
