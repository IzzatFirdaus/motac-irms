@extends('layouts.app')

@section('title', __('Maklumat Pengguna')) {{-- Static title, as per recent fix in Livewire component --}}

@section('content')
    {{-- !!! IMPORTANT: This is the ONLY root HTML element for Livewire component [settings.users.show] !!! --}}
    <div>
        <div class="container-fluid px-lg-4 py-4">

            {{-- Page Header and Back Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="fas fa-user-circle me-2"></i>{{ __('Maklumat Pengguna') }}
                    @if ($userToShow->exists)
                        <span class="text-muted fw-normal ms-2"> - {{ $userToShow->name }}</span>
                    @endif
                </h1>
                <a href="{{ route('settings.users.index') }}" wire:navigate class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center motac-btn-outline">
                    <i class="fas fa-arrow-left me-1"></i>
                    {{ __('Kembali ke Senarai Pengguna') }}
                </a>
            </div>

            <div class="card shadow-sm motac-card">
                <div class="card-header bg-light py-3 motac-card-header">
                    <h3 class="h5 card-title fw-semibold mb-0">{{ __('Butiran Pengguna') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            <img src="{{ $userToShow->profile_photo_url }}" alt="{{ $userToShow->name }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h5 class="mb-1">{{ $userToShow->name }}</h5>
                            <p class="text-muted small">{{ $userToShow->email }}</p>
                            {{-- Removed: @if ($userToShow->motac_email) <p class="text-muted small mb-0">{{ $userToShow->motac_email }} (MOTAC)</p> @endif --}}
                            <span class="badge rounded-pill {{ \App\Models\User::getRoleBadgeClass($userToShow->roles->first()->name ?? '') }}">{{ $userToShow->roles->first()->name ?? __('Tiada Peranan') }}</span>
                        </div>
                        <div class="col-md-9">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm motac-table-show mb-4">
                                    <tbody>
                                        <tr>
                                            <th>{{ __('Tajuk') }}</th>
                                            <td>{{ $userToShow->title }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Nama Penuh') }}</th>
                                            <td>{{ $userToShow->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('No. Kad Pengenalan') }}</th>
                                            <td>{{ $userToShow->identification_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Emel Peribadi') }}</th>
                                            <td>{{ $userToShow->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('No. Telefon') }}</th>
                                            <td>{{ $userToShow->phone_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Jabatan') }}</th>
                                            <td>{{ $userToShow->department->name ?? __('Tiada Jabatan') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Jawatan') }}</th>
                                            <td>{{ $userToShow->position->name ?? __('Tiada Jawatan') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('ID Kakitangan') }}</th>
                                            <td>{{ $userToShow->staff_id ?? __('Tidak Ditetapkan') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Status') }}</th>
                                            <td>
                                                <span class="badge {{ $userToShow->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $userToShow->status == 'active' ? __('Aktif') : __('Tidak Aktif') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Dicipta Pada') }}</th>
                                            <td>{{ $userToShow->created_at->format('d/m/Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Dikemaskini Pada') }}</th>
                                            <td>{{ $userToShow->updated_at->format('d/m/Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>{{ __('Peranan') }}</th>
                                            <td>
                                                @forelse ($userToShow->roles as $role)
                                                    <span class="badge rounded-pill {{ \App\Models\User::getRoleBadgeClass($role->name) }} me-1">
                                                        {{ $role->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted">{{ __('Tiada Peranan Ditetapkan') }}</span>
                                                @endforelse
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                @can('update', $userToShow)
                                    <a href="{{ route('settings.users.edit', $userToShow->id) }}" wire:navigate class="btn btn-primary me-2">
                                        <i class="fas fa-edit me-1"></i> {{ __('Edit Pengguna') }}
                                    </a>
                                @endcan
                                @can('delete', $userToShow)
                                    @if (Auth::user()->id !== $userToShow->id) {{-- Prevent deleting self --}}
                                        {{-- Dispatch to open a global delete modal. The actual deletion method is on the Index component. --}}
                                        <button wire:click="$dispatch('open-delete-modal', { id: {{ $userToShow->id }}, itemDescription: '{{ __('pengguna') }} {{ $userToShow->name }}', deleteMethod: 'deleteUser' })" class="btn btn-danger">
                                            <i class="fas fa-trash-alt me-1"></i> {{ __('Padam Pengguna') }}
                                        </button>
                                    @endif
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- The delete modal itself will be in index.blade.php or your main layout, listening for the 'open-delete-modal' event --}}
    </div>
@endsection
