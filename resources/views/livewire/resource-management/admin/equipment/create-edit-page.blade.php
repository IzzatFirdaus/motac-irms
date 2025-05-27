@extends('layouts.app') {{-- Or your admin layout, e.g., layouts.admin --}}

@section('title', $equipmentId ? 'Edit Peralatan ICT' : 'Tambah Peralatan ICT Baru')

@section('content')
    <div class="container-fluid"> {{-- Or your preferred container class from your layout --}}
        {{-- You can add breadcrumbs or page headers here if your layout supports it --}}
        {{-- Example for a simple page title if not using @section('title') for it --}}
        {{-- <h1 class="h3 mb-4 text-gray-800">{{ $equipmentId ? 'Edit Peralatan ICT' : 'Tambah Peralatan ICT Baru' }}</h1> --}}

        @livewire('resource-management.admin.equipment.equipment-form', ['equipmentId' => $equipmentId])
    </div>
@endsection
