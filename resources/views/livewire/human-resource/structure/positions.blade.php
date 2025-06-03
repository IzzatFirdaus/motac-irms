{{-- resources/views/livewire/positions.blade.php --}}
<div>

    @php
        // Standardized helper path
        $configData = \App\Helpers\Helpers::appClasses();
    @endphp

    @section('title', __('Jawatan - Struktur Organisasi')) {{-- Design Language 1.2: BM First --}}

    <div class="demo-inline-spacing mb-3"> {{-- Added mb-3 for spacing --}}
        <button wire:click.prevent='showNewPositionModal' type="button" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#positionModal">
            {{-- Iconography: Design Language 2.4. Changed from ti-plus. --}}
            <span class="bi bi-plus-lg me-1"></span>{{ __('Tambah Jawatan Baharu') }}
        </button>
    </div>

    <div class="card motac-card"> {{-- Added .motac-card --}}
        <h5 class="card-header motac-card-header">
            {{-- Iconography: Design Language 2.4. Changed from ti-id-badge-2. --}}
            <i class="bi bi-person-rolodex fs-4 text-primary me-2"></i>{{ __('Senarai Jawatan') }} {{-- Color changed for emphasis --}}
        </h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover"> {{-- Added table-hover --}}
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Nama Jawatan') }}</th>
                        <th>{{ __('Gred Berkaitan') }}</th> {{-- Design Language 1.2 --}}
                        <th>{{ __('Status') }}</th>
                        {{-- 'Vacancies Count' is not in the MOTAC 'positions' table design. Confirm if needed. --}}
                        <th>{{ __('Tindakan') }}</th> {{-- Design Language 1.2 --}}
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($positions as $position)
                        <tr>
                            <td>{{ $position->id }}</td>
                            <td><strong>{{ $position->name }}</strong></td>
                            <td>
                                {{-- Display associated grade_id from MOTAC design (positions table) --}}
                                @if ($position->grade)
                                    {{ $position->grade->name }}
                                @else
                                    {{ __('N/A') }}
                                @endif
                            </td>
                            <td>
                                {{-- Display is_active status from MOTAC design (positions table: is_active boolean) --}}
                                {{-- Ensure .bg-label-success & .bg-label-danger are styled by MOTAC theme (Design Language 2.1) --}}
                                @if ($position->is_active)
                                    <span class="badge bg-label-success me-1">{{ __('Aktif') }}</span>
                                @else
                                    <span class="badge bg-label-danger me-1">{{ __('Tidak Aktif') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <div class="dropdown">
                                        {{-- Iconography: Design Language 2.4. Changed from ti-dots-vertical. --}}
                                        <button type="button"
                                            class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            {{-- Ensure modal-position allows editing of description, grade_id, is_active as per design_tokens.json or DB Schema --}}
                                            <a wire:click.prevent='showEditPositionModal({{ $position }})'
                                                data-bs-toggle="modal" data-bs-target="#positionModal"
                                                class="dropdown-item" href="#">
                                                {{-- Iconography: Design Language 2.4. Changed from ti-pencil. --}}
                                                <i class="bi bi-pencil-square me-1"></i> {{ __('Sunting') }}
                                            </a>
                                            <a wire:click.prevent='confirmDeletePosition({{ $position->id }})'
                                                class="dropdown-item" href="#">
                                                {{-- Iconography: Design Language 2.4. Changed from ti-trash. --}}
                                                <i class="bi bi-trash3 me-1"></i> {{ __('Padam') }}
                                            </a>
                                        </div>
                                    </div>
                                    @if ($confirmedId === $position->id)
                                        <button wire:click.prevent='deletePosition({{ $position }})' type="button"
                                            class="btn btn-xs btn-danger ms-2">{{ __('Pasti?') }}</button>
                                        {{-- Simplified button style --}}
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"> {{-- Adjusted colspan --}}
                                <div class="text-center p-4">
                                    {{-- Iconography: Design Language 2.4 --}}
                                    <i class="bi bi-person-workspace fs-1 text-muted mb-2 d-block"></i>
                                    {{-- Design Language 1.4: Formal Tone --}}
                                    <h5 class="mb-1 mx-2">{{ __('Tiada Jawatan Ditemui') }}</h5>
                                    <p class="mb-3 mx-2 text-muted">
                                        {{ __('Sila tambah jawatan baharu untuk memulakan.') }}
                                    </p>
                                    <button class="btn btn-primary btn-sm" wire:click.prevent='showNewPositionModal'
                                        data-bs-toggle="modal" data-bs-target="#positionModal">
                                        {{-- Iconography: Design Language 2.4. Changed from ti-plus. --}}
                                        <span class="bi bi-plus-lg me-1"></span>{{ __('Tambah Jawatan Baharu') }}
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
    {{-- Make sure _partials/_modals/modal-position.blade.php includes fields for:
    name (string)
    grade_id (foreignId, nullable, links to grades.id) - Potentially a select dropdown of Grades
    description (text, nullable)
    is_active (boolean, default: true) - Potentially a checkbox/toggle
    (As per MOTAC `positions` table structure from System Design / design_tokens.json)
--}}
    @include('_partials/_modals/modal-position')
</div>
