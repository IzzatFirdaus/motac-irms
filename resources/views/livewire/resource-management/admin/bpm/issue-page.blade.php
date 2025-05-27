@extends('layouts.app') {{-- Or your admin layout --}}

@section('title', 'Rekod Pengeluaran Peralatan')

@section('content')
    <div class="container-fluid">
        @livewire('resource-management.admin.bpm.process-issuance', ['loanApplicationId' => $loanApplicationId])
    </div>
@endsection
