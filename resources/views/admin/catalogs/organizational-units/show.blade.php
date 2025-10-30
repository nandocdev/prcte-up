@extends('layouts.app')

@section('title', __('Detalles de Unidad Organizacional'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-university mr-2"></i>{{ $unit->name }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.organizational-units') }}">{{ __('Unidades Organizacionales') }}</a></li>
                <li class="breadcrumb-item active">{{ $unit->name }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Información Principal -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    @php
                        $typeIcons = [
                            'universidad' => 'fas fa-university',
                            'facultad' => 'fas fa-building',
                            'centro' => 'fas fa-hospital',
                            'departamento' => 'fas fa-users',
                            'escuela' => 'fas fa-graduation-cap',
                            'instituto' => 'fas fa-flask'
                        ];
                    @endphp
                    <i class="{{ $typeIcons[$unit->type] ?? 'fas fa-building' }} fa-5x text-primary"></i>
                </div>
                
                <h3 class="profile-username text-center">{{ $unit->name }}</h3>
                <p class="text-muted text-center">{{ ucfirst($unit->type) }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>{{ __('Código') }}</b>
                        <span class="float-right"><code>{{ $unit->code }}</code></span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Tipo') }}</b>
                        <span class="float-right">
                            @php
                                $typeColors = [
                                    'universidad' => 'primary',
                                    'facultad' => 'success',
                                    'centro' => 'info',
                                    'departamento' => 'warning',
                                    'escuela' => 'secondary',
                                    'instituto' => 'dark'
                                ];
                            @endphp
                            <span class="badge badge-{{ $typeColors[$unit->type] ?? 'secondary' }}">
                                {{ ucfirst($unit->type) }}
                            </span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Unidad Padre') }}</b>
                        <span class="float-right">
                            @if($unit->parent)
                                <a href="{{ route('admin.catalogs.organizational-units.show', $unit->parent) }}">
                                    {{ $unit->parent->name }}
                                </a>
                            @else
                                <span class="text-muted">{{ __('Ninguna') }}</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Subunidades') }}</b>
                        <span class="float-right">
                            <span class="badge badge-info">{{ $unit->children->count() }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Usuarios') }}</b>
                        <span class="float-right">
                            <span class="badge badge-primary">{{ $unit->users->count() }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Estado') }}</b>
                        <span class="float-right">
                            @if($unit->is_active)
                                <span class="badge badge-success">{{ __('Activa') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ __('Inactiva') }}</span>
                            @endif
                        </span>
                    </li>
                </ul>

                <div class="text-center">
                    <a href="{{ route('admin.catalogs.organizational-units.edit', $unit) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> {{ __('Editar Unidad') }}
                    </a>
                </div>
            </div>
        </div>

        @if($unit->description)
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">{{ __('Descripción') }}</h3>
            </div>
            <div class="card-body">
                <p>{{ $unit->description }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Contenido Principal -->
    <div class="col-md-8">
        <div class="row">
            <!-- Subunidades -->
            @if($unit->children->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Subunidades') }}</h3>
                        <div class="card-tools">
                            <span class="badge badge-primary">{{ $unit->children->count() }} {{ __('unidades') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($unit->children as $child)
                                <div class="col-md-6 mb-3">
                                    <div class="card card-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="{{ $typeIcons[$child->type] ?? 'fas fa-building' }}"></i>
                                                {{ $child->name }}
                                            </h6>
                                            <p class="card-text">
                                                <small class="text-muted">{{ $child->code }}</small><br>
                                                <span class="badge badge-{{ $typeColors[$child->type] ?? 'secondary' }}">
                                                    {{ ucfirst($child->type) }}
                                                </span>
                                                @if($child->users->count() > 0)
                                                    <span class="badge badge-primary">{{ $child->users->count() }} usuarios</span>
                                                @endif
                                            </p>
                                            <a href="{{ route('admin.catalogs.organizational-units.show', $child) }}" class="btn btn-sm btn-outline-primary">
                                                {{ __('Ver detalles') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Usuarios -->
            @if($unit->users->count() > 0)
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Usuarios Asignados') }}</h3>
                        <div class="card-tools">
                            <span class="badge badge-primary">{{ $unit->users->count() }} {{ __('usuarios') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('Nombre') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Código de Profesor') }}</th>
                                        <th>{{ __('Roles') }}</th>
                                        <th>{{ __('Estado') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unit->users->take(10) as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->professor_code)
                                                    <code>{{ $user->professor_code }}</code>
                                                @else
                                                    <span class="text-muted">{{ __('N/A') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach($user->roles as $role)
                                                    <span class="badge badge-secondary badge-sm">{{ $role->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge badge-success badge-sm">{{ __('Activo') }}</span>
                                                @else
                                                    <span class="badge badge-secondary badge-sm">{{ __('Inactivo') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($unit->users->count() > 10)
                            <div class="text-center">
                                <a href="{{ route('admin.users.index', ['unit' => $unit->id]) }}" class="btn btn-outline-primary">
                                    {{ __('Ver todos los usuarios') }} ({{ $unit->users->count() - 10 }} {{ __('más') }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Jerarquía -->
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Ubicación en la Jerarquía') }}</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $hierarchy = [];
                            $current = $unit;
                            while ($current) {
                                $hierarchy[] = $current;
                                $current = $current->parent;
                            }
                            $hierarchy = array_reverse($hierarchy);
                        @endphp

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                @foreach($hierarchy as $level)
                                    @if($loop->last)
                                        <li class="breadcrumb-item active">
                                            <i class="{{ $typeIcons[$level->type] ?? 'fas fa-building' }}"></i>
                                            {{ $level->name }}
                                        </li>
                                    @else
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('admin.catalogs.organizational-units.show', $level) }}">
                                                <i class="{{ $typeIcons[$level->type] ?? 'fas fa-building' }}"></i>
                                                {{ $level->name }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop