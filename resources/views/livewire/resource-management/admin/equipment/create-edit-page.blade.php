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

        @livewire('resource-management.admin.equipment.equipment-form', ['equipmentId' => $equipmentId])
    </div>
@endsection
