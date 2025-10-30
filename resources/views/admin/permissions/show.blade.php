@extends('layouts.app')

@section('title', __('Permiso: :name', ['name' => $permission->name]))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-key mr-2"></i>{{ __('Información del Permiso') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">{{ __('Permisos') }}</a></li>
                <li class="breadcrumb-item active">{{ $permission->name }}</li>
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
                    @if(in_array($permission->name, ['system.manage', 'users.manage', 'roles.manage', 'permissions.manage']))
                        <i class="fas fa-shield-alt fa-5x text-danger"></i>
                        <p class="text-muted text-center mt-2">
                            <span class="badge badge-danger">
                                <i class="fas fa-exclamation-triangle"></i> {{ __('Permiso Crítico') }}
                            </span>
                        </p>
                    @else
                        <i class="fas fa-key fa-5x text-primary"></i>
                    @endif
                </div>
                
                <h3 class="profile-username text-center">{{ $permission->name }}</h3>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>{{ __('Categoría') }}</b>
                        <span class="float-right">
                            @php
                                $category = explode('.', $permission->name)[0] ?? 'general';
                            @endphp
                            <span class="badge badge-info">{{ ucfirst($category) }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Roles Asignados') }}</b>
                        <span class="float-right">
                            <span class="badge badge-success">{{ $permission->roles->count() }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Usuarios Afectados') }}</b>
                        <span class="float-right">
                            <span class="badge badge-warning">{{ $usersWithPermission->count() }}</span>
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Creado') }}</b>
                        <span class="float-right">{{ $permission->created_at->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Actualizado') }}</b>
                        <span class="float-right">{{ $permission->updated_at->format('d/m/Y H:i') }}</span>
                    </li>
                </ul>

                <div class="text-center">
                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> {{ __('Editar Permiso') }}
                    </a>
                </div>
            </div>
        </div>

        @if(in_array($permission->name, ['system.manage', 'users.manage', 'roles.manage', 'permissions.manage']))
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">{{ __('Permiso Crítico del Sistema') }}</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __('Este permiso es crítico para el funcionamiento del sistema.') }}
                </div>
                <p>{{ __('Restricciones aplicadas:') }}</p>
                <ul>
                    <li>{{ __('No se puede eliminar') }}</li>
                    <li>{{ __('No se puede renombrar') }}</li>
                    <li>{{ __('Los cambios en roles deben hacerse con precaución') }}</li>
                </ul>
            </div>
        </div>
        @endif
    </div>

    <!-- Roles y Usuarios -->
    <div class="col-md-8">
        <div class="row">
            <!-- Roles Asignados -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Roles que tienen este Permiso') }}</h3>
                        <div class="card-tools">
                            <span class="badge badge-primary">{{ $permission->roles->count() }} {{ __('roles') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($permission->roles as $role)
                            <div class="d-flex align-items-center justify-content-between mb-3 p-3 border rounded">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-user-shield fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</h6>
                                        <small class="text-muted">
                                            {{ $role->users->count() }} {{ __('usuarios') }} • 
                                            {{ $role->permissions->count() }} {{ __('permisos totales') }}
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> {{ __('Ver Rol') }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-user-shield fa-3x mb-3"></i>
                                <p>{{ __('Este permiso no está asignado a ningún rol.') }}</p>
                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary">
                                    {{ __('Asignar a Roles') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Usuarios Afectados -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Usuarios que tienen este Permiso') }}</h3>
                        <div class="card-tools">
                            <span class="badge badge-info">{{ $usersWithPermission->count() }} {{ __('usuarios') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($usersWithPermission as $user)
                            <div class="d-flex align-items-center justify-content-between mb-2 p-2 border-bottom">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007bff&color=fff"
                                             class="img-circle"
                                             alt="{{ $user->name }}"
                                             style="width: 40px; height: 40px;">
                                    </div>
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                        <br>
                                        <small>
                                            {{ __('Roles:') }} 
                                            @foreach($user->roles as $userRole)
                                                <span class="badge badge-secondary badge-sm">{{ $userRole->name }}</span>
                                            @endforeach
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i> {{ __('Ver') }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <p>{{ __('Ningún usuario tiene este permiso actualmente.') }}</p>
                                <p>{{ __('Para que los usuarios obtengan este permiso, debe estar asignado a un rol y ese rol debe estar asignado a usuarios.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Análisis del Permiso -->
<div class="row">
    <div class="col-md-6">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">{{ __('Análisis del Permiso') }}</h3>
            </div>
            <div class="card-body">
                @php
                    $category = explode('.', $permission->name)[0] ?? 'general';
                    $action = str_replace($category . '.', '', $permission->name);
                @endphp
                
                <div class="row">
                    <div class="col-md-6">
                        <strong>{{ __('Información Técnica:') }}</strong>
                        <ul class="list-unstyled mt-2">
                            <li><strong>{{ __('Categoría:') }}</strong> {{ $category }}</li>
                            <li><strong>{{ __('Acción:') }}</strong> {{ $action }}</li>
                            <li><strong>{{ __('Guard:') }}</strong> {{ $permission->guard_name }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>{{ __('Estadísticas:') }}</strong>
                        <ul class="list-unstyled mt-2">
                            <li><strong>{{ __('Impacto:') }}</strong> 
                                @if($usersWithPermission->count() > 10)
                                    <span class="badge badge-warning">{{ __('Alto') }}</span>
                                @elseif($usersWithPermission->count() > 5)
                                    <span class="badge badge-info">{{ __('Medio') }}</span>
                                @else
                                    <span class="badge badge-success">{{ __('Bajo') }}</span>
                                @endif
                            </li>
                            <li><strong>{{ __('Estado:') }}</strong> 
                                @if($permission->roles->count() > 0)
                                    <span class="badge badge-success">{{ __('En uso') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ __('Sin usar') }}</span>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">{{ __('Permisos Relacionados') }}</h3>
            </div>
            <div class="card-body">
                @php
                    $relatedPermissions = App\Models\Permission::where('name', 'like', $category . '.%')
                        ->where('id', '!=', $permission->id)
                        ->limit(5)
                        ->get();
                @endphp

                @if($relatedPermissions->count() > 0)
                    <p>{{ __('Otros permisos en la categoría') }} <strong>{{ $category }}</strong>:</p>
                    <ul class="list-unstyled">
                        @foreach($relatedPermissions as $related)
                            <li class="mb-1">
                                <a href="{{ route('admin.permissions.show', $related) }}" class="text-decoration-none">
                                    {{ $related->name }}
                                </a>
                                <small class="text-muted">({{ $related->roles->count() }} roles)</small>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">{{ __('No hay otros permisos en esta categoría.') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@stop