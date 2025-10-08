@extends('adminlte::page')

@section('title', __('Gestión de Asignación de Roles'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ __('Dashboard de Asignación de Roles') }}</h1>
            <p class="text-muted">{{ __('Gestión avanzada de la tabla model_has_roles') }}</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Asignación de Roles') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Estadísticas Generales -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>{{ __('Total Usuarios') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalRoles }}</h3>
                    <p>{{ __('Roles Disponibles') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalAssignments }}</h3>
                    <p>{{ __('Asignaciones Activas') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-link"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $usersWithoutRoles }}</h3>
                    <p>{{ __('Sin Roles') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-slash"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Matriz de Roles por Usuario -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Matriz de Asignación de Roles') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#massAssignModal">
                            <i class="fas fa-users-cog"></i> {{ __('Asignación Masiva') }}
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="exportMatrix()">
                            <i class="fas fa-download"></i> {{ __('Exportar') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control"
                                       value="{{ request('search') }}"
                                       placeholder="{{ __('Buscar usuario...') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="role_filter" class="form-control">
                                    <option value="">{{ __('Filtrar por rol') }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}"
                                                {{ request('role_filter') === $role->name ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="unit_filter" class="form-control">
                                    <option value="">{{ __('Filtrar por unidad') }}</option>
                                    @foreach($organizationalUnits as $unit)
                                        <option value="{{ $unit->id }}"
                                                {{ request('unit_filter') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> {{ __('Filtrar') }}
                                </button>
                                <a href="{{ route('admin.role-assignment.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> {{ __('Limpiar') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla Matriz -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="roleMatrix">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="align-middle">{{ __('Usuario') }}</th>
                                    <th rowspan="2" class="align-middle">{{ __('Unidad') }}</th>
                                    <th colspan="{{ $roles->count() }}" class="text-center bg-light">{{ __('Roles') }}</th>
                                    <th rowspan="2" class="align-middle">{{ __('Acciones') }}</th>
                                </tr>
                                <tr>
                                    @foreach($roles as $role)
                                        <th class="text-center bg-light" style="writing-mode: vertical-rl; text-orientation: mixed;">
                                            <span title="{{ ucfirst(str_replace('_', ' ', $role->name)) }}">
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007bff&color=fff"
                                                     class="img-circle mr-2" style="width: 30px; height: 30px;" alt="{{ $user->name }}">
                                                <div>
                                                    <strong>{{ $user->name }}</strong><br>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                    @if($user->professor_code)
                                                        <br><small class="badge badge-info">{{ $user->professor_code }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $user->organizationalUnit->name ?? __('Sin asignar') }}</small>
                                        </td>
                                        @foreach($roles as $role)
                                            <td class="text-center">
                                                @if($user->hasRole($role->name))
                                                    <i class="fas fa-check-circle text-success" title="{{ __('Asignado') }}"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-muted" title="{{ __('No asignado') }}"></i>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.users.show', $user) }}"
                                                   class="btn btn-outline-primary btn-sm" title="{{ __('Ver detalles') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-warning btn-sm"
                                                        onclick="quickEditUser({{ $user->id }}, '{{ $user->name }}')"
                                                        title="{{ __('Edición rápida') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + $roles->count() }}" class="text-center text-muted">
                                            {{ __('No se encontraron usuarios.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $users->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución por Roles -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Distribución por Roles') }}</h3>
                </div>
                <div class="card-body">
                    <canvas id="roleDistributionChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Usuarios por Unidad Organizacional') }}</h3>
                </div>
                <div class="card-body">
                    <canvas id="unitDistributionChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignación Masiva -->
<div class="modal fade" id="massAssignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Asignación Masiva de Roles') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.role-assignment.mass-assign') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Seleccionar Usuarios') }}</label>
                                <select name="users[]" class="form-control select2" multiple style="width: 100%;">
                                    @foreach($allUsers as $user)
                                        <option value="{{ $user->id }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Seleccionar Roles') }}</label>
                                <select name="roles[]" class="form-control select2" multiple style="width: 100%;">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">
                                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('Acción') }}</label>
                        <select name="action" class="form-control">
                            <option value="assign">{{ __('Asignar roles') }}</option>
                            <option value="remove">{{ __('Remover roles') }}</option>
                            <option value="replace">{{ __('Reemplazar todos los roles') }}</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ __('Esta acción afectará múltiples usuarios. Asegúrate de revisar la selección antes de continuar.') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Ejecutar') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edición Rápida -->
<div class="modal fade" id="quickEditModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Edición Rápida de Roles') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="quickEditForm" method="POST">
                @csrf
                <div class="modal-body">
                    <h6 id="quickEditUserName"></h6>
                    <div class="form-group">
                        <label>{{ __('Roles') }}</label>
                        <div id="quickEditRoles">
                            @foreach($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                           name="roles[]" value="{{ $role->name }}"
                                           id="role_{{ $role->id }}">
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Actualizar') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<style>
    .table th {
        vertical-align: middle;
    }
    .select2-container {
        width: 100% !important;
    }
    .small-box .icon {
        top: -10px;
        right: 10px;
    }
</style>
@stop

@section('js')
<script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Gráfico de distribución de roles
    const roleData = @json($roleDistribution);
    new Chart(document.getElementById('roleDistributionChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(roleData),
            datasets: [{
                data: Object.values(roleData),
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Gráfico de distribución por unidades
    const unitData = @json($unitDistribution);
    new Chart(document.getElementById('unitDistributionChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(unitData),
            datasets: [{
                label: 'Usuarios',
                data: Object.values(unitData),
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

function quickEditUser(userId, userName) {
    document.getElementById('quickEditUserName').textContent = userName;
    document.getElementById('quickEditForm').action = `/admin/users/${userId}/sync-roles`;

    // Limpiar checkboxes
    document.querySelectorAll('#quickEditRoles input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });

    // Cargar roles actuales del usuario
    fetch(`/admin/users/${userId}/roles`)
        .then(response => response.json())
        .then(roles => {
            roles.forEach(role => {
                const checkbox = document.querySelector(`#quickEditRoles input[value="${role}"]`);
                if (checkbox) checkbox.checked = true;
            });
        });

    $('#quickEditModal').modal('show');
}

function exportMatrix() {
    window.location.href = '{{ route("admin.role-assignment.export") }}?' + new URLSearchParams(window.location.search);
}
</script>
@stop
