@extends('adminlte::page')

@section('title', 'Gestión Avanzada de Usuarios - VIEX')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-users-cog"></i> Gestión Avanzada de Usuarios
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Usuarios Avanzado</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    {{-- Barra de Herramientas --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Herramientas de Gestión
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" id="btn-mass-assign-roles">
                                    <i class="fas fa-user-plus"></i> Asignar Roles
                                </button>
                                <button type="button" class="btn btn-warning" id="btn-mass-change-status">
                                    <i class="fas fa-toggle-on"></i> Cambiar Estado
                                </button>
                                <button type="button" class="btn btn-info" id="btn-generate-passwords">
                                    <i class="fas fa-key"></i> Generar Contraseñas
                                </button>
                                <button type="button" class="btn btn-danger" id="btn-mass-delete">
                                    <i class="fas fa-trash"></i> Eliminar Seleccionados
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Nuevo Usuario
                                </a>
                                <button type="button" class="btn btn-outline-success" id="btn-export">
                                    <i class="fas fa-download"></i> Exportar
                                </button>
                                <button type="button" class="btn btn-outline-info" id="btn-import">
                                    <i class="fas fa-upload"></i> Importar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Contador de Seleccionados --}}
                    <div id="selection-info" class="alert alert-info mt-3" style="display: none;">
                        <i class="fas fa-info-circle"></i>
                        <span id="selected-count">0</span> usuario(s) seleccionado(s)
                        <button type="button" class="btn btn-sm btn-outline-info ml-2" id="btn-select-all">
                            Seleccionar Todo
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="btn-deselect-all">
                            Deseleccionar Todo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros Avanzados --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-outline card-info collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i> Filtros Avanzados
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.advanced') }}" id="filters-form">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="search">Búsqueda General</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="{{ request('search') }}" placeholder="Nombre, email, código...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="role">Rol</label>
                                    <select class="form-control" id="role" name="role">
                                        <option value="">Todos los roles</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="organizational_unit">Unidad Organizacional</label>
                                    <select class="form-control" id="organizational_unit" name="organizational_unit">
                                        <option value="">Todas las unidades</option>
                                        @foreach($organizationalUnits as $unit)
                                            <option value="{{ $unit->id }}" {{ request('organizational_unit') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Estado</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Todos</option>
                                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activos</option>
                                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="created_from">Creado Desde</label>
                                    <input type="date" class="form-control" id="created_from" name="created_from" 
                                           value="{{ request('created_from') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="created_to">Creado Hasta</label>
                                    <input type="date" class="form-control" id="created_to" name="created_to" 
                                           value="{{ request('created_to') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="form-control-plaintext">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filtrar
                                        </button>
                                        <a href="{{ route('admin.users.advanced') }}" class="btn btn-secondary ml-2">
                                            <i class="fas fa-times"></i> Limpiar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla de Usuarios --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> 
                        Lista de Usuarios ({{ $users->total() }} total)
                    </h3>
                </div>
                <div class="card-body table-responsive p-0">
                    @if($users->count() > 0)
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all-checkbox">
                                    </th>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Código Profesor</th>
                                    <th>Roles</th>
                                    <th>Unidad</th>
                                    <th>Estado</th>
                                    <th>Creado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->profile_photo_url ?? asset('vendor/adminlte/dist/img/avatar.png') }}" 
                                                     alt="User Image" class="img-circle img-size-32 mr-2">
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    @if($user->id === auth()->id())
                                                        <span class="badge badge-warning badge-sm">Tú</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->professor_code)
                                                <span class="badge badge-info">{{ $user->professor_code }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge badge-primary badge-sm">
                                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($user->organizationalUnit)
                                                <span class="text-sm">{{ $user->organizationalUnit->name }}</span>
                                            @else
                                                <span class="text-muted">Sin asignar</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-sm text-muted">{{ $user->created_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <button type="button" class="btn btn-danger btn-sm btn-delete-user" 
                                                            data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-4 text-center">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>No se encontraron usuarios</h5>
                            <p class="text-muted">No hay usuarios que coincidan con los filtros aplicados.</p>
                        </div>
                    @endif
                </div>
                @if($users->hasPages())
                    <div class="card-footer">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal para Asignación Masiva de Roles --}}
<div class="modal fade" id="massRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Asignación Masiva de Roles</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="mass-role-form">
                    <div class="form-group">
                        <label for="mass-roles">Seleccionar Roles</label>
                        <select multiple class="form-control" id="mass-roles" name="role_ids[]" size="5">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mass-action">Acción</label>
                        <select class="form-control" id="mass-action" name="action">
                            <option value="assign">Asignar roles (agregar a los existentes)</option>
                            <option value="sync">Sincronizar roles (reemplazar existentes)</option>
                            <option value="remove">Remover roles seleccionados</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-mass-roles">Ejecutar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Cambio de Estado --}}
<div class="modal fade" id="massStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cambiar Estado de Usuarios</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="mass-status-form">
                    <div class="form-group">
                        <label>Nuevo Estado</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status-active" value="1">
                            <label class="form-check-label" for="status-active">
                                <i class="fas fa-check-circle text-success"></i> Activar usuarios
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status-inactive" value="0">
                            <label class="form-check-label" for="status-inactive">
                                <i class="fas fa-times-circle text-danger"></i> Desactivar usuarios
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btn-confirm-mass-status">Cambiar Estado</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.user-checkbox, #select-all-checkbox {
    transform: scale(1.2);
}

.table td {
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.img-size-32 {
    width: 32px;
    height: 32px;
}

.badge-sm {
    font-size: 0.7em;
}

#selection-info {
    margin-bottom: 0;
}

.modal-body .form-check {
    margin-bottom: 10px;
}

.modal-body .form-check-label {
    margin-left: 5px;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    let selectedUsers = [];

    // Manejo de selección de usuarios
    function updateSelectionInfo() {
        const count = selectedUsers.length;
        $('#selected-count').text(count);
        $('#selection-info').toggle(count > 0);
        
        // Habilitar/deshabilitar botones de acción masiva
        $('.btn-group button[id^="btn-mass"]').prop('disabled', count === 0);
    }

    $('.user-checkbox').change(function() {
        const userId = parseInt($(this).val());
        if ($(this).is(':checked')) {
            if (!selectedUsers.includes(userId)) {
                selectedUsers.push(userId);
            }
        } else {
            selectedUsers = selectedUsers.filter(id => id !== userId);
        }
        updateSelectionInfo();
    });

    $('#select-all-checkbox').change(function() {
        const isChecked = $(this).is(':checked');
        $('.user-checkbox').prop('checked', isChecked);
        
        if (isChecked) {
            selectedUsers = $('.user-checkbox').map(function() {
                return parseInt($(this).val());
            }).get();
        } else {
            selectedUsers = [];
        }
        updateSelectionInfo();
    });

    $('#btn-select-all').click(function() {
        $('#select-all-checkbox').prop('checked', true).trigger('change');
    });

    $('#btn-deselect-all').click(function() {
        $('#select-all-checkbox').prop('checked', false).trigger('change');
    });

    // Asignación masiva de roles
    $('#btn-mass-assign-roles').click(function() {
        if (selectedUsers.length === 0) {
            Swal.fire('Error', 'Selecciona al menos un usuario', 'error');
            return;
        }
        $('#massRoleModal').modal('show');
    });

    $('#btn-confirm-mass-roles').click(function() {
        const roleIds = $('#mass-roles').val();
        const action = $('#mass-action').val();
        
        if (!roleIds || roleIds.length === 0) {
            Swal.fire('Error', 'Selecciona al menos un rol', 'error');
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
            url: '{{ route("admin.users.mass-assign-roles") }}',
            method: 'POST',
            data: {
                user_ids: selectedUsers,
                role_ids: roleIds,
                action: action,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#massRoleModal').modal('hide');
                if (response.success) {
                    Swal.fire('Éxito', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Ocurrió un error inesperado', 'error');
            },
            complete: function() {
                $('#btn-confirm-mass-roles').prop('disabled', false).html('Ejecutar');
            }
        });
    });

    // Cambio masivo de estado
    $('#btn-mass-change-status').click(function() {
        if (selectedUsers.length === 0) {
            Swal.fire('Error', 'Selecciona al menos un usuario', 'error');
            return;
        }
        $('#massStatusModal').modal('show');
    });

    $('#btn-confirm-mass-status').click(function() {
        const status = $('input[name="status"]:checked').val();
        
        if (status === undefined) {
            Swal.fire('Error', 'Selecciona un estado', 'error');
            return;
        }

        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

        $.ajax({
            url: '{{ route("admin.users.mass-change-status") }}',
            method: 'POST',
            data: {
                user_ids: selectedUsers,
                status: status === '1',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#massStatusModal').modal('hide');
                if (response.success) {
                    Swal.fire('Éxito', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Ocurrió un error inesperado', 'error');
            },
            complete: function() {
                $('#btn-confirm-mass-status').prop('disabled', false).html('Cambiar Estado');
            }
        });
    });

    // Eliminar usuarios individuales
    $('.btn-delete-user').click(function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: `Se eliminará el usuario "${userName}"`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Implementar eliminación individual
                window.location.href = '{{ route("admin.users.index") }}/' + userId + '/delete';
            }
        });
    });

    // Exportar usuarios
    $('#btn-export').click(function() {
        const filters = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("admin.users.export") }}?' + filters.toString();
    });

    // Inicializar estado de botones
    updateSelectionInfo();
});
</script>
@stop