@extends('layouts.app')

@section('title', ($equipmentId ? __('Kemaskini Peralatan ICT') : __('Tambah Peralatan ICT Baru')))

@section('content')
    <div class="container-fluid">
        {{-- Breadcrumbs (optional) --}}
        {{-- <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('resource-management.admin.equipment.equipment-index') }}">{{ __('Senarai Peralatan ICT') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $equipmentId ? __('Kemaskini Peralatan') : __('Tambah Peralatan Baru') }}</li>
            </ol>
        </nav> --}}
        @livewire('resource-management.admin.equipment.equipment-form', ['equipmentId' => $equipmentId])
    </div>
@endsection
