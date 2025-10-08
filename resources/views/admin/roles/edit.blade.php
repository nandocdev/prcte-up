@extends('adminlte::page')

@section('title', __('Editar Rol'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ __('Editar Rol') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">{{ __('Roles') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.show', $role) }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</a></li>
                <li class="breadcrumb-item active">{{ __('Editar') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Información del Rol') }}</h3>
                </div>

                <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('Nombre del Rol') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $role->name) }}" required
                                   @if(in_array($role->name, ['super_admin', 'profesor', 'coordinador_extension', 'decano_director', 'viex_admin']))
                                       readonly
                                   @endif>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            @if(in_array($role->name, ['super_admin', 'profesor', 'coordinador_extension', 'decano_director', 'viex_admin']))
                                <small class="form-text text-warning">
                                    <i class="fas fa-lock"></i> {{ __('Este es un rol del sistema y no se puede renombrar.') }}
                                </small>
                            @else
                                <small class="form-text text-muted">
                                    {{ __('Usa nombres en minúsculas con guiones bajos, ej: "coordinador_academico"') }}
                                </small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>{{ __('Permisos') }}</label>
                            <div class="card">
                                <div class="card-body">
                                    @foreach($permissions as $group => $groupPermissions)
                                        <div class="mb-4">
                                            <h6 class="text-primary font-weight-bold">
                                                {{ ucfirst($group) }}
                                                <button type="button" class="btn btn-link btn-sm"
                                                        onclick="toggleGroup('{{ $group }}')">
                                                    {{ __('Alternar todos') }}
                                                </button>
                                            </h6>
                                            <div class="row">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="col-md-4">
                                                        <div class="icheck-primary">
                                                            <input type="checkbox"
                                                                   id="permission_{{ $permission->id }}"
                                                                   name="permissions[]"
                                                                   value="{{ $permission->name }}"
                                                                   class="group-{{ $group }}"
                                                                   {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                            <label for="permission_{{ $permission->id }}">
                                                                {{ str_replace($group . '.', '', $permission->name) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <hr>
                                    @endforeach
                                </div>
                            </div>
                            @error('permissions')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Actualizar Rol') }}
                        </button>
                        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Rol Actual') }}</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-shield fa-3x text-primary"></i>
                        <h5 class="mt-2">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</h5>
                        @if(in_array($role->name, ['super_admin', 'profesor', 'coordinador_extension', 'decano_director', 'viex_admin']))
                            <span class="badge badge-warning">{{ __('Rol del Sistema') }}</span>
                        @endif
                    </div>

                    <h6><strong>{{ __('Permisos Actuales:') }}</strong></h6>
                    @php
                        $currentGroups = $role->permissions->groupBy(function($permission) {
                            return explode('.', $permission->name)[0] ?? 'general';
                        });
                    @endphp

                    @forelse($currentGroups as $group => $perms)
                        <div class="mb-2">
                            <small class="text-muted">{{ ucfirst($group) }}:</small>
                            <span class="badge badge-info">{{ $perms->count() }}</span>
                        </div>
                    @empty
                        <p class="text-muted">{{ __('Sin permisos') }}</p>
                    @endforelse

                    <hr>

                    <h6><strong>{{ __('Estadísticas:') }}</strong></h6>
                    <ul class="list-unstyled">
                        <li><strong>{{ __('Usuarios:') }}</strong> {{ $role->users->count() }}</li>
                        <li><strong>{{ __('Permisos:') }}</strong> {{ $role->permissions->count() }}</li>
                        <li><strong>{{ __('Creado:') }}</strong> {{ $role->created_at->format('d/m/Y') }}</li>
                    </ul>
                </div>
            </div>

            @if(in_array($role->name, ['super_admin', 'profesor', 'coordinador_extension', 'decano_director', 'viex_admin']))
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Rol del Sistema') }}</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ __('Este rol es parte del sistema VIEX y algunos cambios están restringidos:') }}</p>
                        <ul>
                            <li>{{ __('No se puede cambiar el nombre') }}</li>
                            <li>{{ __('No se puede eliminar') }}</li>
                            <li>{{ __('Los permisos se pueden modificar con precaución') }}</li>
                        </ul>
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ __('Modificar permisos puede afectar el funcionamiento del sistema.') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('js')
    <script>
        function toggleGroup(groupName) {
            const checkboxes = document.querySelectorAll(`.group-${groupName}`);
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
        }
    </script>
@stop
