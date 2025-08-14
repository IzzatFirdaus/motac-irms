@extends('layouts.app') {{-- Ensure this layout is MOTAC-themed (Noto Sans, MOTAC Colors, etc.) --}}

@section('title', __('Rekod Pengeluaran Peralatan')) {{-- Design Language 1.2: Bahasa Melayu First --}}

@section('content')
    <div class="container-fluid">
        {{-- The Livewire component 'process-issuance' will contain specific UI elements to be themed. --}}
        @livewire('resource-management.admin.bpm.process-issuance', ['loanApplicationId' => $loanApplicationId])
    </div>
@endsection
