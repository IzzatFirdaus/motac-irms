{{-- resources/views/users/index.blade.php --}}
@extends('layouts.app')

@section('title', __('All Users'))

@section('content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 fw-bold text-dark mb-0">{{ __('All Users') }}</h1>
            {{-- Add Create User button if applicable --}}
            {{-- @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-plus-lg me-1"></i> {{ __('Create User') }}
            </a>
        @endcan --}}
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                @if ($users->isEmpty())
                    <div class="alert alert-info" role="alert">
                        {{ __('No users found.') }}
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">{{ __('Name') }}
                                    </th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">
                                        {{ __('Department') }}</th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium">{{ __('Grade') }}
                                    </th>
                                    <th scope="col" class="small text-uppercase text-muted fw-medium text-end">
                                        {{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="align-middle">{{ $user->full_name }}</td>
                                        <td class="align-middle">{{ $user->department->name ?? '-' }}</td>
                                        <td class="align-middle">{{ $user->grade->name ?? '-' }}</td>
                                        <td class="text-end align-middle">
                                            <a href="{{ route('users.show', $user->id) }}"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center">
                                                <i class="bi bi-eye me-1"></i> {{ __('View') }}
                                            </a>
                                            {{-- Add Edit/Delete buttons if applicable --}}
                                            {{-- @can('update', $user)
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-secondary ms-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if ($users->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
