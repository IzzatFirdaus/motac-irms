<<<<<<< HEAD
@extends('layouts.app') {{-- This should be your MOTAC-themed application layout. --}}

@section('title', $equipmentId ? __('Kemaskini Peralatan ICT') : __('Tambah Peralatan ICT Baru')) {{-- Design Language 1.2: BM First --}}

@section('content')
    <div class="container-fluid"> {{-- Consistent with MOTAC theme for internal tools (often full-width) --}}
        {{--
            Optional: Breadcrumbs (ensure styling and icons align with Design Language)
            Example:
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('resource-management.admin.equipment-admin.index') }}">{{ __('Senarai Peralatan ICT') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $equipmentId ? __('Kemaskini Peralatan') : __('Tambah Peralatan Baru') }}</li>
                </ol>
            </nav>
        --}}
=======
@extends('layouts.app') {{-- Or your admin layout, e.g., layouts.admin --}}

@section('title', $equipmentId ? 'Edit Peralatan ICT' : 'Tambah Peralatan ICT Baru')

@section('content')
    <div class="container-fluid"> {{-- Or your preferred container class from your layout --}}
        {{-- You can add breadcrumbs or page headers here if your layout supports it --}}
        {{-- Example for a simple page title if not using @section('title') for it --}}
        {{-- <h1 class="h3 mb-4 text-gray-800">{{ $equipmentId ? 'Edit Peralatan ICT' : 'Tambah Peralatan ICT Baru' }}</h1> --}}
>>>>>>> 7940bed (feat: Standardize authorization policies, update service provider and models, and refine configuration for consistent role management and grade-based approvals; Refactor: Streamline notification system with generic classes and consolidations)

        @livewire('resource-management.admin.equipment.equipment-form', ['equipmentId' => $equipmentId])
    </div>
@endsection
