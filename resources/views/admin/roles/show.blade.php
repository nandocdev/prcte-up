@extends('layouts.app')

@section('title', __('Rol: :name', ['name' => ucfirst(str_replace('_', ' ', $role->name))]))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>{{ __('Información del Rol') }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">{{ __('Roles') }}</a></li>
            <li class="breadcrumb-item active">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <!-- Información Principal -->
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <i class="fas fa-user-shield fa-5x text-primary"></i>
                </div>
                <h3 class="profile-username text-center">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</h3>

                @if(in_array($role->name, ['super_admin', 'profesor', 'coordinador_extension', 'viex_admin']))
                <p class="text-muted text-center">
                    <span class="badge badge-warning">
                        <i class="fas fa-shield-alt"></i> {{ __('Rol del Sistema') }}
                    </span>
                </p>
                @endif

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>{{ __('Usuarios Asignados') }}</b>
                        <span class="float-right">
                            <span class="badge badge-primary">{{ $role->users->count() }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Permisos') }}</b>
                        <span class="float-right">
                            <span class="badge badge-success">{{ $role->permissions->count() }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Creado') }}</b>
                        <span class="float-right">{{ $role->created_at->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Actualizado') }}</b>
                        <span class="float-right">{{ $role->updated_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>

                <div class="text-center">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> {{ __('Editar Rol') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Permisos y Usuarios -->
    <div class="col-md-8">
        <div class="row">
            <!-- Permisos -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Permisos del Rol') }}</h3>
                    </div>
                    <div class="card-body">
                        @php
                        $groupedPermissions = $role->permissions->groupBy(function($permission) {
                        return explode('.', $permission->name)[0] ?? 'general';
                        });
                        @endphp

                        @forelse($groupedPermissions as $group => $permissions)
                        <div class="mb-3">
                            <h6 class="text-muted text-uppercase">{{ ucfirst($group) }}</h6>
                            @foreach($permissions as $permission)
                            <span class="badge badge-info mr-1 mb-1">
                                {{ str_replace($group . '.', '', $permission->name) }}
                            </span>
                            @endforeach
                        </div>
                        @empty
                        <p class="text-muted">{{ __('Este rol no tiene permisos asignados.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Usuarios -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Usuarios con este Rol') }}</h3>
                    </div>
                    <div class="card-body">
                        @forelse($role->users as $user)
                        <div class="d-flex align-items-center mb-2">
                            <div class="user-panel d-inline-flex align-items-center">
                                <div class="image mr-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007bff&color=fff"
                                        class="img-circle elevation-2"
                                        alt="{{ $user->name }}"
                                        style="width: 30px; height: 30px;">
                                </div>
                                <div class="info">
                                    <strong>{{ $user->name }}</strong><br>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                            <div class="ml-auto">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> {{ __('Ver') }}
                                </a>
                            </div>
                        </div>
                        @unless($loop->last)
                        <hr class="my-2">
                        @endunless
                        @empty
                        <p class="text-muted">{{ __('No hay usuarios asignados a este rol.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Descripción del rol si es del sistema -->
@if(in_array($role->name, ['super_admin', 'profesor', 'coordinador_extension', 'viex_admin']))
<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">{{ __('Descripción del Rol del Sistema') }}</h3>
            </div>
            <div class="card-body">
                @switch($role->name)
                @case('super_admin')
                <p><strong>{{ __('Super Administrador') }}:</strong> {{ __('Tiene control total sobre el sistema, puede gestionar usuarios, roles, permisos y todas las funcionalidades administrativas.') }}</p>
                @break
                @case('profesor')
                <p><strong>{{ __('Profesor') }}:</strong> {{ __('Puede crear, editar y gestionar sus propios trabajos de extensión. Es el rol base para usuarios que pueden proponer proyectos.') }}</p>
                @break
                @case('coordinador_extension')
                <p><strong>{{ __('Coordinador de Extensión') }}:</strong> {{ __('Puede revisar y aprobar trabajos de extensión de su unidad organizacional antes de ser enviados al Decanato/Dirección.') }}</p>
                @break
                @case('viex_admin')
                <p><strong>{{ __('Administrador VIEX') }}:</strong> {{ __('Asigna evaluadores, certifica trabajos finales y genera reportes. Es el rol de mayor autoridad en el proceso de extensión.') }}</p>
                @break
                @endswitch

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __('Este es un rol del sistema y no debe ser eliminado para mantener la integridad del workflow de VIEX.') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@push('styles')
<style>
    .user-panel .image img {
        width: 30px;
        height: 30px;
    }
</style>
@endpush