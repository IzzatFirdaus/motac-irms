@extends('layouts.app') {{-- This should be your main application layout that supports Livewire, ideally 'livewire.layouts.app' or similar. --}}

@section('title', $equipmentId ? __('Edit Peralatan ICT') : __('Tambah Peralatan ICT Baru'))

@section('content')
    <div class="container-fluid"> {{-- Consistent with the container class used in your 'livewire.layouts.app'. --}}
        {{--
            This Blade view acts as an entry point for the Equipment Form.
            Any global page elements like breadcrumbs or advanced page titles, if not handled by the Livewire component itself,
            could be placed here or within the extended layout ('layouts.app').
            For example:
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('resource-management.admin.equipment-admin.index') }}">{{ __('Senarai Peralatan ICT') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $equipmentId ? __('Edit Peralatan') : __('Tambah Peralatan Baru') }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-4 text-gray-800">{{ $equipmentId ? __('Edit Peralatan ICT') : __('Tambah Peralatan ICT Baru') }}</h1>
        --}}

        {{--
            The core functionality for creating or editing equipment is handled by the Livewire component below.
            The '$equipmentId' (which can be null for creation) is passed to the component,
            allowing it to fetch existing data for editing or initialize a new form.
            This aligns with the system design's approach for admin CRUD interfaces (System Design: Section 9.3).
        --}}
        @livewire('resource-management.admin.equipment.equipment-form', ['equipmentId' => $equipmentId])
    </div>
@endsection
