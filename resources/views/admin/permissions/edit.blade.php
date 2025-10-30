@extends('layouts.app')

@section('title', __('Editar Permiso: :name', ['name' => $permission->name]))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit mr-2"></i>{{ __('Editar Permiso') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">{{ __('Permisos') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.permissions.show', $permission) }}">{{ $permission->name }}</a></li>
                <li class="breadcrumb-item active">{{ __('Editar') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        @if(in_array($permission->name, ['system.manage', 'users.manage', 'roles.manage', 'permissions.manage']))
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> {{ __('Permiso Crítico del Sistema') }}</h5>
            <p>{{ __('Este permiso es crítico para el funcionamiento del sistema. Algunas modificaciones están restringidas por seguridad.') }}</p>
            <ul>
                <li>{{ __('No se puede cambiar el nombre del permiso') }}</li>
                <li>{{ __('No se puede eliminar') }}</li>
                <li>{{ __('Los cambios en roles deben hacerse con precaución') }}</li>
            </ul>
        </div>
        @endif

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('Información del Permiso') }}</h3>
            </div>
            
            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <!-- Nombre del Permiso -->
                    <div class="form-group">
                        <label for="name">{{ __('Nombre del Permiso') }} <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $permission->name) }}"
                               @if(in_array($permission->name, ['system.manage', 'users.manage', 'roles.manage', 'permissions.manage'])) readonly @endif
                               placeholder="{{ __('ej: users.create') }}">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        @if(!in_array($permission->name, ['system.manage', 'users.manage', 'roles.manage', 'permissions.manage']))
                            <small class="form-text text-muted">
                                {{ __('Formato recomendado: categoria.accion (ej: users.create, reports.view)') }}
                            </small>
                        @endif
                    </div>

                    <!-- Guard Name -->
                    <div class="form-group">
                        <label for="guard_name">{{ __('Guard') }} <span class="text-danger">*</span></label>
                        <select class="form-control @error('guard_name') is-invalid @enderror" 
                                id="guard_name" 
                                name="guard_name">
                            <option value="web" {{ old('guard_name', $permission->guard_name) === 'web' ? 'selected' : '' }}>
                                {{ __('Web (Predeterminado)') }}
                            </option>
                            <option value="api" {{ old('guard_name', $permission->guard_name) === 'api' ? 'selected' : '' }}>
                                {{ __('API') }}
                            </option>
                        </select>
                        @error('guard_name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">
                            {{ __('Selecciona el guard apropiado para este permiso.') }}
                        </small>
                    </div>

                    <!-- Roles Asignados -->
                    <div class="form-group">
                        <label>{{ __('Roles que tendrán este Permiso') }}</label>
                        <div class="row">
                            @foreach($allRoles as $role)
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->id }}" 
                                               id="role_{{ $role->id }}"
                                               {{ $permission->roles->contains($role->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            <small class="text-muted d-block">
                                                {{ $role->users->count() }} {{ __('usuarios') }}
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Los usuarios que tengan estos roles obtendrán automáticamente este permiso.') }}
                        </small>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Volver') }}
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Guardar Cambios') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Información Adicional -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Impacto de los Cambios') }}</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $currentUsers = $permission->roles->sum(function($role) {
                                return $role->users->count();
                            });
                        @endphp
                        
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-success">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <h5 class="description-header">{{ $currentUsers }}</h5>
                                    <span class="description-text">{{ __('Usuarios Actuales') }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="description-block">
                                    <span class="description-percentage text-warning">
                                        <i class="fas fa-user-shield"></i>
                                    </span>
                                    <h5 class="description-header">{{ $permission->roles->count() }}</h5>
                                    <span class="description-text">{{ __('Roles Asignados') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="progress-group mt-3">
                            {{ __('Nivel de Uso') }}
                            <span class="float-right">
                                @if($currentUsers > 10)
                                    <b>{{ __('Alto') }}</b>
                                @elseif($currentUsers > 5)
                                    <b>{{ __('Medio') }}</b>
                                @else
                                    <b>{{ __('Bajo') }}</b>
                                @endif
                            </span>
                            <div class="progress progress-sm">
                                <div class="progress-bar 
                                    @if($currentUsers > 10) bg-danger 
                                    @elseif($currentUsers > 5) bg-warning 
                                    @else bg-success @endif" 
                                    style="width: {{ min(($currentUsers / 20) * 100, 100) }} %">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Buenas Prácticas') }}</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                {{ __('Usa nombres descriptivos y consistentes') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                {{ __('Sigue el formato categoria.accion') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                {{ __('Evita permisos demasiado amplios') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-shield-alt text-danger"></i>
                                {{ __('Ten cuidado con permisos de administración') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-users text-info"></i>
                                {{ __('Asigna a roles, no directamente a usuarios') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if(!in_array($permission->name, ['system.manage', 'users.manage', 'roles.manage', 'permissions.manage']))
        <!-- Zona de Peligro -->
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">{{ __('Zona de Peligro') }}</h3>
            </div>
            <div class="card-body">
                <p>{{ __('Las siguientes acciones son irreversibles y pueden afectar el funcionamiento del sistema.') }}</p>
                
                <button type="button" 
                        class="btn btn-danger" 
                        onclick="confirmDelete({{ $permission->id }}, '{{ $permission->name }}', {{ $currentUsers }})">
                    <i class="fas fa-trash"></i> {{ __('Eliminar Permiso') }}
                </button>
                
                <form id="delete-form-{{ $permission->id }}" 
                      action="{{ route('admin.permissions.destroy', $permission) }}" 
                      method="POST" 
                      class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@stop

@push('scripts')
<script>
function confirmDelete(permissionId, permissionName, userCount) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: `
            <p>Estás a punto de eliminar el permiso <strong>${permissionName}</strong>.</p>
            ${userCount > 0 ? `<p class="text-warning">⚠️ Este permiso afecta a <strong>${userCount} usuarios</strong>.</p>` : ''}
            <p>Esta acción no se puede deshacer.</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${permissionId}`).submit();
        }
    });
}

// Actualizar conteo de usuarios en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[name="roles[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateUserCount);
    });
    
    function updateUserCount() {
        let totalUsers = 0;
        const checkedBoxes = document.querySelectorAll('input[name="roles[]"]:checked');
        
        checkedBoxes.forEach(checkbox => {
            const label = document.querySelector(`label[for="${checkbox.id}"]`);
            const userCountText = label.querySelector('small').textContent;
            const userCount = parseInt(userCountText.match(/\d+/)[0]);
            totalUsers += userCount;
        });
        
        // Actualizar display si existe
        const currentUsersDisplay = document.querySelector('.description-header');
        if (currentUsersDisplay) {
            currentUsersDisplay.textContent = totalUsers;
        }
    }
});
</script>
@endpush