{{-- resources/views/livewire/departments.blade.php --}}
<div>

@php
  // Standardized helper path
  $configData = \App\Helpers\Helpers::appClasses();
@endphp

@section('title', __('Jabatan - Struktur Organisasi')) {{-- Design Language 1.2: Bahasa Melayu First --}}

<div class="demo-inline-spacing mb-3"> {{-- Added mb-3 for spacing --}}
  <button wire:click.prevent='showNewDepartmentModal' type="button" class="btn btn-primary"
    data-bs-toggle="modal" data-bs-target="#departmentModal">
    {{-- Iconography: Design Language 2.4. Changed from ti-plus. --}}
    <span class="bi bi-plus-lg me-1"></span>{{ __('Tambah Jabatan Baharu') }}
  </button>
</div>

<div class="card motac-card"> {{-- Added .motac-card for MOTAC theme consistency --}}
  <h5 class="card-header motac-card-header">
    {{-- Iconography: Design Language 2.4. Changed from ti-building. --}}
    <i class="bi bi-buildings fs-4 text-primary me-2"></i>{{ __('Senarai Jabatan') }} {{-- Color changed to primary for emphasis --}}
  </h5>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover"> {{-- Added table-hover for better UX --}}
      <thead>
        <tr>
          <th>{{ __('ID') }}</th>
          <th>{{ __('Nama Jabatan') }}</th>
          <th>{{ __('Jenis Cawangan') }}</th> {{-- Design Language 1.2 --}}
          <th>{{ __('Status') }}</th>
          <th>{{ __('Jumlah Anggota') }}</th>
          {{-- Consider adding 'Head of Department' if relevant to display --}}
          {{-- <th>{{ __('Ketua Jabatan') }}</th> --}}
          <th>{{ __('Tindakan') }}</th> {{-- Design Language 1.2 --}}
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @forelse($departments as $department)
        <tr>
          <td>{{ $department->id }}</td>
          <td><strong>{{ $department->name }}</strong></td>
          <td>
            {{-- Display branch_type from MOTAC design (users table: branch_type enum) --}}
            @if($department->branch_type)
              {{ __(Str::title(str_replace('_', ' ', $department->branch_type))) }}
            @else
              {{ __('N/A') }}
            @endif
          </td>
          <td>
            {{-- Display is_active status from MOTAC design (departments table: is_active boolean) --}}
            {{-- Ensure .bg-label-success & .bg-label-danger are styled by MOTAC theme (Design Language 2.1) --}}
            @if ($department->is_active)
              <span class="badge bg-label-success me-1">{{ __('Aktif') }}</span>
            @else
              <span class="badge bg-label-danger me-1">{{ __('Tidak Aktif') }}</span>
            @endif
          </td>
          <td>
            {{-- This method $this->getMembersCount() is part of the Livewire component --}}
            <span class="badge rounded-pill bg-label-info">{{ $this->getMembersCount($department->id) }}</span>
          </td>
          {{-- <td>{{ $department->headOfDepartment ? $department->headOfDepartment->name : 'N/A' }}</td> --}}
          <td>
            <div class="d-flex"> {{-- Corrected from original: display: flex; --}}
              <div class="dropdown">
                {{-- Iconography: Design Language 2.4. Changed from ti-dots-vertical. --}}
                <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                <div class="dropdown-menu dropdown-menu-end">
                  {{-- Ensure modal-department allows editing of all MOTAC fields as per design_tokens.json or DB schema --}}
                  <a wire:click.prevent='showEditDepartmentModal({{ $department }})' data-bs-toggle="modal" data-bs-target="#departmentModal" class="dropdown-item" href="#">
                    {{-- Iconography: Design Language 2.4. Changed from ti-pencil. --}}
                    <i class="bi bi-pencil-square me-1"></i> {{ __('Sunting') }}
                  </a>
                  <a wire:click.prevent='confirmDeleteDepartment({{ $department->id }})' class="dropdown-item" href="#">
                    {{-- Iconography: Design Language 2.4. Changed from ti-trash. --}}
                    <i class="bi bi-trash3 me-1"></i> {{ __('Padam') }}
                  </a>
                </div>
              </div>
              @if ($confirmedId === $department->id)
                <button wire:click.prevent='deleteDepartment({{ $department }})' type="button" class="btn btn-xs btn-danger ms-2">{{ __('Pasti?') }}</button> {{-- Simplified button style --}}
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6"> {{-- Adjusted colspan --}}
            <div class="text-center p-4">
                {{-- Iconography: Design Language 2.4 --}}
                <i class="bi bi-folder-x fs-1 text-muted mb-2 d-block"></i>
                {{-- Design Language 1.4: Formal Tone --}}
                <h5 class="mb-1 mx-2">{{ __('Tiada Jabatan Ditemui') }}</h5>
                <p class="mb-3 mx-2 text-muted">
                  {{ __('Sila tambah jabatan baharu untuk memulakan.') }}
                </p>
                <button class="btn btn-primary btn-sm" wire:click.prevent='showNewDepartmentModal' data-bs-toggle="modal" data-bs-target="#departmentModal">
                    {{-- Iconography: Design Language 2.4. Changed from ti-plus. --}}
                    <span class="bi bi-plus-lg me-1"></span>{{ __('Tambah Jabatan Baharu') }}
                  </button>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Modal --}}
{{-- Ensure _partials/_modals/modal-department.blade.php includes fields for:
    name (string)
    branch_type (enum: 'headquarters', 'state') - Potentially a select dropdown
    code (string, nullable)
    description (text, nullable)
    is_active (boolean, default: true) - Potentially a checkbox/toggle
    head_of_department_id (foreignId, nullable, links to users.id) - Potentially a select dropdown of users
    (As per MOTAC `departments` table structure from System Design / design_tokens.json)
--}}
@include('_partials/_modals/modal-department')
</div>
