@extends('adminlte::page')

@section('title', __('Usuario: :name', ['name' => $user->name]))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>{{ __('Información del Usuario') }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('Usuarios') }}</a></li>
            <li class="breadcrumb-item active">{{ $user->name }}</li>
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
                    <img class="profile-user-img img-fluid img-circle"
                        src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007bff&color=fff&size=128"
                        alt="{{ $user->name }}">
                </div>
                <h3 class="profile-username text-center">{{ $user->name }}</h3>
                <p class="text-muted text-center">
                    @if($user->organizationalUnit)
                        {{ $user->organizationalUnit->name }}
                    @else
                        {{ __('Sin unidad asignada') }}
                    @endif
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>{{ __('Email') }}</b>
                        <span class="float-right">{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Código Profesor') }}</b>
                        <span class="float-right">
                            @if($user->professor_code)
                                <span class="badge badge-info">{{ $user->professor_code }}</span>
                            @else
                                <span class="text-muted">{{ __('N/A') }}</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Estado') }}</b>
                        <span class="float-right">
                            @if($user->is_active)
                                <span class="badge badge-success">{{ __('Activo') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('Inactivo') }}</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <b>{{ __('Trabajos de Extensión') }}</b>
                        <span class="float-right">
                            <span class="badge badge-primary">{{ $user->workOfExtensions->count() }}</span>
                        </span>
                    </li>
                </ul>

                <div class="text-center">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> {{ __('Editar Usuario') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles y Permisos -->
    <div class="col-md-8">
        <div class="row">
            <!-- Roles -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Roles Asignados') }}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#assignRoleModal">
                                <i class="fas fa-plus"></i> {{ __('Asignar Rol') }}
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($user->roles as $role)
                            <span class="badge badge-secondary mr-2 mb-2" style="font-size: 0.9em; padding: 0.5em 0.75em;">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                <button type="button" class="btn btn-link btn-sm p-0 ml-2 text-white"
                                    onclick="confirmRemoveRole('{{ $role->name }}', '{{ ucfirst(str_replace('_', ' ', $role->name)) }}')"
                                    title="{{ __('Remover rol') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @empty
                            <p class="text-muted">{{ __('Este usuario no tiene roles asignados.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Permisos -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Permisos del Usuario') }}</h3>
                    </div>
                    <div class="card-body">
                        @php
                            $allPermissions = $user->getAllPermissions()->groupBy(function ($permission) {
                                return explode('.', $permission->name)[0] ?? 'general';
                            });
                        @endphp

                        @forelse($allPermissions as $group => $permissions)
                            <div class="mb-3">
                                <h6 class="text-muted text-uppercase">{{ ucfirst($group) }}</h6>
                                @foreach($permissions as $permission)
                                    <span class="badge badge-info mr-1 mb-1">
                                        {{ str_replace($group . '.', '', $permission->name) }}
                                    </span>
                                @endforeach
                            </div>
                        @empty
                            <p class="text-muted">{{ __('Este usuario no tiene permisos asignados.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignar Rol -->
<div class="modal fade" id="assignRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Asignar Rol') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.assign-role', $user) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role">{{ __('Seleccionar Rol') }}</label>
                        <select class="form-control" name="role" id="role" required>
                            <option value="">{{ __('Seleccionar...') }}</option>
                            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                @unless($user->hasRole($role->name))
                                    <option value="{{ $role->name }}">
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </option>
                                @endunless
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Asignar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Remover Rol -->
<div class="modal fade" id="removeRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Remover Rol') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="removeRoleForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>{{ __('¿Estás seguro de que deseas remover el rol') }} <strong id="roleToRemove"></strong>
                        {{ __('de este usuario?') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        {{ __('Remover') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function confirmRemoveRole(roleName, roleDisplayName) {
        const form = document.getElementById('removeRoleForm');
        form.action = `{{ route('admin.users.remove-role', $user) }}`;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'role';
        input.value = roleName;
        form.appendChild(input);

        document.getElementById('roleToRemove').textContent = roleDisplayName;
        $('#removeRoleModal').modal('show');
    }
</script>
@stop
