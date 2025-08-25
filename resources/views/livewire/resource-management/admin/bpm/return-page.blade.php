<<<<<<< HEAD
@extends('layouts.app') {{-- Ensure this layout is MOTAC-themed (Noto Sans, MOTAC Colors, etc.) --}}

@section('title', __('Rekod Pulangan Peralatan')) {{-- Design Language 1.2: Bahasa Melayu First --}}

@section('content')
    <div class="container-fluid">
        {{-- The Livewire component 'process-return' will contain specific UI elements to be themed. --}}
=======
@extends('layouts.app') {{-- Or your admin layout --}}

@section('title', 'Rekod Pulangan Peralatan')

@section('content')
    <div class="container-fluid">
>>>>>>> 7940bed (feat: Standardize authorization policies, update service provider and models, and refine configuration for consistent role management and grade-based approvals; Refactor: Streamline notification system with generic classes and consolidations)
        @livewire('resource-management.admin.bpm.process-return', ['loanApplicationId' => $loanApplicationId])
    </div>
@endsection
