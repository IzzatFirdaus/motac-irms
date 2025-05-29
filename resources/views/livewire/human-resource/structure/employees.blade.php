<div>

@php
  // Standardized helper path
  $configData = \App\Helpers\Helpers::appClasses();
@endphp

@section('title', 'Employees - Structure') {{-- MOTAC Design refers to 'Users' or 'Staff'. 'Employees' is fine for HRMS context. --}}

@section('page-style')
  <style>
    .btn-tr {
      opacity: 0;
    }

    tr:hover .btn-tr {
      display: inline-block;
      opacity: 1;
    }

    tr:hover .td-link a { /* Ensure links in td-link also change color on row hover if desired */
      color: #7367f0 !important;
    }
    .avatar img {
        object-fit: cover; /* Ensures avatar images are not distorted */
    }
  </style>
@endsection

<div class="demo-inline-spacing">
  {{-- The modal-employee should align with MOTAC User fields: title, name, identification_number, passport_number,
       position_id, grade_id, department_id, level, mobile_number, email, motac_email, user_id_assigned,
       service_status, appointment_type, previous_department_name, previous_department_email, status
  --}}
  <button wire:click='showCreateEmployeeModal' type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#employeeModal">
    <span class="ti-xs ti ti-plus me-1"></span>{{ __('Add New Employee') }}
  </button>
</div>
<br>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="card-title m-0 me-2"><i class="ti ti-users ti-lg text-info me-3"></i>{{ __('Employees') }}</h5>
    <div class="col-md-4 col-sm-6"> {{-- Adjusted column for better responsiveness --}}
      <input wire:model.live.debounce.300ms="searchTerm" autofocus type="text" class="form-control" placeholder="{{ __('Search (ID, Name, NRIC, Email...)') }}">
    </div>
  </div>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover"> {{-- Added table-hover for better UX --}}
      <thead>
        <tr>
          <th class="col-1">{{ __('ID') }}</th>
          <th class="col-3">{{ __('Name') }}</th>
          <th class="col-2">{{ __('Department') }}</th>    <th class="col-2">{{ __('Position') }}</th>      <th class="col-2">{{ __('Mobile') }}</th>
          <th class="col-1">{{ __('Status') }}</th>
          <th class="col-1 text-center">{{ __('Actions') }}</th> {{-- Centered Actions --}}
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @forelse($employees as $employee)
        <tr>
          <td>{{ $employee->id }}</td>
          <td>
            <div class="d-flex justify-content-start align-items-center user-name">
              <div class="avatar-wrapper me-3">
                <div class="avatar avatar-sm">
                  {{-- Used Storage::url for consistency, added fallback --}}
                  <img src="{{ $employee->profile_photo_path ? Storage::disk('public')->url($employee->profile_photo_path) : asset('assets/img/avatars/default-avatar.png') }}" alt="Avatar" class="rounded-circle">
                </div>
              </div>
              <div class="d-flex flex-column">
                <a href="{{ route('structure-employees-info', $employee->id) }}" class="text-body text-truncate">
                  <span class="fw-semibold">{{ $employee->fullName ?? $employee->name }}</span> {{-- Assuming fullName accessor or use $employee->name --}}
                </a>
                <small class="text-muted">{{ $employee->email }}</small> {{-- Displaying email for more info --}}
              </div>
            </div>
          </td>
          <td>
            {{-- Assuming $employee->department relationship exists --}}
            {{ $employee->department->name ?? __('N/A') }}
          </td>
          <td>
            {{-- Assuming $employee->position relationship exists --}}
            {{ $employee->position->name ?? __('N/A') }}
          </td>
          <td style="direction: ltr">{{ $employee->mobile_number ? '+963 ' . number_format($employee->mobile_number, 0, '', ' ') : __('N/A') }}</td>
          <td>
            {{-- Using User model constant for status as per MOTAC design --}}
            @if ($employee->status == \App\Models\User::STATUS_ACTIVE)
              <span class="badge bg-label-success me-1">{{ __('Active') }}</span>
            @elseif ($employee->status == \App\Models\User::STATUS_INACTIVE)
              <span class="badge bg-label-danger me-1">{{ __('Inactive') }}</span>
            @else
              <span class="badge bg-label-secondary me-1">{{ __(Str::title(str_replace('_', ' ', $employee->status))) }}</span>
            @endif
          </td>
          <td class="text-center"> {{-- Centered Actions --}}
            <button type="button" class="btn btn-sm btn-icon btn-outline-secondary waves-effect me-1" {{-- Removed btn-tr for visibility --}}
                    wire:click='showEditEmployeeModal({{ $employee }})' data-bs-toggle="modal" data-bs-target="#employeeModal"
                    title="{{__('Edit')}}">
              <span class="ti ti-pencil"></span>
            </button>
            <button type="button" class="btn btn-sm btn-icon btn-outline-danger waves-effect" {{-- Removed btn-tr for visibility --}}
                    wire:click.prevent='confirmDeleteEmployee({{ $employee->id }})'
                    title="{{__('Delete')}}">
              <span class="ti ti-trash"></span>
            </button>
            @if ($confirmedId === $employee->id)
            <button wire:click.prevent='deleteEmployee({{ $employee }})' type="button" class="btn btn-xs btn-danger waves-effect waves-light ms-1">{{ __('Sure?') }}</button>
          @endif
          </td>
        </tr>
        @empty
        <tr>
          {{-- Adjusted colspan --}}
          <td colspan="7">
            <div class="mt-2 mb-2" style="text-align: center">
                <h3 class="mb-1 mx-2">{{ __('Oopsie-doodle!') }}</h3>
                <p class="mb-4 mx-2">
                  {{ __('No data found, please sprinkle some data in my virtual bowl, and let the fun begin!') }}
                </p>
                <button class="btn btn-label-primary mb-4" wire:click='showCreateEmployeeModal' data-bs-toggle="modal" data-bs-target="#employeeModal">
                    {{ __('Add New Employee') }}
                  </button>
                <div>
                  <img src="{{ asset('assets/img/illustrations/page-misc-under-maintenance.png') }}" width="200" class="img-fluid">
                </div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="row mt-4 px-3"> {{-- Added padding for pagination --}}
    {{ $employees->links() }}
  </div>

</div>

{{-- Modal --}}
@include('_partials/_modals/modal-employee')
</div>
