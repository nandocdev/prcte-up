@extends('layouts.app')

@section('title', __('Crear Rol'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endpush

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>{{ __('Crear Nuevo Rol') }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">{{ __('Roles') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Crear') }}</li>
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

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('Nombre del Rol') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">
                            {{ __('Usa nombres en minúsculas con guiones bajos, ej: "coordinador_academico"') }}
                        </small>
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
                                            {{ __('Seleccionar todos') }}
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
                                                    {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
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
                        <i class="fas fa-save"></i> {{ __('Crear Rol') }}
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> {{ __('Cancelar') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">{{ __('Información') }}</h3>
            </div>
            <div class="card-body">
                <h6><strong>{{ __('Buenas prácticas:') }}</strong></h6>
                <ul>
                    <li>{{ __('Usa nombres descriptivos y concisos') }}</li>
                    <li>{{ __('Evita espacios, usa guiones bajos') }}</li>
                    <li>{{ __('Asigna solo los permisos necesarios') }}</li>
                    <li>{{ __('Considera el principio de menor privilegio') }}</li>
                </ul>

                <hr>

                <h6><strong>{{ __('Grupos de Permisos:') }}</strong></h6>
                <ul>
                    <li><strong>works:</strong> {{ __('Trabajos de extensión') }}</li>
                    <li><strong>users:</strong> {{ __('Gestión de usuarios') }}</li>
                    <li><strong>roles:</strong> {{ __('Gestión de roles') }}</li>
                    <li><strong>system:</strong> {{ __('Administración del sistema') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
    function toggleGroup(groupName) {
        const checkboxes = document.querySelectorAll(`.group-${groupName}`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }
</script>
@endpush