@extends('layouts.app') {{-- Or your admin layout --}}

@section('title', 'Rekod Pulangan Peralatan')

@section('content')
    <div class="container-fluid">
        @livewire('resource-management.admin.bpm.process-return', ['loanApplicationId' => $loanApplicationId])
    </div>
@endsection
