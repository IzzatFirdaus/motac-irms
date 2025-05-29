@extends('layouts.app')

@section('title', __('Laporan Inventori Peralatan ICT'))

@section('content')
<div class="container py-4">
    @livewire('resource-management.admin.reports.equipment-report')
</div>
@endsection
