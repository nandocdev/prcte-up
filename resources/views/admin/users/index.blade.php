@extends('adminlte::page')

@section('title', __('Gestión de Usuarios'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ __('Gestión de Usuarios') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Usuarios') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Lista de Usuarios') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> {{ __('Nuevo Usuario') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Buscar') }}</label>
                                    <input type="text" name="search" class="form-control"
                                           value="{{ request('search') }}"
                                           placeholder="{{ __('Nombre, email o código...') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ __('Rol') }}</label>
                                    <select name="role" class="form-control">
                                        <option value="">{{ __('Todos los roles') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}"
                                                    {{ request('role') === $role->name ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-search"></i> {{ __('Filtrar') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de usuarios -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Nombre') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Código Profesor') }}</th>
                                    <th>{{ __('Unidad Organizacional') }}</th>
                                    <th>{{ __('Roles') }}</th>
                                    <th>{{ __('Estado') }}</th>
                                    <th>{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-panel d-inline-flex align-items-center">
                                                    <div class="image mr-2">
                                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007bff&color=fff"
                                                             class="img-circle elevation-2"
                                                             alt="{{ $user->name }}"
                                                             style="width: 30px; height: 30px;">
                                                    </div>
                                                    <div class="info">
                                                        {{ $user->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->professor_code)
                                                <span class="badge badge-info">{{ $user->professor_code }}</span>
                                            @else
                                                <span class="text-muted">{{ __('N/A') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->organizationalUnit)
                                                {{ $user->organizationalUnit->name }}
                                            @else
                                                <span class="text-muted">{{ __('No asignada') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @forelse($user->roles as $role)
                                                <span class="badge badge-secondary mr-1">
                                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                </span>
                                            @empty
                                                <span class="text-muted">{{ __('Sin roles') }}</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge badge-success">{{ __('Activo') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Inactivo') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.users.show', $user) }}"
                                                   class="btn btn-info btn-sm" title="{{ __('Ver') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}"
                                                   class="btn btn-primary btn-sm" title="{{ __('Editar') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDelete({{ $user->id }})"
                                                            title="{{ __('Eliminar') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            {{ __('No se encontraron usuarios.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    {{ $users->withQueryString()->links('vendor.pagination.bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Confirmar Eliminación') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ __('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.') }}
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            {{ __('Eliminar') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .user-panel .image img {
            width: 30px;
            height: 30px;
        }
    </style>
@stop

@section('js')
    <script>
        function confirmDelete(userId) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `{{ url('admin/users') }}/${userId}`;
            $('#deleteModal').modal('show');
        }
    </script>
@stop
