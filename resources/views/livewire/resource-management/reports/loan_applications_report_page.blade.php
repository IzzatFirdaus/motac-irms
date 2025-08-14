@extends('layouts.app')

@section('title', __('Laporan Permohonan Pinjaman Peralatan ICT'))

@section('content')
<div class="container py-4">
    @livewire('resource-management.admin.reports.loan-applications-report')
</div>
@endsection
