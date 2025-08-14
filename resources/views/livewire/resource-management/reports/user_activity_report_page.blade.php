@extends('layouts.app') {{-- Assuming Bootstrap 5 is loaded in layouts.app --}}

@section('title', __('Laporan Aktiviti Pengguna'))

@section('content')
<div class="container py-4">
    {{-- The Livewire component will handle the actual report display --}}
    @livewire('resource-management.admin.reports.user-activity-report')
</div>
@endsection
