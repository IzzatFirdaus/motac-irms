<div>

@php
  // Standardized helper path
  $configData = \App\Helpers\Helpers::appClasses();
@endphp

@section('title', 'Departments - Structure')

<div class="demo-inline-spacing">
  <button wire:click.prevent='showNewDepartmentModal' type="button" class="btn btn-primary"
    data-bs-toggle="modal" data-bs-target="#departmentModal">
    <span class="ti-xs ti ti-plus me-1"></span>{{ __('Add New Department') }}
  </button>
</div>
<br>
<div class="card">
  <h5 class="card-header"><i class="ti ti-building ti-lg text-info me-3"></i>{{ __('Departments') }}</h5>
  <div class="table-responsive text-nowrap">
    <table class="table">
      <thead>
        <tr>
          <th>{{ __('ID') }}</th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Branch Type') }}</th> <th>{{ __('Status') }}</th> <th>{{ __('Members Count') }}</th>
          {{-- Consider adding 'Head of Department' if relevant to display --}}
          {{-- <th>{{ __('Head of Department') }}</th> --}}
          <th>{{ __('Actions') }}</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @forelse($departments as $department)
        <tr>
          <td>{{ $department->id }}</td>
          <td><strong>{{ $department->name }}</strong></td>
          <td>
            {{-- Display branch_type from MOTAC design --}}
            @if($department->branch_type)
              {{ __(Str::title(str_replace('_', ' ', $department->branch_type))) }}
            @else
              {{ __('N/A') }}
            @endif
          </td>
          <td>
            {{-- Display is_active status from MOTAC design --}}
            @if ($department->is_active)
              <span class="badge bg-label-success me-1">{{ __('Active') }}</span>
            @else
              <span class="badge bg-label-danger me-1">{{ __('Inactive') }}</span>
            @endif
          </td>
          <td>
            {{-- This method $this->getMembersCount() is part of the Livewire component --}}
            {{ $this->getMembersCount($department->id) }}
          </td>
          {{-- <td>{{ $department->headOfDepartment ? $department->headOfDepartment->name : 'N/A' }}</td> --}}
          <td>
            <div style="display: flex;"> {{-- Corrected: Removed extra closing div from original --}}
              <div class="dropdown">
                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                <div class="dropdown-menu">
                  {{-- Ensure modal-department allows editing of all MOTAC fields like code, description, branch_type, is_active etc. --}}
                  <a wire:click.prevent='showEditDepartmentModal({{ $department }})' data-bs-toggle="modal" data-bs-target="#departmentModal" class="dropdown-item" href=""><i class="ti ti-pencil me-1"></i> {{ __('Edit') }}</a>
                  <a wire:click.prevent='confirmDeleteDepartment({{ $department->id }})' class="dropdown-item" href=""><i class="ti ti-trash me-1"></i> {{ __('Delete') }}</a>
                </div>
              </div>
              @if ($confirmedId === $department->id)
                <button wire:click.prevent='deleteDepartment({{ $department }})' type="button" class="btn btn-sm btn-danger waves-effect waves-light">{{ __('Sure?') }}</button>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          {{-- Adjusted colspan to reflect new number of columns --}}
          <td colspan="6">
            <div class="mt-2 mb-2" style="text-align: center">
                <h3 class="mb-1 mx-2">{{ __('Oopsie-doodle!') }}</h3>
                <p class="mb-4 mx-2">
                  {{ __('No data found, please sprinkle some data in my virtual bowl, and let the fun begin!') }}
                </p>
                <button class="btn btn-label-primary mb-4" wire:click.prevent='showNewDepartmentModal' data-bs-toggle="modal" data-bs-target="#departmentModal">
                    {{ __('Add New Department') }}
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
</div>

{{-- Modal --}}
{{-- Make sure _partials/_modals/modal-department.blade.php includes fields for:
    name (string)
    branch_type (enum: 'headquarters', 'state') - Potentially a select dropdown
    code (string, nullable)
    description (text, nullable)
    is_active (boolean, default: true) - Potentially a checkbox/toggle
    head_of_department_id (foreignId, nullable, links to users.id) - Potentially a select dropdown of users
--}}
@include('_partials/_modals/modal-department')
</div>
